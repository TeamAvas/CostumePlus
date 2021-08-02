<?php

namespace skh6075\costumeplus;

use pocketmine\entity\Skin;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use skh6075\costumeplus\skin\SkinConverter;

final class CostumeAPI{
	use SingletonTrait;

	public function updateSaveSkin(Player $player, string $savePath): void{
		$skinData = $player->getSkin()->getSkinData();
		SkinConverter::skinDataToImageSave($skinData, $savePath);
	}

	private function imageFix(string $file, int $_width, int $_height, bool $access = false) {
		[$width, $height] = getimagesize($file);
		$resize = $width / $height;
		if($access){
			if($width > $height){
				$width = ceil($width - ($width * abs($resize - $_width / $_height)));
			}else{
				$height = ceil($height - ($height * abs($resize - $_width / $_height)));
			}
			$newWidth = $_width;
			$newHeight = $_height;
		}else{
			if($width / $height > $resize){
				$newWidth = $_height * $resize;
				$newHeight = $_height;
			}else{
				$newHeight = $_width / $resize;
				$newWidth = $_width;
			}
		}

		$src = imagecreatefrompng($file);
		$dst = imagecreatetruecolor($_width, $_height);
		imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
		imagealphablending($dst, false);
		imagesavealpha($dst, true);
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $_width, $_height);
		return $dst;
	}

	private function imageCheck(string $skinPath, string $fileName, array $size, Player $player): string{
		$loader = CostumePlus::getInstance();
		$img = imagecreatefrompng($skinPath);
		if($size[0] * $size[1] * $size[2] === 65536){
			$upFile = $this->imageFix($loader->getDataFolder() . "Models/" . $fileName . ".png", 128, 128);
		}else{
			$upFile = $this->imageFix($loader->getDataFolder() . "Models/" . $fileName . ".png", 64, 64);
		}

		imagecolortransparent($upFile, imagecolorallocatealpha($upFile, 0, 0, 0, 127));
		imagealphablending($img, false);
		imagesavealpha($img, true);
		imagecopymerge($img, $upFile, 0, 0, 0, 0, $size[0], $size[1], 100);
		imagepng($img, $file = $loader->getDataFolder() . "SaveSkins/" . strtolower($player->getName()) . "temp.png");
		return $file;
	}

	public function onUpdatePlayerCostumeSkin(Player $player): void{
		$costume = ""; //TODO.. 코스튬 이름

		$loader = CostumePlus::getInstance();
		$skin = $player->getSkin();
		$path = $loader->getDataFolder() . "SaveSkins/" . strtolower($player->getName()) . "temp.png";

		$size = getimagesize($path);
		$path = $this->imageCheck($path, $costume, [$size[0], $size[1], 4], $player);
		$img = imagecreatefrompng($path);
		$bytes = "";
		for($y = 0; $y < $size[1]; $y++){
			for($x = 0; $x < $size[0]; $x++){
				$colorat = imagecolorat($img, $x, $y);
				$a = ((~((int) ($colorat >> 24))) << 1) & 0xff;
				$r = ($colorat >> 16) & 0xff;
				$g = ($colorat >> 8) & 0xff;
				$b = $colorat & 0xff;
				$bytes .= chr($r) . chr($g) . chr($b) . chr($a);
			}
		}
		imagedestroy($img);
		$player->setSkin(new Skin($skin->getSkinId(), $bytes, "", "geomerty.{$costume}", file_get_contents($loader->getDataFolder() . "Models/" . $costume . ".json")));
		$player->sendSkin(Server::getInstance()->getOnlinePlayers());
	}
}