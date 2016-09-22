<?php

namespace Sarav\Admin\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class AdminInitializeCommand extends Command
{
	/**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes the admin module';

    protected $namespace;

    protected $folder;

    protected $files;

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
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
    	$retrieveFolderAndNamespace = $this->getNamespaceFolder();

    	$this->folder = $retrieveFolderAndNamespace['path'];

    	$this->namespace = $retrieveFolderAndNamespace['namespace'];

    	$this->call('module:make:controller', [
            'name'        => 'Admin/Auth',
            '--namespace' => rtrim($this->namespace, '\\'),
            '--stub'      => __DIR__.'/admin/stubs/auth_controller.stub'
        ]);

        $this->createFile('PasswordController.php', __DIR__.'/admin/stubs/password_controller.stub', 'Admin/Auth/Http/Controllers/');
        $this->createFile('HomeController.php', __DIR__.'/admin/stubs/home_controller.stub', 'Admin/Auth/Http/Controllers/');

        $this->createFile('AdminAuthenticate.php', __DIR__.'/admin/adminauthenticate.stub', 'Admin/Auth/Http/Middleware/');
        $this->createFile('AdminRedirectIfAuthenticated.php', __DIR__.'/admin/redirectifauthenticated.stub', 'Admin/Auth/Http/Middleware/');

        $this->publishMigration();
        $this->publishViews();
        $this->publishAssets();

        $this->call('module:make:route.service.provider', [
            'name'        => 'Admin/Auth',
            '--namespace' => rtrim($this->namespace, '\\')
        ]);

        $this->createFile('RouteServiceProvider.php', __DIR__.'/admin/route_service_provider.stub', 'Admin/Auth/Providers/');
        $this->createFile('AuthServiceProvider.php', __DIR__.'/admin/provider.stub', 'Admin/Auth/Providers/');

        $this->call('module:make:breadcrumb', [
            'name'        => 'Admin/Auth',
            '--namespace' => rtrim($this->namespace, '\\'),
            '--stub'      => __DIR__.'/admin/breadcrumbs.stub'
        ]);

        $this->call('module:make:routes', [
            'name'        => 'Admin/Auth',
            '--namespace' => rtrim($this->namespace, '\\'),
            '--stub'      => __DIR__.'/admin/routes.stub'
        ]);

        $this->createFile('Admin.php', __DIR__.'/admin/model.stub', 'Admin/Auth/Model/');
        $this->createFile('AdminInterface.php', __DIR__.'/admin/interface.stub', 'Admin/Auth/Repository/');
        $this->createFile('AdminRepository.php', __DIR__.'/admin/repository.stub', 'Admin/Auth/Repository/');

        app('composer')->dumpAutoloads();
    }

    public function publishViews()
    {
        $path = $this->folder.'Admin/Auth/resources';

        $this->makeDirectory($path);

        $this->files->copyDirectory(__DIR__.'/admin/views/', $path);

        $this->info("Views published successfully!");
    }

    public function createFile($name, $stub, $folder)
    {

        $path = $this->folder.$folder;

        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        $namespace = str_replace('/', '\\', $folder);

        $namespace = rtrim($namespace, '\\');

    	$newNamespace = $this->namespace.$namespace;

        $newFilePath = $this->folder.$folder;

        $content = $this->files->get($stub);

        $newContent = str_replace('DummyNamespace', $newNamespace, $content);

        $newContent = $this->replaceDummies($newContent);

        $this->files->put($newFilePath.$name, $newContent);
    }

    public function parseNamespace()
    {
    	return str_replace('/', '\\', rtrim($this->argument('namespace', '//'))).'\\';
    }

    public function publishAssets()
    {
        $this->files->copyDirectory(__DIR__.'/admin/assets/', public_path());

        $this->info("Assets published successfully!");
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

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim($this->argument('namespace'));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['namespace', InputArgument::REQUIRED, 'The namespace for the admin module'],
        ];
    }

    public function publishMigration()
    {
    	if ($this->files->exists(database_path().'/migrations/2014_10_12_000000_create_admins_table.php'))
    	{
    		return $this->error('Migration file already exists');
    	}

    	if ($this->files->copy(__DIR__.'/admin/migrations/2014_10_12_000000_create_admins_table.php', database_path().'/migrations/2014_10_12_000000_create_admins_table.php'))
    	{
    		$this->info('Admin table migration file created!');
    	} else{
    		$this->error('Admin table migration failed!');
    	}
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

    public function replaceDummies($content)
    {
        $replacableWords = [
            'DummyControllerFolders'             => $this->namespace.'Admin\Auth\Http\Controllers',
            'DummyRouteServiceProviderNamespace' => $this->namespace.'Admin\Auth\Providers\RouteServiceProvider',
            'DummyInterfaceNamespace'            => $this->namespace.'Admin\Auth\Repository\AdminInterface',
            'DummyInterface'                     => 'AdminInterface',
            'DummyModelNamespace'                => $this->namespace.'Admin\Auth\Model\Admin',
            'DummyModel'                         => 'Admin',
            'DummyRepositoryNamespace'           => $this->namespace.'Admin\Auth\Repository\AdminRepository',
            'DummyRepository'                    => 'AdminRepository',
            'DummyAuthenticate'                  => '\\'.$this->namespace.'Admin\Auth\Http\Middleware\AdminAuthenticate',
            'DummyRedirect'                      => '\\'.$this->namespace.'Admin\Auth\Http\Middleware\AdminRedirectIfAuthenticated',
        ];

        foreach($replacableWords as $key => $value)
        {
            $content = str_ireplace($key, $value, $content);
        }

        return $content;
    }
}