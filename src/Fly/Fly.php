<?php

namespace Fly;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\item\Item;

use pocketmine\scheduler\Task;
use pocketmine\scheduler\PluginTask;

use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;

use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\ClickSound;
use pocketmine\level\sound\AnvilUseSound;

use pocketmine\plugin\MethodEventExecutor;
use pocketmine\plugin\EventExecutor;

use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacketV2;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;


use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\level\particle\Particle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\math\Vector3;


class Fly extends PluginBase implements Listener{

    public function onEnable() {
        $this->getLogger()->info("IQ NETWORK Lobby Activited");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->reloadConfig();
    }

    public function onJoinPlayer(PlayerJoinEvent $event){

        $player = $event->getPlayer();	
        $name = $event->getPlayer()->getName();
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
     
        $event->setJoinMessage("§7[§a+§7]§a$name");
         
         $player->sendMessage("§7====================");
         $player->sendMessage(" §l §4IQ§b-§7NetWork");
         $player->sendMessage("§f Code by 0v3r");
         $player->sendMessage("§7====================");
         
         $player->addTitle("§aBienvenu(e) sur");
         $player->addSubTitle("§l§4 IQ§b-§7NetWork");
         
        $player->setFood("20");
        $player->setMaxHealth("20");
        $player->setHealth("20");
        $player->setGamemode(0);
        $player->getlevel()->addSound(new AnvilUseSound($player));
     
     }

     public function onQuitPlayer(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        $name = $event->getPlayer()->getName();
            $event->setQuitMessage("§7[§c-§7]§c$name");
    
    
    }
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args) : bool{
        if (!$sender->hasPermission("fly.command")) {
            $sender->sendMessage(TextFormat::RED.TextFormat::ITALIC."Vous n'avez pas la permission de faire cette commande");
            return false;
        }
        $sender = $sender;
        if(isset($args[0])) {
            $player = $this->getServer()->getPlayer(args[0]);
            if ($player instanceof Player and $sender->hasPermission("fly.command.others")) {
                $sender = $player;
            }
        }
        $world = $sender->getLevel()->getFolderName();
        if (!$this->flyAllowed($world)) {
            $sender->sendMessage(TextFormat::RED.TextFormat::ITALIC."Vous ne pouvez pas Fly dans ce monde");
            return false;
        }
        if (!$sender->isSurvival()) {
            $sender->sendMessage(TextFormat::RED.TextFormat::ITALIC."Vous ne pouvez pas utiliser le fly en creatif");
            return false;
        }
        if (!$sender->getAllowFlight()) {
            $sender->addTitle(TextFormat::AQUA."Fly", TextFormat::RED."Désactivé", 20, 10, 20); 
            $sender->setFlying(false);
            $sender->setAllowFlight(false);
        }else {
            $sender->addTitle(TextFormat::AQUA."Fly", TextFormat::GREEN."Activé", 20, 10, 20);
            $sender->setAllowFlight(true);
        }
        return true;
    }

    public function flyAllowed($world) : bool{
        $config = new Config($this->getDataFolder()."config.yml", Config::YAML);
        $worlds = $config->get("worlds", []);
        return in_array($world, $worlds);
    }
    public function onChange(EntityLevelChangeEvent $event) {
        $player = $event->getEntity();
        if ($player instanceof Player) {
            $world = $event->getTarget()->getFolderName();
            if (!$this->flyAllowed($world) and $player->getAllowFlight() and $player->isSurvival()) {
            $player->addTitle(TextFormat::AQUA."Fly", TextFormat::RED.TextFormat::BOLD."Désactivé", 20, 10, 20);
            $player->setFlying(false);
            $player->setAllowedFlight(false);
            }
        }        
    }
}
