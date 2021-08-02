<?php

namespace skh6075\costumeplus;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

final class CostumePlus extends PluginBase implements Listener{
	use SingletonTrait;

	protected function onLoad() : void{
		self::setInstance($this);
	}

	protected function onEnable() : void{
		if(!is_dir($this->getDataFolder() . "SaveSkins/")) mkdir($this->getDataFolder() . "SaveSkins/");
		if(!is_dir($this->getDataFolder() . "Models/")) mkdir($this->getDataFolder() . "Models/");

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/**
	 * @param PlayerJoinEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerJoinEvent(PlayerJoinEvent $event): void{
		$api = CostumeAPI::getInstance();
		$api->updateSaveSkin($event->getPlayer(), $this->getDataFolder() . "SaveSkins/" . strtolower($event->getPlayer()->getName()) . "temp.png");
		$api->updateSaveSkin($event->getPlayer(), $this->getDataFolder() . "SaveSkins/" . strtolower($event->getPlayer()->getName()) . ".png");
	}

	/**
	 * @param PlayerQuitEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerQuitEvent(PlayerQuitEvent $event): void{
		$player = $event->getPlayer();
		if(file_exists($file = $this->getDataFolder() . "SaveSkins/" . strtolower($player->getName()) . "temp.png")){
			unlink($file);
		}
		if(file_exists($file = $this->getDataFolder() . "SaveSkins/" . strtolower($player->getName()) . ".png")){
			unlink($file);
		}
	}
}