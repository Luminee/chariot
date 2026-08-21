<?php

namespace Luminee\Chariot\Console;

use Illuminate\Console\Application;
use Illuminate\Database\DatabaseManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Chariot extends Application
{
    /**
     * @var DatabaseManager
     */
    protected $db;

    /**
     * @var string
     */
    protected $connection = null;

    /**
     * @var string
     */
    protected $project = null;

    /**
     * @var string
     */
    protected $scripts_dir = null;

    /**
     * @var string
     */
    protected $connection_separator = '';

    /**
     * @var string
     */
    protected $directory_separator = '';

    /**
     * Bootstrap the console application.
     *
     * @return void
     */
    protected function bootstrap()
    {
        $scripts_dir = config('chariot.scripts_dir');
        if (!file_exists($scripts_dir)) {
            mkdir($scripts_dir, 0755, true);
        }
        $this->scripts_dir = realpath($scripts_dir);

        $this->connection_separator = config('chariot.signature.connection_separator');
        $this->directory_separator = config('chariot.signature.directory_separator');

        $this->db = $this->laravel->get('db');

        parent::bootstrap();
    }

    /**
     * Load the commands for the application.
     *
     * @return $this
     */
    public function loadCommands()
    {
        $files = scandir($this->scripts_dir);
        foreach ($files as $file) {
            $this->loadScript($this->scripts_dir, $file);
        }

        return $this;
    }

    protected function loadScript($path, $file)
    {
        if ($file == '.' || $file == '..') {
            return;
        }

        if (is_dir($dir = $path . '/' . $file)) {
            foreach (scandir($dir) as $f) {
                $this->loadScript($dir, $f);
            }
        } else {
            $command = $this->instanceScript($path . '/' . $file);
            if ($command instanceof Command) {
                $this->add($command);
            }
        }
    }

    protected function instanceScript($file)
    {
        $className = $this->getScriptClassname($file);
        $command = require $file;
        if (!is_null($className)) {
            $command = new $className;
        }
        return $command;
    }

    protected function getScriptClassname($file)
    {
        $namespace = $className = '';
        $tokens = token_get_all(file_get_contents($file));
        foreach ($tokens as $key => $token) {
            // 提取命名空间
            if ($token[0] === T_NAMESPACE) {
                for ($i = $key + 1; $i < count($tokens); $i++) {
                    if ($tokens[$i] === ';') break;
                    if (is_array($tokens[$i])) {
                        $namespace .= $tokens[$i][1];
                    }
                }
            }
            // 提取类名（只取第一个类）
            if ($token[0] === T_CLASS && !empty($namespace)) {
                $className = $tokens[$key + 2][1]; // T_CLASS 后第2个token是类名
                return trim(trim($namespace), '\\') . '\\' . $className;
            }
        }
        return null;
    }

    /**
     * Finds a command by name or alias.
     *
     * Contrary to get, this command tries to find the best
     * match if you give it an abbreviation of a name or alias.
     *
     * @param string $name A command name or a command alias
     *
     * @return Command A Command instance
     *
     * @throws CommandNotFoundException When command name is incorrect or ambiguous
     */
    public function find($name)
    {
        try {
            return parent::find($name);
        } catch (CommandNotFoundException $e) {
            return parent::find($this->parseCommand($name));
        }
    }

    protected function parseCommand($name)
    {
        if (stristr($name, $this->connection_separator)) {
            list($name, $connection) = explode($this->connection_separator, $name);

            if (stristr($name, $this->directory_separator)) {
                list($directory) = explode($this->directory_separator, $name);
                list($project) = explode('.', $directory);
                $this->project = strtolower($project);
            }

            $this->connection = $connection;
        }

        return $name;
    }

    /**
     * Runs the current command.
     *
     * If an event dispatcher has been attached to the application,
     * events are also dispatched during the life-cycle of the command.
     *
     * @return int 0 if everything went fine, or an error code
     */
    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output)
    {
        if (!$this->connection) {
            return parent::doRunCommand($command, $input, $output);
        }

        $connectionName = (empty($this->project) ? '' : $this->project . '_') . $this->connection;

        // 如果安装了 luminee/switcher，使用它来切换连接（支持 extra_connections）
        if ($this->laravel->bound('switcher')) {
            return $this->laravel->make('switcher')
                ->run(function () use ($command, $input, $output) {
                    return parent::doRunCommand($command, $input, $output);
                }, $connectionName);
        }

        // Fallback: 直接切换数据库连接
        $previous = $this->db->getDefaultConnection();
        $this->db->setDefaultConnection($connectionName);

        try {
            return parent::doRunCommand($command, $input, $output);
        } finally {
            $this->db->setDefaultConnection($previous);
        }
    }
}
