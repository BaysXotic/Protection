<?php

declare(strict_types=1);

namespace Terpz710\ProtectionPlus;

use pocketmine\plugin\PluginBase;
use Terpz710\ProtectionPlus\Command\PvPCommand;
use Terpz710\ProtectionPlus\Command\ProtectCommand;

class Main extends PluginBase {

    public function onEnable(): void 
        $this->getServer()->getCommandMap()->register("pvp", new PvPCommand($this));
        $this->getServer()->getCommandMap()->register("protect", new ProtectCommand($this));
    }
}
