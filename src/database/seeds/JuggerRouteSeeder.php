<?php

use Illuminate\Database\Seeder;
use JianAstrero\JuggerAPI\Models\JuggerRoute;

class JuggerRouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $juggerRoute = new JuggerRoute();
        $juggerRoute->model_name = 'App\JuggerRoute';
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
        $juggerRoute->sort = array('model_name','asc');
        $juggerRoute->sort_override = true;
        $juggerRoute->save();
    }
}
