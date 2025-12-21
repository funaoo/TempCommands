<?php

declare(strict_types=1);

namespace Funaoo\TempPermissions\manager;

use pocketmine\plugin\Plugin;
use SQLite3;

class DatabaseManager {

    public function __construct(
        private Plugin $plugin,
        private ?SQLite3 $database = null
    ) {}

    public function initialize(): void {
        if (!is_dir($this->plugin->getDataFolder())) {
            mkdir($this->plugin->getDataFolder(), 0777, true);
        }

        $this->database = new SQLite3($this->plugin->getDataFolder() . "permissions.db");
        $this->createTables();
    }

    private function createTables(): void {
        $this->database->exec("CREATE TABLE IF NOT EXISTS temp_permissions (
            player_name TEXT NOT NULL,
            permission TEXT NOT NULL,
            expiry_time INTEGER,
            PRIMARY KEY (player_name, permission)
        )");
    }

    public function savePermission(string $playerName, string $permission, ?int $expiryTime): void {
        $stmt = $this->database->prepare("INSERT OR REPLACE INTO temp_permissions (player_name, permission, expiry_time) VALUES (:player, :permission, :expiry)");
        $stmt->bindValue(":player", $playerName, SQLITE3_TEXT);
        $stmt->bindValue(":permission", $permission, SQLITE3_TEXT);
        $stmt->bindValue(":expiry", $expiryTime, $expiryTime === null ? SQLITE3_NULL : SQLITE3_INTEGER);
        $stmt->execute();
    }

    public function getPermissions(string $playerName): array {
        $stmt = $this->database->prepare("SELECT permission, expiry_time FROM temp_permissions WHERE player_name = :player");
        $stmt->bindValue(":player", $playerName, SQLITE3_TEXT);

        $result = $stmt->execute();
        $permissions = [];

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $permissions[] = [
                "permission" => $row["permission"],
                "expiry_time" => $row["expiry_time"]
            ];
        }

        return $permissions;
    }

    public function removePermission(string $playerName, string $permission): void {
        $stmt = $this->database->prepare("DELETE FROM temp_permissions WHERE player_name = :player AND permission = :permission");
        $stmt->bindValue(":player", $playerName, SQLITE3_TEXT);
        $stmt->bindValue(":permission", $permission, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function getExpiredPermissions(): array {
        $stmt = $this->database->prepare("SELECT player_name, permission FROM temp_permissions WHERE expiry_time IS NOT NULL AND expiry_time < :current");
        $stmt->bindValue(":current", time(), SQLITE3_INTEGER);

        $result = $stmt->execute();
        $expired = [];

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $expired[] = [
                "player_name" => $row["player_name"],
                "permission" => $row["permission"]
            ];
        }

        return $expired;
    }

    public function close(): void {
        $this->database->close();
    }
}