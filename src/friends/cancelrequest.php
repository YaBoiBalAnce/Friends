<?php
namespace friends;

use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;
use pocketmine\Player;
class allowtalk extends PluginTask{
	private $target;
	private $requestp;
	public function __construct(Plugin $owner, Player $target,Player $requestp){
		parent::__construct($owner);
		$this->target = $target;
		$this->requestp = $requestp;
	}
	
	public function onRun($currentTick){
		//cancel
	}
}