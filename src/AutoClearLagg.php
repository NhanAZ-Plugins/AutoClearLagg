<?php
declare(strict_types=1);

namespace twisted\autoclearlagg;

use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use function in_array;
use function is_array;
use function str_replace;
use function strtolower;

class AutoClearLagg extends PluginBase{

    public const LANG_TIME_LEFT = "time-left";
    public const LANG_ENTITIES_CLEARED = "entities-cleared";

    private int $interval;
    private int $seconds;

    private bool $clearItems;
    private bool $clearMobs;
    private bool $clearXpOrbs;

    /** @var list<string> */
    private array $exemptEntities;

    /** @var array<string, string> */
    private array $messages;
    /** @var list<int> */
    private array $broadcastTimes;

    private function rejectConfiguration(string $message) : void{
        $this->getLogger()->warning("Config error: " . $message . ". Cleanup will not start.");
    }

    public function onEnable() : void{
        $config = $this->getConfig()->getAll();

        $seconds = $config["seconds"] ?? 300;
        if(!is_int($seconds) || $seconds < 1){
            $this->rejectConfiguration("seconds must be a positive integer");

            return;
        }
        $this->interval = $this->seconds = $seconds;

        if(!is_array($config["clear"] ?? [])){
            $this->rejectConfiguration("clear must be an array");

            return;
        }
        $clear = $config["clear"] ?? [];
        $items = $clear["items"] ?? false;
        $mobs = $clear["mobs"] ?? false;
        $xpOrbs = $clear["xp-orbs"] ?? false;
        if(!is_bool($items) || !is_bool($mobs) || !is_bool($xpOrbs)){
            $this->rejectConfiguration("clear flags must be booleans");

            return;
        }
        $this->clearItems = $items;
        $this->clearMobs = $mobs;
        $this->clearXpOrbs = $xpOrbs;
        if(!is_array($clear["exempt"] ?? [])){
            $this->rejectConfiguration("clear.exempt must be an array");

            return;
        }
        $this->exemptEntities = [];
        foreach($clear["exempt"] ?? [] as $entity){
            if(!is_string($entity) || $entity === ""){
                $this->rejectConfiguration("clear.exempt must contain nonempty entity names");

                return;
            }
            $this->exemptEntities[] = strtolower($entity);
        }

        if(!is_array($config["messages"] ?? [])){
            $this->rejectConfiguration("messages must be an array");

            return;
        }
        $messages = $config["messages"] ?? [];
        $defaults = [
            self::LANG_TIME_LEFT => $messages[self::LANG_TIME_LEFT] ?? "§cEntities will clear in {SECONDS} seconds",
            self::LANG_ENTITIES_CLEARED => $messages[self::LANG_ENTITIES_CLEARED] ?? "§cCleared a total of {COUNT} entities"
        ];
        $this->messages = [];
        foreach($defaults as $key => $message){
            if(!is_string($message)){
                $this->rejectConfiguration("messages." . $key . " must be a string");

                return;
            }
            $this->messages[$key] = $message;
        }

        $times = $config["times"] ?? [60, 30, 15, 10, 5, 4, 3, 2, 1];
        if(!is_array($times)){
            $this->rejectConfiguration("times must be an array");

            return;
        }
        $this->broadcastTimes = [];
        foreach($times as $time){
            if(!is_int($time) || $time < 1){
                $this->rejectConfiguration("times must contain positive integers");

                return;
            }
            $this->broadcastTimes[] = $time;
        }

        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void{
            if(--$this->seconds === 0){
                $entitiesCleared = 0;
                foreach($this->getServer()->getWorldManager()->getWorlds() as $level){
                    foreach($level->getEntities() as $entity){
                        if($this->clearItems && $entity instanceof ItemEntity){
                            $entity->flagForDespawn();
                            ++$entitiesCleared;
                        }else if($this->clearMobs && $entity instanceof Living && !$entity instanceof Human){
                            if(!in_array(strtolower($entity->getName()), $this->exemptEntities, true)){
                                $entity->flagForDespawn();
                                ++$entitiesCleared;
                            }
                        }else if($this->clearXpOrbs && $entity instanceof ExperienceOrb){
                            $entity->flagForDespawn();
                            ++$entitiesCleared;
                        }
                    }
                }
                if($this->messages[self::LANG_ENTITIES_CLEARED] !== ""){
                    $this->getServer()->broadcastMessage(str_replace("{COUNT}", (string) $entitiesCleared, $this->messages[self::LANG_ENTITIES_CLEARED]));
                }

                $this->seconds = $this->interval;
            }else if(in_array($this->seconds, $this->broadcastTimes, true) && $this->messages[self::LANG_TIME_LEFT] !== ""){
                $this->getServer()->broadcastMessage(str_replace("{SECONDS}", (string) $this->seconds, $this->messages[self::LANG_TIME_LEFT]));
            }
        }), 20);
    }
}
