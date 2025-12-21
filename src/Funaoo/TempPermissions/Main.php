<?php

declare(strict_types=1);

namespace Funaoo\TempPermissions;

use Funaoo\TempPermissions\command\TempPermCommand;
use Funaoo\TempPermissions\listener\VoucherListener;
use Funaoo\TempPermissions\manager\DatabaseManager;
use Funaoo\TempPermissions\manager\PermissionManager;
use Funaoo\TempPermissions\manager\VoucherManager;
use Funaoo\TempPermissions\task\PermissionExpiryTask;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase {

    protected function onEnable(): void {
        $this->saveDefaultConfig();

        $database = new DatabaseManager($this);
        $database->initialize();

        $permissionManager = new PermissionManager($this, $database);
        $voucherManager = new VoucherManager();

        $this->getServer()->getCommandMap()->register("tc", new TempPermCommand($this, $permissionManager, $voucherManager));
        $this->getServer()->getPluginManager()->registerEvents(new VoucherListener($permissionManager, $voucherManager), $this);

        $this->getScheduler()->scheduleRepeatingTask(new PermissionExpiryTask($permissionManager), 20);

        $this->getLogger()->info("TempPermissions ha sido activado correctamente");
    }

    protected function onDisable(): void {
        $this->getLogger()->info("TempPermissions ha sido desactivado correctamente");
    }
}