<?php

declare(strict_types=1);

namespace Funaoo\TempPermissions\command;

use Funaoo\TempPermissions\Main;
use Funaoo\TempPermissions\manager\PermissionManager;
use Funaoo\TempPermissions\manager\VoucherManager;
use Funaoo\TempPermissions\utils\MessageUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TempPermCommand extends Command {

    public function __construct(
        private Main $plugin,
        private PermissionManager $permissionManager,
        private VoucherManager $voucherManager
    ) {
        parent::__construct("tc", "Crea un voucher de permiso temporal", "/tc <permiso> [tiempo]");
        $this->setPermission("temppermissions.create");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage(MessageUtil::error("Este comando solo puede ser usado por jugadores"));
            return false;
        }

        if (!$this->testPermission($sender)) {
            return false;
        }

        if (count($args) < 1) {
            $sender->sendMessage(MessageUtil::warning("Uso: /tc <permiso> [tiempo]"));
            $sender->sendMessage(MessageUtil::info("Ejemplo: /tc fly.use 30m"));
            $sender->sendMessage(MessageUtil::info("Formatos: 30s, 15m, 2h, 7d"));
            return false;
        }

        $permission = $args[0];
        $duration = isset($args[1]) ? $this->parseTime($args[1]) : null;

        if (isset($args[1]) && $duration === null) {
            $sender->sendMessage(MessageUtil::error("Formato de tiempo invalido. Usa: 30s, 15m, 2h, 7d"));
            return false;
        }

        $voucher = $this->voucherManager->createVoucher($permission, $duration);

        if ($sender->getInventory()->canAddItem($voucher)) {
            $sender->getInventory()->addItem($voucher);
            $sender->sendMessage(MessageUtil::success("Voucher creado correctamente"));
        } else {
            $sender->sendMessage(MessageUtil::error("No tienes espacio en el inventario"));
        }

        return true;
    }

    private function parseTime(string $time): ?int {
        if (!preg_match('/^(\d+)([smhd])$/', strtolower($time), $matches)) {
            return null;
        }

        return match($matches[2]) {
            's' => (int)$matches[1],
            'm' => (int)$matches[1] * 60,
            'h' => (int)$matches[1] * 3600,
            'd' => (int)$matches[1] * 86400,
            default => null
        };
    }
}