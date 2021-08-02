<?php

namespace skh6075\costumeplus\skin;

final class SkinData{

	public const ACCEPTED_SKIN_SIZES = [
		64 * 32 * 4,
		64 * 64 * 4,
		128 * 128 * 4
	];

	public const SKIN_WIDTH_MAP = [
		64 * 32 * 4 => 64,
		64 * 64 * 4 => 64,
		128 * 128 * 4 => 128
	];

	public const SKIN_HEIGHT_MAP = [
		64 * 32 * 4 => 32,
		64 * 64 * 4 => 64,
		128 * 128 * 4 => 128
	];

	public static function validateSize(int $size) {
		if(!in_array($size, self::ACCEPTED_SKIN_SIZES)){
			throw new \Exception("Invalid skin size");
		}
	}
}