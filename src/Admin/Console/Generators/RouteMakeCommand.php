<?php
namespace Sarav\Admin\Console\Generators;

use Illuminate\Support\Str;
use Sarav\Admin\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RouteMakeCommand extends GeneratorCommand
{

    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'module:make:routes';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create a new routes file.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Routes';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return ($this->option('stub')) ? $this->option('stub') : __DIR__.'/../stubs/routes.stub';
    }

    /**
     * Gets the file namespace.
     */
    public function getFileNamespace()
    {
        return $this->parseName($this->getNameInput());
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
        $fileName = $this->processFolderPath($name, 'routes/');

        $splittedName = explode('/', $fileName);

        array_pop($splittedName);

        return implode('/', $splittedName).'/web.php';
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
