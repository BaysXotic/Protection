<?php

declare(strict_types=1);

namespace Terpz710\ProtectionPlus;

use pocketmine\plugin\PluginBase;
use Terpz710\ProtectionPlus\Command\InteractCommand;
use Terpz710\ProtectionPlus\Command\PvPCommand;
use Terpz710\ProtectionPlus\Command\ProtectionCommand;

class Main extends PluginBase {

    public function onEnable(): void {
        $this->getServer()->getCommandMap()->register("interact", new InteractCommand($this));
        $this->getServer()->getCommandMap()->register("pvp", new PvPCommand($this));
        $this->getServer()->getCommandMap()->register("protection", new ProtectionCommand($this));
    }
}
