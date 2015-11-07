<?php
namespace friends;

use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class cancelrequest extends PluginTask{
	private $target;
	private $requestp;
	public function __construct(Plugin $owner, Player $target,Player $requestp){
		parent::__construct($owner);
		$this->target = $target;
		$this->requestp = $requestp;
	}
	
	public function onRun($currentTick){
         if (in_array($this->target, $this->getOwner()->request)){
         	foreach ($this->getOwner()->request as $requestp => $target){
         		if ($requestp === $this->requestp){
         			unset($this->getOwner()->request[$requestp]);
         			$requestp = $this->getOwner()->getServer()->getPlayer($requestp);
         			$requestp->sendMessage(TextFormat::RED."Player ".$target." did not accept your friend request... :(");
         		}
         	}
         }
	}
}