<?php

namespace JianAstrero\JuggerAPI\Models;

use Illuminate\Database\Eloquent\Model;
use JianAstrero\JuggerAPI\Traits\CanMutate;
use JianAstrero\JuggerAPI\Traits\HasTable;

class JuggerRoute extends Model
{
    use HasTable, CanMutate;

    protected $fillable = [
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
        'version'
    ];

    protected $casts = [
        'columns' => 'array'
    ];
}
