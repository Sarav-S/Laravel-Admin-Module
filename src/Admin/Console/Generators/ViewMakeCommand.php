<?php
namespace Sarav\Admin\Console\Generators;

use Illuminate\Support\Str;
use Sarav\Admin\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ViewMakeCommand extends GeneratorCommand
{

    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'module:make:views';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Creates list of view files';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Views';

    public function fire()
    {
        $path = $this->getPath($this->getNameInput());

        $this->makeDirectory($path.'create.blade.php');
        $this->files->put($path.'create.blade.php', $this->replaceDummies($this->files->get(__DIR__.'/../stubs/views/create.blade.stub')));

        $this->makeDirectory($path.'update.blade.php');
        $this->files->put($path.'update.blade.php', $this->replaceDummies($this->files->get(__DIR__.'/../stubs/views/update.blade.stub')));

        $this->makeDirectory($path.'index.blade.php');
        $this->files->put($path.'index.blade.php', $this->replaceDummies($this->files->get(__DIR__.'/../stubs/views/index.blade.stub')));

        $this->makeDirectory($path.'_form.blade.php');
        $this->files->put($path.'_form.blade.php', $this->replaceDummies($this->files->get(__DIR__.'/../stubs/views/_form.blade.stub')));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return null; 
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
        $fileName = $this->processFolderPath($name, 'resources/');

        $count = explode('/', $this->getNameInput());

        $splittedName = explode('/', $fileName);

        $negativeCount = '-'.count($count);

        $splittedName = array_slice($splittedName, 0, $negativeCount);

        return implode('/', $splittedName).'/'.$this->getNameForFolderRendering().'/';
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
