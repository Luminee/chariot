<?php

namespace Luminee\Chariot\Commands;

use Luminee\Chariot\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeScriptCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chariot:make:script {signature} {--project=} {--module=} {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Chariot script command';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @param  Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $signature = str_replace([':', '-'], '_', $this->argument('signature'));
        $name = $this->option('name') ?: date('Y_m_d_His') . "_" . Str::snake($signature);

        $path = $this->getScriptPath();
        $this->makeDirectory($path);

        $path .= "/{$name}.php";

        if ($this->files->exists($path)) {
            $this->error("Script file already exists at {$path}!");
            return false;
        }

        $this->files->put($path, $this->buildCommandFile($this->argument('signature')));

        $this->info("Script file created successfully at {$path}!");
    }

    /**
     * Get the path for the script file.
     *
     * @return string
     */
    protected function getScriptPath()
    {
        $directory = $this->scripts_dir;

        $project = $this->option('project');
        if ($project) {
            $directory .= "/{$project}";
        }

        $module = $this->option('module');
        if ($module) {
            $directory .= "/{$module}";
        }

        return $directory;
    }

    /**
     * Create the directory for the script file.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true, true);
        }

        return $path;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $signature
     * @return string
     */
    protected function buildCommandFile($signature)
    {
        $stub = $this->files->get(__DIR__ . '/stubs/script.stub');

        return str_replace(['{$signature}'],  [$signature],  $stub);
    }
}
