<?php

namespace Sarav\Admin\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Sarav\Admin\Console\Traits\FolderTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class GeneratorCommand extends Command
{
	use FolderTrait;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type;

    /**
     * Namespace for the module
     */
    protected $namespace;


    protected $name;

    /**
     * Options for the command
     *
     * @var array
     */
    protected $options = [];

    /**
     * Create a new controller creator command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    abstract protected function getStub();

    /**
     * Gets the file namespace.
     *
     * @return  string
     */
    abstract protected function getFileNamespace();

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        $this->name = $name = $this->parseName($this->getNameInput());

        $path = $this->getPath($name);

        if ($this->alreadyExists($this->getNameInput())) {
            
            $this->error($this->type.' already exists!');

            return false;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->info($this->type.' created successfully.');
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        $name = $this->parseName($rawName);

        return $this->files->exists($this->getPath($name));
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace($this->laravel->getNamespace(), '', $name);

        return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Parse the name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function parseName($name)
    {
        $rootNamespace = $this->getGivenNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        if (Str::contains($name, '/')) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->parseName($this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name);
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
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceDummies($stub);

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            'DummyNamespace', $this->getFileNamespace(), $stub
        );

        $stub = str_replace(
            'DummyRootNamespace', $this->getGivenNamespace(), $stub
        );
        
        return $this;
    }

    /**
     * Get the full namespace name for a given class.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace('DummyClass', $class, $stub);
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

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class'],
        ];
    }

    /**
     * The array of command options.
     *
     * @return array
     */
    public function getOptions()
    {
    	$this->options[] = [
            'namespace',
            null,
            InputOption::VALUE_OPTIONAL,
            'The namespace attributes.',
            null
        ];

        return $this->options;
    }

    public function getGivenNamespace()
    {
        if ($this->option('namespace'))
        {
        	$folder = $this->getNamespaceFolder();

        	if (count($folder))
        	{
        		return $folder['namespace'];
        	}
        }

        return $this->laravel->getNamespace();
    }

    public function parseNamespace()
    {
    	return str_replace('/', '\\', rtrim($this->option('namespace', '//'))).'\\';
    }

    public function getNamespaceFolder()
    {
    	$giveNamespace = $this->parseNamespace();

        if (empty(rtrim($giveNamespace, '\\')))
        {
            $giveNamespace = $this->laravel->getNamespace();
        }

    	$composer = json_decode(file_get_contents(base_path('composer.json')), true);

        foreach ((array) data_get($composer, 'autoload.psr-4') as $namespace => $path) {
            foreach ((array) $path as $pathChoice) {
                if ($namespace == $giveNamespace) {
                    return [
                    	'namespace' => $namespace,
                    	'path'      => base_path($pathChoice)
                    ];
                }
            }
        }

        throw new \Exception('Give namespace is not registered in composer.json. Please register and proceed');
    }

    public function getFolderName()
    {
    	return $this->getNameInput();
    }

    protected function replaceDummies($content)
    {
        
        if (stripos($this->getNameInput(), '/') !== false)
        {
            $url = $this->getCommonTerm();
        }
        else
        {
            $url = $this->getPluralName();
        }

        $replaceWords = [
            'DummyPluralCaps'                    => ucfirst($this->getPluralName()),
            'DummyPlural'                        => $this->getPluralName(),
            'DummySingularCaps'                  => ucfirst(str_singular($this->getPluralName())),
            'DummySingular'                      => str_singular($this->getPluralName()),
            'DummyListUrl'                       => $url.'.index',
            'DummyCreateUrl'                     => $url.'.create',
            'DummyStoreUrl'                      => $url.'.store',
            'DummyEditUrl'                       => $url.'.edit',
            'DummyUpdateUrl'                     => $url.'.update',
            'DummyDeleteUrl'                     => $url.'.destroy',
            'DummyFormFilePath'                  => $url.'._form',
            'DummyControllerNamespace'           => $this->getLastElement($this->getControllerName()),
            'DummyRouteServiceProviderNamespace' => $this->getRouteServiceProviderName(),
            'DummyInterfaceNamespace'            => $this->getInterfaceName(),
            'DummyInterface'                     => $this->getLastElement($this->getInterfaceName()),
            'DummyRequestNamespace'              => $this->getRequestName(),
            'DummyRequest'                       => $this->getLastElement($this->getRequestName()),
            'DummyControllerFolders'             => implode('\\', $this->getAllElementExceptLast($this->getControllerName())),
            'DummyRepositoryNamespace'           => $this->getRepositoryName(),
            'DummyModelNamespace'                => $this->getModelName(),
            'DummyModel'                         => $this->getLastElement($this->getModelName()),
            'DummyTest'                          => $this->getLastElement($this->getTestCaseName()),
            'dummyview'                          => $url,
            'dummyroutes'                        => str_replace('.', '/', $this->getCommonTerm()),
            'listallurl'                         => str_replace('.', '/', $url),
            'createurl'                          => str_replace('.', '/', $url.'.create')
        ];

        foreach($replaceWords as $key => $value)
        {
            $content = str_ireplace($key, $value, $content);
        }

        return $content;
    }

    protected function getCommonTerm()
    {
        $url = str_replace('/', '.', implode('/', $this->getAllElementExceptLast($this->getNameInput())));
        $url = strtolower($url).'.'.$this->getPluralName();

        return $url;
    }

    protected function getLastElement($element)
    {
        if (stripos($element, '/') !== false)
        {
            $names = explode('/', $element);
        }
        elseif(stripos($element, '\\') !== false)
        {
            $names = explode('\\', $element);
        }else{
            $names = $element;
        }

        if (!is_array($names))
        {
            $names = [$names];
        }

        return array_pop($names);
    }

    protected function getAllElementExceptLast($element)
    {
        if (stripos($element, '/') !== false)
        {
            $names = explode('/', $element);
        }
        elseif(stripos($element, '\\') !== false)
        {
            $names = explode('\\', $element);
        }else{
            $names = $element;
        }

        if (!is_array($names))
        {
            $names = [$names];
        }

        array_pop($names);

        return $names;
    }

    protected function getPluralName()
    {
        return strtolower(str_plural($this->getLastElement($this->getNameInput())));
    }

    protected function getInterfaceName()
    {
        return $this->name.'\\Repository\\'.ucfirst($this->getFileName()).'Interface';
    }

    protected function getModelName()
    {
        return $this->name.'\\Model\\'.ucfirst($this->getFileName());
    }

    protected function getRepositoryName()
    {
        return $this->name.'\\Repository\\'.ucfirst($this->getFileName()).'Repository';
    }

    protected function getRequestName()
    {
        return $this->name.'\\Http\\Requests\\'.ucfirst($this->getFileName()).'Request';
    }

    protected function getControllerName()
    {
        return $this->name.'\\Http\\Controllers\\'.ucfirst($this->getFileName()).'Controller';
    }

    protected function getProviderName()
    {
        return $this->name.'\\Providers\\'.ucfirst($this->getFileName()).'ServiceProvider';
    }

    protected function getTestCaseName()
    {
        return ucfirst($this->getLastElement($this->getNameInput())).'TestCase';
    }

    protected function getRouteServiceProviderName()
    {
        return $this->name.'\\Providers\\RouteServiceProvider';
    }

    protected function getFileName()
    {
        return $this->getLastElement($this->name);
    }
}
