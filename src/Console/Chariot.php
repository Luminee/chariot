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
    protected $scripts_dir = null;

    /**
     * @var string
     */
    protected $connection_separator = '';

    /**
     * Bootstrap the console application.
     *
     * @return void
     */
    protected function bootstrap()
    {
        $this->scripts_dir = realpath(config('chariot.scripts_dir'));

        $this->connection_separator = config('chariot.signature.connection_separator');

        $this->db = $this->laravel->get('db');

        $this->appendExtraConnections();

        parent::bootstrap();
    }

    protected function appendExtraConnections()
    {
        $extra_connections = config('chariot.extra_connections');

        if (!empty($extra_connections)) {
            $this->laravel['config']['database.connections'] = array_merge(
                $this->laravel['config']['database.connections'],
                $$this->handleExtraConnections($extra_connections)
            );
        }
    }

    protected function handleExtraConnections($extra_connections)
    {
        $connections = [];
        foreach ($extra_connections as $name => $config) {
            $name = strtolower($name);
            if ($this->isConnectionConfig($config)) {
                $connections[$name] = $config;
            } else {
                foreach ($config as $conn => $connection_config) {
                    $connections[$name . '_' . $conn] = $connection_config;
                }
            }
        }

        return $connections;
    }

    protected function isConnectionConfig($config)
    {
        foreach ($config as $value) {
            if (!is_array($value)) {
                return true;
            }
        }

        return false;
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
            $command = require_once $path . '/' . $file;

            $this->add($command);
        }
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

        $previousConnection = $this->db->getDefaultConnection();

        $this->db->setDefaultConnection($this->connection ?? $previousConnection);

        return tap(parent::doRunCommand($command, $input, $output), function () use ($previousConnection) {
            $this->db->setDefaultConnection($previousConnection);
        });
    }
}
