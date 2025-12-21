<?php

declare(strict_types=1);

namespace Funaoo\TempPermissions\manager;

use pocketmine\permission\PermissionAttachment;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

class PermissionManager {

    private array $attachments = [];

    public function __construct(
        private Plugin $plugin,
        private DatabaseManager $database
    ) {}

    public function givePermission(Player $player, string $permission, ?int $duration): void {
        $expiryTime = $duration !== null ? time() + $duration : null;

        $this->database->savePermission($player->getName(), $permission, $expiryTime);

        if (!isset($this->attachments[$player->getName()])) {
            $this->attachments[$player->getName()] = $player->addAttachment($this->plugin);
        }

        $this->attachments[$player->getName()]->setPermission($permission, true);
    }

    public function removePermission(Player $player, string $permission): void {
        $this->database->removePermission($player->getName(), $permission);

        if (isset($this->attachments[$player->getName()])) {
            $this->attachments[$player->getName()]->unsetPermission($permission);
        }
    }

    public function loadPlayerPermissions(Player $player): void {
        $permissions = $this->database->getPermissions($player->getName());

        if (empty($permissions)) {
            return;
        }

        if (!isset($this->attachments[$player->getName()])) {
            $this->attachments[$player->getName()] = $player->addAttachment($this->plugin);
        }

        foreach ($permissions as $data) {
            if ($data["expiry_time"] === null || $data["expiry_time"] > time()) {
                $this->attachments[$player->getName()]->setPermission($data["permission"], true);
            } else {
                $this->database->removePermission($player->getName(), $data["permission"]);
            }
        }
    }

    public function checkExpiredPermissions(): void {
        $expired = $this->database->getExpiredPermissions();

        foreach ($expired as $data) {
            $this->database->removePermission($data["player_name"], $data["permission"]);

            $player = $this->plugin->getServer()->getPlayerExact($data["player_name"]);
            if ($player !== null && isset($this->attachments[$player->getName()])) {
                $this->attachments[$player->getName()]->unsetPermission($data["permission"]);
            }
        }
    }

    public function unloadPlayer(Player $player): void {
        if (isset($this->attachments[$player->getName()])) {
            unset($this->attachments[$player->getName()]);
        }
    }
}