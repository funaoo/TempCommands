<?php

declare(strict_types=1);

namespace Funaoo\TempPermissions\manager;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat as TF;

class VoucherManager {

    public function createVoucher(string $permission, ?int $duration): Item {
        $paper = VanillaItems::PAPER();
        $paper->setCustomName(TF::DARK_PURPLE . "Voucher de Permiso");

        $isPermanent = $duration === null;

        $lore = [
            TF::GRAY . "Permiso: " . TF::DARK_PURPLE . $permission,
            TF::GRAY . "Duracion: " . TF::DARK_PURPLE . ($isPermanent ? "Permanente" : $this->formatDuration($duration)),
            "",
            TF::GRAY . "Click derecho para activar"
        ];

        $paper->setLore($lore);

        $nbt = $paper->getNamedTag();
        $nbt->setString("temp_permission", $permission);

        if (!$isPermanent) {
            $nbt->setInt("temp_duration", $duration);
        }

        $paper->setNamedTag($nbt);

        return $paper;
    }

    public function isVoucher(Item $item): bool {
        return $item->getNamedTag()->getTag("temp_permission") !== null;
    }

    public function getPermission(Item $item): ?string {
        $tag = $item->getNamedTag()->getTag("temp_permission");
        return $tag !== null ? $tag->getValue() : null;
    }

    public function getDuration(Item $item): ?int {
        $tag = $item->getNamedTag()->getTag("temp_duration");
        return $tag !== null ? $tag->getValue() : null;
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