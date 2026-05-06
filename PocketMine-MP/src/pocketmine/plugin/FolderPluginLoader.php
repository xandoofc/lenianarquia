<?php

/*
 * FolderPluginLoader - compatível com PocketMine-MP API 3.0.0-ALPHA11
 * Carrega plugins que estão em formato de pasta (não-phar) na pasta plugins/
 */

declare(strict_types=1);

namespace pocketmine\plugin;

use pocketmine\Server;
use pocketmine\utils\MainLogger;
use pocketmine\utils\TextFormat;

class FolderPluginLoader implements PluginLoader {

	/** @var Server */
	private $server;

	public function __construct(Server $server){
		$this->server = $server;
	}

	public function loadPlugin(string $file) : ?Plugin{
		if(is_dir($file) and file_exists($file . "/plugin.yml") and file_exists($file . "/src")){
			if(($description = $this->getPluginDescription($file)) instanceof PluginDescription){
				MainLogger::getLogger()->info(TextFormat::LIGHT_PURPLE . "Loading (folder) " . $description->getFullName());
				$dataFolder = dirname($file) . DIRECTORY_SEPARATOR . $description->getName();

				$className = $description->getMain();
				$this->server->getLoader()->addPath($file . "/src");

				if(class_exists($className, true)){
					$plugin = new $className();
					$plugin->init($this, $this->server, $description, $dataFolder, $file);
					$plugin->onLoad();
					return $plugin;
				} else {
					MainLogger::getLogger()->warning("FolderPluginLoader: main class '{$className}' not found for plugin " . $description->getName());
				}
			} else {
				MainLogger::getLogger()->debug("FolderPluginLoader: getPluginDescription returned null for " . $file);
			}
		} else {
			if(is_dir($file)){
				$hasYml = file_exists($file . "/plugin.yml") ? "yes" : "no";
				$hasSrc = file_exists($file . "/src") ? "yes" : "no";
				MainLogger::getLogger()->debug("FolderPluginLoader: dir={$file} plugin.yml={$hasYml} src={$hasSrc}");
			}
		}

		return null;
	}

	public function getPluginDescription(string $file) : ?PluginDescription{
		if(is_dir($file) and file_exists($file . "/plugin.yml")){
			$yaml = file_get_contents($file . "/plugin.yml");
			if($yaml !== false and $yaml !== ""){
				try{
					return new PluginDescription($yaml);
				}catch(\Exception $e){
					MainLogger::getLogger()->warning("FolderPluginLoader: invalid plugin.yml in " . $file . ": " . $e->getMessage());
				}
			}
		}

		return null;
	}

	public function getPluginFilters() : string{
		return '/^[^\\.]/';
	}

	public function enablePlugin(Plugin $plugin) : void{
		if($plugin instanceof PluginBase and !$plugin->isEnabled()){
			MainLogger::getLogger()->info(TextFormat::GREEN . "Enabling " . $plugin->getDescription()->getFullName());
			$plugin->setEnabled(true);
		}
	}

	public function disablePlugin(Plugin $plugin) : void{
		if($plugin instanceof PluginBase and $plugin->isEnabled()){
			MainLogger::getLogger()->info(TextFormat::RED . "Disabling " . $plugin->getDescription()->getFullName());
			$plugin->setEnabled(false);
		}
	}
}
