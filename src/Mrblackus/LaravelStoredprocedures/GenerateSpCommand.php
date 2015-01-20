<?php

namespace Mrblackus\LaravelStoredprocedures;

use Illuminate\Console\Command;

class GenerateSpCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate-sp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate models for stored procedure on database';

    /**
     * Create a new command instance.
     *
     * @return GenerateSpCommand
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->info('Reading stored procedures from database...');

        $schema     = \Config::get('laravel-storedprocedures::schema');
        $model_path = app_path(\Config::get('laravel-storedprocedures::model_save_dir'));

        if (!file_exists($model_path))
            mkdir($model_path);

        $generator = new Generator(\DB::connection()->getPdo(), $schema, $model_path);
        $nbSp      = $generator->run();

        $this->info($nbSp . ' SP model' . ($nbSp > 1 ? 's' : '') . ' generated !');
        exec('php artisan dump-autoload');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }

}
