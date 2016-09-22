<?php
namespace Sarav\Admin\Console\Generators;

use Illuminate\Support\Str;
use Sarav\Admin\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RouteServiceProviderMakeCommand extends GeneratorCommand
{

    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'module:make:route.service.provider';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create a new service provider.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Route Service Provider';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return ($this->option('stub')) ? $this->option('stub') : __DIR__.'/../stubs/route_service_provider.stub';
    }

    /**
     * Gets the file namespace.
     */
    public function getFileNamespace()
    {
        return $this->parseName($this->getNameInput()).'\\Providers';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $fileName = $this->processFolderPath($name, 'Providers/');

        $splittedName = explode('/', $fileName);

        array_pop($splittedName);

        return implode('/', $splittedName).'/RouteServiceProvider.php';
    }


    /**
     * The array of command arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return [
            [
                'name',
                InputArgument::REQUIRED,
                'The name of class being generated.',
                null
            ]
        ];
    }


    /**
     * The array of command options.
     *
     * @return array
     */
    public function getOptions()
    {
        parent::getOptions();

        $options = [
            [
                'stub',
                null,
                InputOption::VALUE_OPTIONAL,
                'The fillable attributes.',
                null
            ],
        ];

        foreach($options as $option)
        {
            $this->options[] = $option;
        }

        return $this->options;
    }
}
