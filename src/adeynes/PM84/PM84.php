<?php
declare(strict_types=1);

namespace adeynes\PM84;

use adeynes\PM84\functions\Penis;
use adeynes\PM84\functions\PM84Function;
use adeynes\PM84\utils\ConfigPaths;
use adeynes\PM84\utils\MessageFactory;
use adeynes\PM84\utils\MessagePaths;
use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

final class PM84 extends PluginBase
{

    public const COLORS = [14, 1, 4, 5, 13, 9, 3, 11, 10, 2, 6];

    public const DEFAULT_RADIUS = 50;

    /** @var PM84 */
    private static $instance;

    /** @var Config */
    private $messages;

    public static function getInstance(): self
    {
        return self::$instance;
    }

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public function onEnable(): void
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();

        $this->saveResource("messages.yml");
        $this->messages = new Config("{$this->getDataFolder()}messages.yml");
    }

    public function getMessage(string $path): ?string
    {
        return $this->messages->getNested($path) ?? null;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($label === "graph") {
            if (!$sender instanceof Player) {
                $sender->sendMessage(MessageFactory::fullFormat(
                    $this->getMessage(MessagePaths::ERROR_NOT_IN_GAME),
                    []
                ));
                return true;
            }

            if (count($args) < 1) {
                return false;
            }

            $function_name = strtolower($args[0]);
            $function_class = $this->getConfig()->getNested(ConfigPaths::FUNCTIONS.$function_name);
            if (!class_exists($function_class) || !is_subclass_of($function_class, PM84Function::class)) {
                $sender->sendMessage(MessageFactory::fullFormat(
                    $this->getMessage(MessagePaths::ERROR_FUNCTION_DOES_NOT_EXIST),
                    ["function" => $function_name]
                ));
                return true;
            }

            $radius_str = $args[1] ?? strval(self::DEFAULT_RADIUS);
            if (!is_numeric($radius_str)) {
                $sender->sendMessage(MessageFactory::fullFormat(
                    $this->getMessage(MessagePaths::ERROR_NOT_A_NUMBER),
                    ["input" => $radius_str]
                ));
                return true;
            }

            /** @var PM84Function $function */
            $function = new $function_class();

            $precision = 1000;

            $radius = intval($radius_str);
            $interval_u = (max($function->getUBounds()) - min($function->getUBounds())) / $precision;
            $interval_v = (max($function->getVBounds()) - min($function->getVBounds())) / $precision;
            // We only use x scale on purpose so every direction gets scaled the same
            // TODO: radius for each direction
            $scale = (2*$radius) / (max($function->getXDomainBounds()) - min($function->getXDomainBounds()));

            $center_x = (intval(round($sender->getX())));
            $center_y = (intval(round($sender->getY())));
            $center_z = (intval(round($sender->getZ())));

            $updated_blocks = [];

            for ($num_u = 0; $num_u < $precision; ++$num_u) {
                for ($num_v = 0; $num_v < $precision; ++$num_v) {
                    $output = $function->function_(
                        min($function->getUBounds()) + $num_u * $interval_u,
                        min($function->getVBounds()) + $num_v * $interval_v
                    );
                    $scaled_output = new Vector3(
                        intval(round($center_x + $scale * $output->getX())),
                        intval(round($center_y + $scale * $output->getY())),
                        intval(round($center_z + $scale * $output->getZ()))
                    );

                    if ($scaled_output->getY() >= 256) {
                        var_dump($output);
                        var_dump(min($function->getUBounds()) + $num_u * $interval_u);
                    }

                    $x = $scaled_output->getX();
                    $y = $scaled_output->getY();
                    $z = $scaled_output->getZ();

                    $updated_blocks[Level::blockHash($x, $y, $z)] = $scaled_output;
                }
            }

            $total = count($updated_blocks);
            $count = 0;
            $color_count = count(self::COLORS);
            foreach ($updated_blocks as $updated_block) {
                $sender->getLevel()->setBlock(
                    $updated_block,
                    Block::get(Block::WOOL, self::COLORS[intval($color_count * $count/$total)])
                );
                ++$count;
            }
        }

        return true;
    }

}