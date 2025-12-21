<?php

declare(strict_types=1);

namespace Funaoo\TempPermissions\listener;

use Funaoo\TempPermissions\manager\PermissionManager;
use Funaoo\TempPermissions\manager\VoucherManager;
use Funaoo\TempPermissions\utils\MessageUtil;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class VoucherListener implements Listener {

    public function __construct(
        private PermissionManager $permissionManager,
        private VoucherManager $voucherManager
    ) {}

    public function onJoin(PlayerJoinEvent $event): void {
        $this->permissionManager->loadPlayerPermissions($event->getPlayer());
    }

    public function onQuit(PlayerQuitEvent $event): void {
        $this->permissionManager->unloadPlayer($event->getPlayer());
    }

    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if (!$this->voucherManager->isVoucher($item)) {
            return;
        }

        $event->cancel();

        $permission = $this->voucherManager->getPermission($item);
        $duration = $this->voucherManager->getDuration($item);

        if ($permission === null) {
            return;
        }

        $this->permissionManager->givePermission($player, $permission, $duration);

        if ($duration === null) {
            $player->sendMessage(MessageUtil::success("Permiso activado permanentemente"));
            $player->sendMessage(MessageUtil::info("Permiso: " . $permission));
        } else {
            $player->sendMessage(MessageUtil::success("Permiso activado temporalmente"));
            $player->sendMessage(MessageUtil::info("Permiso: " . $permission));
            $player->sendMessage(MessageUtil::info("Duracion: " . $this->formatDuration($duration)));
        }

        $item->pop();
        $player->getInventory()->setItemInHand($item);
    }

    private function formatDuration(int $seconds): string {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($days > 0) {
            return sprintf("%dd %dh %dm %ds", $days, $hours, $minutes, $secs);
        } elseif ($hours > 0) {
            return sprintf("%dh %dm %ds", $hours, $minutes, $secs);
        } elseif ($minutes > 0) {
            return sprintf("%dm %ds", $minutes, $secs);
        } else {
            return sprintf("%ds", $secs);
        }
    }
}