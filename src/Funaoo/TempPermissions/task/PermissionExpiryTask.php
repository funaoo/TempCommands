<?php

declare(strict_types=1);

namespace Funaoo\TempPermissions\task;

use Funaoo\TempPermissions\manager\PermissionManager;
use pocketmine\scheduler\Task;

class PermissionExpiryTask extends Task {

    public function __construct(
        private PermissionManager $permissionManager
    ) {}

    public function onRun(): void {
        $this->permissionManager->checkExpiredPermissions();
    }
}