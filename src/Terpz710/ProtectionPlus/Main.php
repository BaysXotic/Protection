<?php

declare(strict_types=1);

namespace Terpz710\ProtectionPlus;

use pocketmine\plugin\PluginBase;
use Terpz710\ProtectionPlus\Command\ProtectCommand;
use Terpz710\ProtectionPlus\Command\InteractCommand; // Add this line to import the InteractCommand class

class Main extends PluginBase {

    public function onEnable(): void {
        $this->getServer()->getCommandMap()->register("protection", new ProtectCommand($this));
        $this->getServer()->getCommandMap()->register("interact", new InteractCommand($this)); // Register the InteractCommand
    }
}
