<?php

/**
 * @name    leni login
 * @version 1.4.0
 * @main SimpleLogin\Main
 * @api 3.0.0-ALPHA11
 */

namespace SimpleLogin;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\form\CustomForm;
use pocketmine\form\element\Input;
use pocketmine\form\element\Label;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;

class Main extends PluginBase implements Listener {

    /** @var Config */
    private $db;
    /** @var string[] */
    private $authenticated = [];

    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->db = new Config($this->getDataFolder() . "players.json", Config::JSON);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("SimpleLogin v1.4.0 (Coords) habilitado!");
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        
        // Ativar coordenadas
        $this->showCoordinates($player);
        
        // Pequeno atraso para garantir que o cliente esteja pronto para receber o form
        $this->getServer()->getScheduler()->scheduleDelayedTask(new class($this, $player) extends \pocketmine\scheduler\Task {
            private $plugin;
            private $player;
            public function __construct($plugin, $player) {
                $this->plugin = $plugin;
                $this->player = $player;
            }
            public function onRun(int $currentTick) {
                if ($this->player->isOnline()) {
                    $this->plugin->checkAuth($this->player);
                }
            }
        }, 20);
    }
    
    public function showCoordinates(Player $player) {
        $pk = new GameRulesChangedPacket();
        $pk->gameRules = ["showcoordinates" => [1, true]];
        $player->dataPacket($pk);
    }

    public function checkAuth(Player $player) {
        $name = strtolower($player->getName());
        $ip = $player->getAddress();

        if (!$this->db->exists($name)) {
            $this->showRegisterForm($player);
        } else {
            $data = $this->db->get($name);
            // Suporte para formato antigo (apenas string hash) ou novo (array com ip)
            $lastIp = is_array($data) ? ($data["ip"] ?? "") : "";
            
            if ($lastIp === $ip) {
                $this->authenticated[$name] = true;
                $player->sendMessage("§a[leni login] Bem-vindo de volta!.");
            } else {
                $this->showLoginForm($player);
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event) {
        $name = strtolower($event->getPlayer()->getName());
        unset($this->authenticated[$name]);
    }

    public function onMove(PlayerMoveEvent $event) {
        if (!$this->isAuthenticated($event->getPlayer())) {
            $event->setCancelled();
        }
    }

    public function onChat(PlayerChatEvent $event) {
        if (!$this->isAuthenticated($event->getPlayer())) {
            $event->setCancelled();
        }
    }

    public function onCommandPre(PlayerCommandPreprocessEvent $event) {
        $player = $event->getPlayer();
        if (!$this->isAuthenticated($player)) {
            $message = $event->getMessage();
            if (strpos($message, "/login") !== 0 && strpos($message, "/register") !== 0) {
                $event->setCancelled();
            }
        }
    }

    public function onBreak(BlockBreakEvent $event) {
        if (!$this->isAuthenticated($event->getPlayer())) $event->setCancelled();
    }

    public function onPlace(BlockPlaceEvent $event) {
        if (!$this->isAuthenticated($event->getPlayer())) $event->setCancelled();
    }

    public function onDamage(EntityDamageEvent $event) {
        $entity = $event->getEntity();
        if ($entity instanceof Player && !$this->isAuthenticated($entity)) {
            $event->setCancelled();
        }
    }

    private function isAuthenticated(Player $player) {
        return isset($this->authenticated[strtolower($player->getName())]);
    }

    private function savePlayer(Player $player, string $passwordHash) {
        $name = strtolower($player->getName());
        $this->db->set($name, [
            "hash" => $passwordHash,
            "ip" => $player->getAddress(),
            "last_login" => date("Y-m-d H:i:s")
        ]);
        $this->db->save();
    }

    public function showRegisterForm(Player $player) {
        $form = new CustomForm("§l§bREGISTRO", function(Player $player, $data) {
            if ($data === null) {
                $this->showRegisterForm($player);
                return;
            }
            $pass = $data[1];
            $conf = $data[2];
            if (strlen($pass) < 4) {
                $player->sendMessage("§cERRO: A senha deve ter pelo menos 4 caracteres!");
                $this->showRegisterForm($player);
                return;
            }
            if ($pass !== $conf) {
                $player->sendMessage("§cERRO: As senhas não coincidem!");
                $this->showRegisterForm($player);
                return;
            }
            
            $this->savePlayer($player, password_hash($pass, PASSWORD_DEFAULT));
            $this->authenticated[strtolower($player->getName())] = true;
            $player->sendMessage("§aSucesso! Você se registrou e sua sessão foi salva.");
        });

        $form->addElement(new Label("Bem-vindo! Por favor, crie uma senha para se registrar."));
        $form->addElement(new Input("Senha:", "Digite sua senha aqui..."));
        $form->addElement(new Input("Confirme a Senha:", "Repita sua senha..."));

        $player->sendForm($form);
    }

    public function showLoginForm(Player $player) {
        $form = new CustomForm("§l§aLOGIN", function(Player $player, $data) {
            if ($data === null) {
                $this->showLoginForm($player);
                return;
            }
            $pass = $data[1];
            $name = strtolower($player->getName());
            $playerData = $this->db->get($name);
            
            // Suporte para legado
            $hash = is_array($playerData) ? $playerData["hash"] : $playerData;
            
            if (password_verify($pass, $hash)) {
                $this->authenticated[$name] = true;
                $this->savePlayer($player, $hash); // Atualiza IP e data
                $player->sendMessage("§aLogin realizado com sucesso! Sua sessão foi salva.");
            } else {
                $player->sendMessage("§cERRO: Senha incorreta!");
                $this->showLoginForm($player);
            }
        });

        $form->addElement(new Label("Olá novamente! Por favor, digite sua senha para entrar."));
        $form->addElement(new Input("Senha:", "Digite sua senha..."));

        $player->sendForm($form);
    }
}
