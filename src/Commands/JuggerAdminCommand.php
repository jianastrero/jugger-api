<?php

namespace JianAstrero\JuggerAPI\Console\Commands;

use Illuminate\Console\Command;
use JianAstrero\JuggerAPI\Models\JuggerAdmin;
use JianAstrero\JuggerAPI\Models\JuggerRoute;
use Illuminate\Support\Facades\Hash;

class JuggerAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jugger:admin {username=juggeradmin} {password=AdminPassword}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a jugger account to use jugger api';

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
        $juggerAdmin = new JuggerAdmin();
        $juggerAdmin->username = $this->argument('username');
        $juggerAdmin->password = Hash::make($this->argument('password'));
        $juggerAdmin->save();
        $this->info('Successfully created Jugger Admin Account!');
    }
}
