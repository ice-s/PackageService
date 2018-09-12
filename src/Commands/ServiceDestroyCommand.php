<?php

namespace Ices\ServiceTool\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
class ServiceDestroyCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'service:destroy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Destroy Model and Service';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = Config::get('command');

        $this->deleteDir(app_path("/Entities"));
        $this->deleteDir(app_path("/Services"));
    }

    public function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            return;
        }

        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}