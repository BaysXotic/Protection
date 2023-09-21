<?php

declare(strict_types=1);

namespace Terpz710\ProtectionPlus\Command;

use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockInteractEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class InteractCommand extends Command implements Listener {

    private $interactionActive = [];

    public function __construct(PluginBase $plugin) {
        parent::__construct("interact", "Toggle block interaction");
        $this->setPermission("protectionplus.interact");
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        // Your execute method code here
    }

    /**
     * @param BlockInteractEvent $event
     * @priority HIGHEST
     */
    public function onInteract(BlockInteractEvent $event): void {
        $player = $event->getPlayer();
        $world = $player->getWorld()->getFolderName();
        $block = $event->getBlock();

        if (isset($this->interactionActive[$world])) {
            // Define a list of block types that should be allowed
            $allowedBlockTypes = [
                Block::CRAFTING_TABLE,  // Allow interaction with crafting tables
                Block::CHEST,           // Allow interaction with chests
                Block::BREWING_STAND,   // Allow interaction with brewing stands
                // Add more block types as needed
            ];

            // Check if the block's block type is in the list of allowed block types
            if (!in_array($block->getId(), $allowedBlockTypes)) {
                // Block type is not allowed, cancel interaction
                $player->sendMessage("Block interaction is active in this world. You cannot interact with this block.");
                $event->setCancelled();
            }
        }
    }
}
