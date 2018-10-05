<?php

namespace JianAstrero\JuggerAPI\Console\Commands;

use Illuminate\Console\Command;
use JianAstrero\JuggerAPI\Models\JuggerRoute;

class JuggerSeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jugger:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed jugger routes with itself such that Jugger API frontend would work';

    /**
     * Create a new command instance.
     *
     * @return void
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
    public function handle()
    {
        $juggerRoute = new JuggerRoute();
        $juggerRoute->model_name = 'JianAstrero\JuggerAPI\Models\JuggerRoute';
        $juggerRoute->slug = 'jugger-api-routes';
        $juggerRoute->columns =
            array(
                'id',
                'model_name',
                'slug',
                'columns',
                'column_override',
                'sort',
                'sort_override',
                'create',
                'read',
                'update',
                'delete',
                'list',
                'paginate_item_count',
            );
        $juggerRoute->column_override = true;
        $juggerRoute->sort = '+model_name';
        $juggerRoute->sort_override = true;
        $juggerRoute->save();
        $this->info('Successfully seeded jugger routes!');
    }
}
