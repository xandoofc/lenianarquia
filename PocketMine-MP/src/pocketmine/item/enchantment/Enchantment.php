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

namespace pocketmine\item\enchantment;

use pocketmine\event\entity\EntityDamageEvent;

/**
 * Manages enchantment type data.
 */
class Enchantment{

	const PROTECTION = 0;
	const FIRE_PROTECTION = 1;
	const FEATHER_FALLING = 2;
	const BLAST_PROTECTION = 3;
	const PROJECTILE_PROTECTION = 4;
	const THORNS = 5;
	const RESPIRATION = 6;
	const DEPTH_STRIDER = 7;
	const AQUA_AFFINITY = 8;
	const SHARPNESS = 9;
	const SMITE = 10;
	const BANE_OF_ARTHROPODS = 11;
	const KNOCKBACK = 12;
	const FIRE_ASPECT = 13;
	const LOOTING = 14;
	const EFFICIENCY = 15;
	const SILK_TOUCH = 16;
	const UNBREAKING = 17;
	const FORTUNE = 18;
	const POWER = 19;
	const PUNCH = 20;
	const FLAME = 21;
	const INFINITY = 22;
	const LUCK_OF_THE_SEA = 23;
	const LURE = 24;
	const FROST_WALKER = 25;
	const MENDING = 26;

	const RARITY_COMMON = 10;
	const RARITY_UNCOMMON = 5;
	const RARITY_RARE = 2;
	const RARITY_MYTHIC = 1;

	const SLOT_NONE = 0x0;
	const SLOT_ALL = 0x7fff;
	const SLOT_ARMOR = self::SLOT_HEAD | self::SLOT_TORSO | self::SLOT_LEGS | self::SLOT_FEET;
	const SLOT_HEAD = 0x1;
	const SLOT_TORSO = 0x2;
	const SLOT_LEGS = 0x4;
	const SLOT_FEET = 0x8;
	const SLOT_SWORD = 0x10;
	const SLOT_BOW = 0x20;
	const SLOT_TOOL = self::SLOT_HOE | self::SLOT_SHEARS | self::SLOT_FLINT_AND_STEEL;
	const SLOT_HOE = 0x40;
	const SLOT_SHEARS = 0x80;
	const SLOT_FLINT_AND_STEEL = 0x100;
	const SLOT_DIG = self::SLOT_AXE | self::SLOT_PICKAXE | self::SLOT_SHOVEL;
	const SLOT_AXE = 0x200;
	const SLOT_PICKAXE = 0x400;
	const SLOT_SHOVEL = 0x800;
	const SLOT_FISHING_ROD = 0x1000;
	const SLOT_CARROT_STICK = 0x2000;
	const SLOT_ELYTRA = 0x4000;

	/** @var Enchantment[] */
	protected static $enchantments;

	public static function init(){
		self::$enchantments = new \SplFixedArray(256);

		self::registerEnchantment(new ProtectionEnchantment(self::PROTECTION, "%enchant.protect.all", self::RARITY_COMMON, self::SLOT_ARMOR, 4, 0.75, \null));
		self::registerEnchantment(new ProtectionEnchantment(self::FIRE_PROTECTION, "%enchantment.protect.fire", self::RARITY_UNCOMMON, self::SLOT_ARMOR, 4, 1.25, [
			EntityDamageEvent::CAUSE_FIRE,
			EntityDamageEvent::CAUSE_FIRE_TICK,
			EntityDamageEvent::CAUSE_LAVA
			//TODO: check fireballs
		]));
		self::registerEnchantment(new ProtectionEnchantment(self::FEATHER_FALLING, "%enchantment.protect.fall", self::RARITY_UNCOMMON, self::SLOT_FEET, 4, 2.5, [
			EntityDamageEvent::CAUSE_FALL
		]));
		self::registerEnchantment(new ProtectionEnchantment(self::BLAST_PROTECTION, "%enchantment.protect.explosion", self::RARITY_RARE, self::SLOT_ARMOR, 4, 1.5, [
			EntityDamageEvent::CAUSE_BLOCK_EXPLOSION,
			EntityDamageEvent::CAUSE_ENTITY_EXPLOSION
		]));
		self::registerEnchantment(new ProtectionEnchantment(self::PROJECTILE_PROTECTION, "%enchantment.protect.projectile", self::RARITY_UNCOMMON, self::SLOT_ARMOR, 4, 1.5, [
			EntityDamageEvent::CAUSE_PROJECTILE
		]));

		self::registerEnchantment(new Enchantment(self::RESPIRATION, "%enchantment.oxygen", self::RARITY_RARE, self::SLOT_HEAD, 3));

		self::registerEnchantment(new Enchantment(self::EFFICIENCY, "%enchantment.digging", self::RARITY_COMMON, self::SLOT_DIG | self::SLOT_SHEARS, 5));
		self::registerEnchantment(new Enchantment(self::SILK_TOUCH, "%enchantment.untouching", self::RARITY_MYTHIC, self::SLOT_DIG | self::SLOT_SHEARS, 1));
		self::registerEnchantment(new Enchantment(self::UNBREAKING, "%enchantment.durability", self::RARITY_UNCOMMON, self::SLOT_ALL, 3)); //TODO: item type flags need to be split up
	}

	/**
	 * Registers an enchantment type.
	 *
	 * @param Enchantment $enchantment
	 */
	public static function registerEnchantment(Enchantment $enchantment) : void{
		self::$enchantments[$enchantment->getId()] = clone $enchantment;
	}

	/**
	 * @param int $id
	 *
	 * @return Enchantment|null
	 */
	public static function getEnchantment(int $id){
		return self::$enchantments[$id] ?? \null;
	}

	/**
	 * @param string $name
	 *
	 * @return Enchantment|null
	 */
	public static function getEnchantmentByName(string $name){
		$const = Enchantment::class . "::" . \strtoupper($name);
		if(\defined($const)){
			return self::getEnchantment(\constant($const));
		}
		return \null;
	}

	/** @var int */
	private $id;
	/** @var string */
	private $name;
	/** @var int */
	private $rarity;
	/** @var int */
	private $slot;
	/** @var int */
	private $maxLevel;

	/**
	 * @param int    $id
	 * @param string $name
	 * @param int    $rarity
	 * @param int    $slot
	 * @param int    $maxLevel
	 */
	public function __construct(int $id, string $name, int $rarity, int $slot, int $maxLevel){
		$this->id = $id;
		$this->name = $name;
		$this->rarity = $rarity;
		$this->slot = $slot;
		$this->maxLevel = $maxLevel;
	}

	/**
	 * Returns the ID of this enchantment as per Minecraft PE
	 * @return int
	 */
	public function getId() : int{
		return $this->id;
	}

	/**
	 * Returns a translation key for this enchantment's name.
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}

	/**
	 * Returns an int constant indicating how rare this enchantment type is.
	 * @return int
	 */
	public function getRarity() : int{
		return $this->rarity;
	}

	/**
	 * Returns an int with bitflags set to indicate what item types this enchantment can apply to.
	 * @return int
	 */
	public function getSlot() : int{
		return $this->slot;
	}

	/**
	 * Returns whether this enchantment can apply to the specified item type.
	 * @param int $slot
	 *
	 * @return bool
	 */
	public function hasSlot(int $slot) : bool{
		return ($this->slot & $slot) > 0;
	}

	/**
	 * Returns the maximum level of this enchantment that can be found on an enchantment table.
	 * @return int
	 */
	public function getMaxLevel() : int{
		return $this->maxLevel;
	}

	//TODO: methods for min/max XP cost bounds based on enchantment level (not needed yet - enchanting is client-side)
}
