<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\entity\Effect;
use pocketmine\entity\Living;

class Potion extends Item implements Consumable{

	const WATER = 0;
	const MUNDANE = 1;
	const LONG_MUNDANE = 2;
	const THICK = 3;
	const AWKWARD = 4;
	const NIGHT_VISION = 5;
	const LONG_NIGHT_VISION = 6;
	const INVISIBILITY = 7;
	const LONG_INVISIBILITY = 8;
	const LEAPING = 9;
	const LONG_LEAPING = 10;
	const STRONG_LEAPING = 11;
	const FIRE_RESISTANCE = 12;
	const LONG_FIRE_RESISTANCE = 13;
	const SWIFTNESS = 14;
	const LONG_SWIFTNESS = 15;
	const STRONG_SWIFTNESS = 16;
	const SLOWNESS = 17;
	const LONG_SLOWNESS = 18;
	const WATER_BREATHING = 19;
	const LONG_WATER_BREATHING = 20;
	const HEALING = 21;
	const STRONG_HEALING = 22;
	const HARMING = 23;
	const STRONG_HARMING = 24;
	const POISON = 25;
	const LONG_POISON = 26;
	const STRONG_POISON = 27;
	const REGENERATION = 28;
	const LONG_REGENERATION = 29;
	const STRONG_REGENERATION = 30;
	const STRENGTH = 31;
	const LONG_STRENGTH = 32;
	const STRONG_STRENGTH = 33;
	const WEAKNESS = 34;
	const LONG_WEAKNESS = 35;
	const WITHER = 36;

	/**
	 * Returns a list of effects applied by potions with the specified ID.
	 *
	 * @param int $id
	 * @return Effect[]
	 *
	 * @throws \InvalidArgumentException if the potion type is unknown
	 */
	public static function getPotionEffectsById(int $id) : array{
		switch($id){
			case self::WATER:
			case self::MUNDANE:
			case self::LONG_MUNDANE:
			case self::THICK:
			case self::AWKWARD:
				return [];
			case self::NIGHT_VISION:
				return [
					Effect::getEffect(Effect::NIGHT_VISION)->setDuration(3600)
				];
			case self::LONG_NIGHT_VISION:
				return [
					Effect::getEffect(Effect::NIGHT_VISION)->setDuration(9600)
				];
			case self::INVISIBILITY:
				return [
					Effect::getEffect(Effect::INVISIBILITY)->setDuration(3600)
				];
			case self::LONG_INVISIBILITY:
				return [
					Effect::getEffect(Effect::INVISIBILITY)->setDuration(9600)
				];
			case self::LEAPING:
				return [
					Effect::getEffect(Effect::JUMP_BOOST)->setDuration(3600)
				];
			case self::LONG_LEAPING:
				return [
					Effect::getEffect(Effect::JUMP_BOOST)->setDuration(9600)
				];
			case self::STRONG_LEAPING:
				return [
					Effect::getEffect(Effect::JUMP_BOOST)->setDuration(1800)->setAmplifier(1)
				];
			case self::FIRE_RESISTANCE:
				return [
					Effect::getEffect(Effect::FIRE_RESISTANCE)->setDuration(3600)
				];
			case self::LONG_FIRE_RESISTANCE:
				return [
					Effect::getEffect(Effect::FIRE_RESISTANCE)->setDuration(9600)
				];
			case self::SWIFTNESS:
				return [
					Effect::getEffect(Effect::SPEED)->setDuration(3600)
				];
			case self::LONG_SWIFTNESS:
				return [
					Effect::getEffect(Effect::SPEED)->setDuration(9600)
				];
			case self::STRONG_SWIFTNESS:
				return [
					Effect::getEffect(Effect::SPEED)->setDuration(1800)->setAmplifier(1)
				];
			case self::SLOWNESS:
				return [
					Effect::getEffect(Effect::SLOWNESS)->setDuration(1800)
				];
			case self::LONG_SLOWNESS:
				return [
					Effect::getEffect(Effect::SLOWNESS)->setDuration(4800)
				];
			case self::WATER_BREATHING:
				return [
					Effect::getEffect(Effect::WATER_BREATHING)->setDuration(3600)
				];
			case self::LONG_WATER_BREATHING:
				return [
					Effect::getEffect(Effect::WATER_BREATHING)->setDuration(9600)
				];
			case self::HEALING:
				return [
					Effect::getEffect(Effect::INSTANT_HEALTH)
				];
			case self::STRONG_HEALING:
				return [
					Effect::getEffect(Effect::INSTANT_HEALTH)->setAmplifier(1)
				];
			case self::HARMING:
				return [
					Effect::getEffect(Effect::INSTANT_DAMAGE)
				];
			case self::STRONG_HARMING:
				return [
					Effect::getEffect(Effect::INSTANT_DAMAGE)->setAmplifier(1)
				];
			case self::POISON:
				return [
					Effect::getEffect(Effect::POISON)->setDuration(900)
				];
			case self::LONG_POISON:
				return [
					Effect::getEffect(Effect::POISON)->setDuration(2400)
				];
			case self::STRONG_POISON:
				return [
					Effect::getEffect(Effect::POISON)->setDuration(440)->setAmplifier(1)
				];
			case self::REGENERATION:
				return [
					Effect::getEffect(Effect::REGENERATION)->setDuration(900)
				];
			case self::LONG_REGENERATION:
				return [
					Effect::getEffect(Effect::REGENERATION)->setDuration(2400)
				];
			case self::STRONG_REGENERATION:
				return [
					Effect::getEffect(Effect::REGENERATION)->setDuration(440)->setAmplifier(1)
				];
			case self::STRENGTH:
				return [
					Effect::getEffect(Effect::STRENGTH)->setDuration(3600)
				];
			case self::LONG_STRENGTH:
				return [
					Effect::getEffect(Effect::STRENGTH)->setDuration(9600)
				];
			case self::STRONG_STRENGTH:
				return [
					Effect::getEffect(Effect::STRENGTH)->setDuration(1800)->setAmplifier(1)
				];
			case self::WEAKNESS:
				return [
					Effect::getEffect(Effect::WEAKNESS)->setDuration(1800)
				];
			case self::LONG_WEAKNESS:
				return [
					Effect::getEffect(Effect::WEAKNESS)->setDuration(4800)
				];
			case self::WITHER:
				return [
					Effect::getEffect(Effect::WITHER)->setDuration(800)->setAmplifier(1)
				];
		}

		throw new \InvalidArgumentException("Unknown potion type $id");
	}

	public function __construct(int $meta = 0){
		parent::__construct(self::POTION, $meta, "Potion");
	}

	public function getMaxStackSize() : int{
		return 1;
	}

	public function onConsume(Living $consumer){

	}

	public function getAdditionalEffects() : array{
		//TODO: check CustomPotionEffects NBT
		return self::getPotionEffectsById($this->meta);
	}

	public function getResidue(){
		return ItemFactory::get(Item::GLASS_BOTTLE);
	}
}
