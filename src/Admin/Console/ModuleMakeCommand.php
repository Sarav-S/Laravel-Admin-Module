<?php

namespace Sarav\Admin\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleMakeCommand extends Command
{
	/**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates and initializes the given module';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $name = $this->getNameInput();
        
        $this->call('module:make:interface', [
            'name'        => $name,
            '--namespace' => $this->option('namespace')
        ]);

        $this->call('module:make:testcase', [
            'name'        => $name,
            '--namespace' => $this->option('namespace')
        ]);

        $this->call('module:make:model', [
            'name'        => $name,
            '--namespace' => $this->option('namespace')
        ]);


        $exploded = explode('/', $name);
        $exploded = end($exploded);

        $tableName = str_plural(strtolower($exploded));
        
        $this->call('make:migration', [
            'name'    => 'create_'.$tableName.'_table',
            '--create' => $tableName
        ]);

        $this->call('module:make:routes', [
            'name'        => $name,
            '--namespace' => $this->option('namespace')
        ]);

        $this->call('module:make:request', [
            'name'        => $name,
            '--namespace' => $this->option('namespace')
        ]);

        $this->call('module:make:controller', [
            'name'        => $name,
            '--namespace' => $this->option('namespace')
        ]);

        $this->call('module:make:repository', [
            'name'        => $name,
            '--namespace' => $this->option('namespace')
        ]);

        $this->call('module:make:route.service.provider', [
            'name'        => $name,
            '--namespace' => $this->option('namespace')
        ]);

        $this->call('module:make:provider', [
            'name'        => $name,
            '--namespace' => $this->option('namespace')
        ]);

        $this->call('module:make:views', [
            'name'        => $name,
            '--namespace' => $this->option('namespace')
        ]);

        $this->call('module:make:breadcrumb', [
            'name'        => $name,
            '--namespace' => $this->option('namespace')
        ]);

        app('composer')->dumpAutoloads();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the module'],
        ];
    }

    /**
     * The array of command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            [
                'namespace',
                null,
                InputOption::VALUE_OPTIONAL,
                'The namespace attributes.',
                null
            ]
        ];
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim($this->argument('name'));
    }
}