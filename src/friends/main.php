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
class main extends PluginBase implements Listener{
	private $request = array();
	public function onEnable(){
		$this->getLogger()->info("Loaded!");
		$this->getServer()->getPluginManager()->registerEvents($this ,$this);
		@mkdir($this->getDataFolder());
		@mkdir($this->getDataFolder()."players/");
	}
	
	public function onJoin(PlayerJoinEvent $ev){
		if (!file_exists($this->getDataFolder()."players/".$ev->getPlayer()->getName().".yml")){
			$config = new Config($this->getDataFolder()."players/".strtolower($ev->getPlayer()->getName()).".yml", Config::YAML);
			$config->set("friends", array());
			$config->save();
			echo "made config for ".$ev->getPlayer()->getName();
		}
	}
	
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
				}
			}}else{
		$sender->sendMessage("Must use command in-game");
	}
			break;
			case "accept":
				echo var_dump($this->request);
				if (in_array($sender->getName(), $this->request)){
					echo "added";
					foreach ($this->request as $target => $requestp){
						$target = $this->getServer()->getPlayer($target);
						$requestp = $this->getServer()->getPlayer($requestp);
						echo $target->getName().$requestp->getName();
						if ($requestp->getName() === $sender->getName()){
							echo "yes";
							$this->addFriend($target, $requestp);
							$this->addFriend($requestp, $target);
						}
					}
				}
			break;
		}
	}
	public function addRequest(Player $target,Player $requestp){
		$requestp->sendMessage("Sent request to ".$target->getName());
		$this->request[$requestp->getName()] = $target->getName();
		$target->sendMessage($requestp->getName()." has requested you as a friend do /accept to accept or ignore");
		echo var_dump($this->request);
// 		$task = new $this->removeRequest($target, $requestp);
// 		$this->getServer()->getScheduler()->scheduleDelayedTask($task, 20*30);
	}
	
	public function removeRequest(Player $target,Player $requestp){
		$requestp->sendMessage($target->getName()." did not accept your friend request :(");
		unset($this->request[$requestp]);
	}
	
	public function addFriend(Player $player,Player $friend){
		$config = new Config($this->getDataFolder()."players/". strtolower($player->getName()).".yml", Config::YAML);
		$array = $config->get("friends", []);
		$array[] = $friend->getName();
		$config->set("friends", $array);
		$config->save();
		echo "added friend ";
	}
	
}