<?php
/**
 * Created by PhpStorm.
 * User: Jian Astrero
 * Date: 25/09/2018
 * Time: 5:49 PM
 */

namespace JianAstrero\JuggerAPI\Traits;

trait HasTable
{
    public static function getTableName() {
        return with(new static)->getTable();
    }

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public static function getTableColumnsStatically() {
        return with(new static)->getConnection()->getSchemaBuilder()->getColumnListing(with(new static)->getTableName());
    }
}
