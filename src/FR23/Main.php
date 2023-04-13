<?php

namespace FR23;

use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;
use FR23\EventListener;

class Main extends PluginBase {

    private $players;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->config = $this->getConfig();
        $this->players = new Config($this->getDataFolder() . "players.yml", Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    public function onPlayerJoin(Player $player): void {
        if (!$this->players->exists($player->getName())) {
            $this->showRegisterUI($player);
        } else {
            $this->showLoginUI($player);
        }
    }

    private function showRegisterUI(Player $player): void {
        $form = new CustomForm(function(Player $player, $data): void {
            if ($data === null) {
                $player->kick(TextFormat::RED . "Registration cancelled", false);
                return;
            }
            $password = $data[1];
            $this->players->set($player->getName(), $password);
            $this->players->save();
            $player->sendMessage(TextFormat::GREEN . "You have successfully registered!");
        });
        $form->setTitle("§l§6Register");
        $form->addInput("Enter a password", "Example: pw123");
        $form->addInput("Confirm password", "Example: pw123");
        $player->sendForm($form);
    }

    private function showLoginUI(Player $player): void {
        $form = new CustomForm(function(Player $player, $data): void {
            if ($data === null) {
                $player->kick(TextFormat::RED . "Login cancelled", false);
                return;
            }
            $name = $player->getName();
            $password = $data[0];
            $savedPassword = $this->players->get($name);
            if ($password === $savedPassword) {
                $player->sendMessage(TextFormat::GREEN . "You have successfully logged in!");
            } else {
                $player->kick(TextFormat::RED . "INVALID PASSWORD\n\n§eIf you forget your Password, please contact the Admin/Owner", false);
            }
        });
        $form->setTitle("§l§6Login");
        $form->addInput("Enter your password", "Example: pw123");
        $player->sendForm($form);
    }
}
