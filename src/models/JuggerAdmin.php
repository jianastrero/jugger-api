<?php

namespace JianAstrero\JuggerAPI\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class JuggerAdmin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'jugger_admins';

    protected $fillable = [
        'username',
        'password'
    ];

    protected $hidden = [
        'password'
    ];
}
