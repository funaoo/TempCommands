<?php

declare(strict_types=1);

namespace Funaoo\TempPermissions\utils;

use pocketmine\utils\TextFormat as TF;

class MessageUtil {

    public static function prefix(): string {
        return TF::DARK_PURPLE . "TempPerms " . TF::DARK_GRAY . "» " . TF::RESET;
    }

    public static function error(string $message): string {
        return self::prefix() . TF::RED . $message;
    }

    public static function success(string $message): string {
        return self::prefix() . TF::GREEN . $message;
    }

    public static function info(string $message): string {
        return self::prefix() . TF::GRAY . $message;
    }

    public static function warning(string $message): string {
        return self::prefix() . TF::YELLOW . $message;
    }
}