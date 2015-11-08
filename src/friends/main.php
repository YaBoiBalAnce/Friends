<?php
namespace friends;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\event\entity\EntityDamageEvent;

class main extends PluginBase implements Listener{
	public $request = array();
	public function onEnable(){
		$this->getLogger()->info("Loaded!");
		$this->getServer()->getPluginManager()->registerEvents($this ,$this);
		@mkdir($this->getDataFolder());
		@mkdir($this->getDataFolder()."players/");
	}
	//events
	public function onDamageByPlayer(EntityDamageEvent $ev){
		$cause = $ev->getCause();
		switch ($cause){
		case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
		$atkr = $ev->getDamager();
		$player = $ev->getEntity();
		if ($atkr instanceof Player and $player instanceof Player){
			if($this->isFriend($player, $atkr->getName())){
				$ev->setCancelled();
				$atkr->sendMessage("Cannot attack friend :(");
			}
		}
		break;
		}
	}
	
	public function onJoin(PlayerJoinEvent $ev){
		if (!file_exists($this->getDataFolder()."players/".$ev->getPlayer()->getName().".yml")){
			$config = new Config($this->getDataFolder()."players/".strtolower($ev->getPlayer()->getName()).".yml", Config::YAML);
			$config->set("friends", array());
			$config->save();
			echo "made config for ".$ev->getPlayer()->getName();
		}
	}
	//commands
	public function onCommand(CommandSender $sender,Command $command, $label,array $args){
		switch($command->getName()){
			case "friend":
			if ($sender instanceof Player){
			if (isset($args[0])){
				switch ($args[0]){
					case "add":
						if (isset($args[1])){
							$player = $this->getServer()->getPlayer($args[1]);
							if(!$player == null){
								$this->addRequest($player, $sender);
							}	else {
								$sender->sendMessage(TextFormat::RED."Player not found");
							}
						}
					break;
					case "remove":
						if (isset($args[1])){
							if ($this->removeFriend($sender, $args[1])){
								$sender->sendMessage("Friend removed");
							}else{
								$sender->sendMessage("Friend not found do /friend list \n to list your friends");
							}
						}else{
							$sender->sendMessage("Usage: /friend remove [name]");
						}
					break;
					case "list":
						$config = new Config($this->getDataFolder()."players/". strtolower($sender->getName()).".yml", Config::YAML);
						$array = $config->get("friends", []);
						$sender->sendMessage(TextFormat::GOLD.TextFormat::BOLD."Friends:");
						foreach ($array as $friendname){
							$sender->sendMessage(TextFormat::GREEN."* ".$friendname);
						}
					break;
				}
			}}else{
		$sender->sendMessage("Must use command in-game");
	}
			break;
			case "accept":
				echo var_dump($this->request);
				if (in_array($sender->getName(), $this->request)){
					//echo "added";
					foreach ($this->request as $target => $requestp){
						$target = $this->getServer()->getPlayer($target);
						$requestp = $this->getServer()->getPlayer($requestp);
						echo $target->getName().$requestp->getName();
						if ($requestp->getName() === $sender->getName()){
							//echo "yes";
							$this->addFriend($target, $requestp);
							$this->addFriend($requestp, $target);
						}
					}
				}
			break;
		}
	}
	
	//api
	public function addRequest(Player $target,Player $requestp){
		if (!$this->isFriend($requestp, $target->getName())){
		$requestp->sendMessage("Sent request to ".$target->getName());
		$this->request[$requestp->getName()] = $target->getName();
		$target->sendMessage(TextFormat::GREEN.$requestp->getName()." has requested you as a friend do /accept to accept or ignore to ignore");
		echo var_dump($this->request);
 		$task = new cancelrequest($this, $target, $requestp);
 		$this->getServer()->getScheduler()->scheduleDelayedTask($task, 20*30);
		}else{
			$requestp->sendMessage("That player is already your friend :)");
		}
	}
	
	public function addFriend(Player $player,Player $friend){
		$config = new Config($this->getDataFolder()."players/". strtolower($player->getName()).".yml", Config::YAML);
		$array = $config->get("friends", []);
		$array[] = $friend->getName();
		$config->set("friends", $array);
		$config->save();
		$player->sendMessage("Added ".$friend->getName()." as a friend!");
	}
	
	public function removeFriend(Player $player, $friendname){
		if ($this->isFriend($player, $friendname)){
			$config = new Config($this->getDataFolder()."players/". strtolower($player->getName()).".yml", Config::YAML);
			$array = $config->get("friends", []);
			$id = array_search($friendname, $array);
			unset($array[$id]);
			$config->set("friends", $array);
			$config->save();
			return true;
		}
		return false;
	}
	
	public function isFriend(Player $player, $isfriendname){
		$config = new Config($this->getDataFolder()."players/". strtolower($player->getName()).".yml", Config::YAML);
		$array = $config->get("friends", []);
		if (in_array($isfriendname, $array)){
			return true;
		}
		return false;
	}
	
}