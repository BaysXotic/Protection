<?php

declare(strict_types=1);

namespace Terpz710\ProtectionPlus\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class InteractionCommand extends Command implements Listener {

    public function __construct(PluginBase $plugin) {
        parent::__construct("interaction", "Toggle block interaction");
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            $sender->sendMessage("Interaction blocking is active.");
        } else {
            $sender->sendMessage("This command can only be used in-game");
        }
        return true;
    }

    /**
     * @param PlayerInteractEvent $event
     * @priority HIGHEST
     */
    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        
        if ($player->getInventory()->getItemInHand()->getId() !== 0) {
            $player->sendMessage("Interaction blocking is active. You cannot use items.");
            $event->cancel();
        }
    }
}
