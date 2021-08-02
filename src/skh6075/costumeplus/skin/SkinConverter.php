<?php

namespace skh6075\costumeplus\skin;

final class SkinConverter{

	public static function skinDataToImage(string $skinData) {
		$size = strlen($skinData);
		SkinData::validateSize($size);

		$width = SkinData::SKIN_WIDTH_MAP[$size];
		$height = SkinData::SKIN_HEIGHT_MAP[$size];
		$skinPos = 0;
		$image = imagecreatetruecolor($width, $height);

		imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
		for($y = 0; $y < $height; $y++){
			for($x = 0; $x < $width; $x++){
				$r = ord($skinData[$skinPos]);
				$skinPos++;
				$g = ord($skinData[$skinPos]);
				$skinPos++;
				$b = ord($skinData[$skinPos]);
				$skinPos++;
				$a = 127 - intdiv(ord($skinData[$skinPos]), 2);
				$skinPos++;
				$col = imagecolorallocatealpha($image, $r, $g, $b, $a);
				imagesetpixel($image, $x, $y, $col);
			}
		}
		imagesavealpha($image, true);
		return $image;
	}

	public static function skinDataToImageSave(string $skinData, string $savePath): void{
		$image = self::skinDataToImage($skinData);
		imagepng($image, $savePath);
		imagedestroy($image);
	}
}