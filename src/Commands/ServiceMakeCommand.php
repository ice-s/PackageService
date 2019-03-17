<?php

namespace Ices\ServiceTool\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ServiceMakeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'service:make';

    /**
     * @var array
     */
    protected $config = [];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Service, Example : php artisan service:make Customer Backend';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->config = Config::get('command');

        $names = $this->argument('name');

        if (isset($names[0])) {
            $package = !empty($names[1]) ? $names[1] : '';
            $this->make($names[0], $package);
        }

    }

    protected function isExistBaseService($package = '')
    {
        return $this->createBase($package, 'Service');
    }

    protected function isExistBaseRepository($package = '')
    {
        return $this->createBase($package, 'Repository');
    }

    protected function isExistBaseModel($package = '')
    {
        return $this->createBase($package, 'Model');
    }

    protected function make($name, $package = '')
    {
        if (!$this->isExistBaseService($package)) {
            echo "Create new Base Service\n";
        }

        if (!$this->isExistBaseModel($package)) {
            echo "Create new Base Model\n";
        }

        if (!$this->isExistBaseRepository($package)) {
            echo "Create new Base Repository\n";
        }

        /*Create service*/
        $this->makeService($name, $package);

        /*Create Model*/
        $this->makeModel($name, $package);

        /*Create Repository*/
        $this->makeRepository($name, $package);
    }

    protected function makeService($name, $package = '')
    {
        $this->createClass($name, $package, 'Service');
    }

    protected function makeRepository($name, $package = '')
    {
        $this->createClass($name, $package, 'Repository');
    }

    protected function makeModel($name, $package = '')
    {
        $this->createClass($name, $package, 'Model');
    }

    protected function createBase($package, $type)
    {
        $packagePath = '';
        $packageNameSpace = '';

        if (!empty($package)) {
            $packagePath = '/' . $package;
            $packageNameSpace = '\\' . $package;
        }

        $baseFile = $this->config['BaseFile'][$type]; //PHP File

        if ($type == 'Repository' || $type == 'Model') {
            $basePath = $this->config['EntityPath'] . "{$packagePath}" . $this->config[$type . 'Path'];
        } else {
            $basePath = $this->config[$type . 'Path'] . "{$packagePath}";
        }

        $baseStub = $this->config['Stubs']['Base' . $type]; // template file

        $path = app_path($basePath);
        $filePath = app_path($basePath . "{$baseFile}");

        /*get template*/
        $stubFile = $this->getStub($baseStub);

        if (!file_exists($filePath)) {
            $this->makeDir($path);

            $template = file_get_contents($stubFile);
            $template = str_replace(['{{PackageName}}'], [$packageNameSpace], $template);

            file_put_contents($path . "/{$baseFile}", $template);

            return false;
        }

        return true;
    }

    public function createClass($name, $package = '', $type)
    {
        $packagePath = '';
        $packageNameSpace = '';

        if (!empty($package)) {
            $packagePath = '/' . $package;
            $packageNameSpace = '\\' . $package;
        }
        if ($type == "Repository" || $type == "Model") {
            $basePath = $this->config['EntityPath'] . "{$packagePath}" . $this->config[$type . 'Path'];
            $stubFileName = "stubs/{$type}.stub"; // template file
            $class = "{$name}{$type}.php";

            if ($type == "Repository"){
                $stubRepoInterface = "stubs/{$type}Interface.stub";
                $repoInterface = "{$name}{$type}Interface.php";
            }
        } else {
            $basePath = $this->config['ServicePath'] . "{$packagePath}";
            $stubFileName = "stubs/Service.stub"; // template file
            $class = "{$name}Service.php";
        }

        /*get template*/
        $stubFile = $this->getStub($stubFileName);
        $folder = $name;
        $folderPath = app_path("{$basePath}/{$folder}");
        $fileName = app_path("{$basePath}/{$folder}/{$class}");

        if (!file_exists($fileName)) {
            $this->makeDir($folderPath);
            $template = file_get_contents($stubFile);
            $template = str_replace(['{{Name}}', '{{NameFirstLowerCase}}', '{{PackageName}}'],
                [$name, lcfirst($name), $packageNameSpace], $template);
            file_put_contents($fileName, $template);

            if ($type == "Repository"){
                $stubRepoInterface = "stubs/{$type}Interface.stub";
                $repoInterface = "{$name}{$type}Interface.php";
                $fileNameRepoInterface = app_path("{$basePath}/{$folder}/{$repoInterface}");
                $templateRepo = file_get_contents($stubRepoInterface);
                $templateRepo = str_replace(['{{Name}}', '{{NameFirstLowerCase}}', '{{PackageName}}'],
                    [$name, lcfirst($name), $packageNameSpace], $templateRepo);
                var_dump($fileNameRepoInterface, $templateRepo);
                file_put_contents($fileNameRepoInterface, $templateRepo);
            }

            return true;
        } else {
            echo "WARNING : {$type} is existed\n";

            return false;
        }
    }

    protected function getStub($path)
    {
        return __DIR__ . '/../Resources/' . $path;
    }

    protected function makeDir($folderPath)
    {
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::IS_ARRAY, 'The names of services will be created.'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['plain', 'p', InputOption::VALUE_NONE, 'Generate a plain service (without some resources).'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when the service already exists.'],
        ];
    }
}