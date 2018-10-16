<?php
/**
 * Created by PhpStorm.
 * User: Jian Astrero
 * Date: 25/09/2018
 * Time: 5:49 PM
 */

namespace JianAstrero\JuggerAPI\Traits;

use function array_key_exists;
use function array_keys;
use function file_put_contents;
use Illuminate\Support\Facades\Schema;
use function is_array;
use function is_string;
use function json_decode;
use function json_encode;
use function property_exists;
use function public_path;

trait CanMutate
{
    public function mutate() {
        return $this->mutateColumns()->mutateCasts();
    }

    private function mutateCasts() {
        foreach (array_keys($this->casts) as $key) {
            if ($this->casts[$key] == 'array') {
                if (is_string($this->{$key})) {
                    $this->{$key} = json_decode($this->{$key});
                }
            }
        }
        return $this;
    }

    private function mutateColumns() {
        foreach ($this->getTableColumns() as $key) {
            $type = Schema::getColumnType($this->getTable(), $key);
            if ($type == 'boolean') {
                $this->{$key} = $this->{$key} == 1;
            } else if (is_string($this->{$key}) && get_called_class()::is_JSON($this->{$key})) {
                $this->{$key} = json_decode($this->{$key});
            }
        }

        return $this;
    }

    public function fillMutatedMultiple($array) {
        foreach (array_keys($array) as $key) {
            $type = Schema::getColumnType($this->getTable(), $key);
            if ($type == 'boolean') {
                $array[$key] = ("".$array[$key]) == "true" ? 1 : 0;
            } else if (is_string($array[$key]) && get_called_class()::is_JSON($array[$key])) {
                $array[$key] = json_decode($array[$key]);
            }
        }

        return $this->fill($array);
    }

    public function fillMutated($request) {
        $array = $request->input();
        if (with(new static)->juggerCasts != null) {
            foreach (array_keys(with(new static)->juggerCasts) as $key) {
                if (with(new static)->juggerCasts[$key] == 'file') {
                    if ($request->hasFile($key)) {
                        $path = $request->{$key}->store('files');
                        $array[$key] = $path;
                    }
                }
            }
        }
        foreach (array_keys($array) as $key) {
            $type = Schema::getColumnType($this->getTable(), $key);
            if ($type == 'boolean') {
                $array[$key] = ("".$array[$key]) == "true" ? 1 : 0;
            } else if (is_string($array[$key]) && get_called_class()::is_JSON($array[$key])) {
                $array[$key] = json_decode($array[$key]);
            }
        }

        return $this->fill($array);
    }

    public static function createMutated($request) {
        $array = $request->input();
        if (with(new static)->juggerCasts != null) {
            foreach (array_keys(with(new static)->juggerCasts) as $key) {
                if (with(new static)->juggerCasts[$key] == 'file') {
                    if ($request->hasFile($key)) {
                        $path = $request->{$key}->store('files');
                        $array[$key] = $path;
                    }
                }
            }
        }
        foreach (array_keys($array) as $key) {
            $type = Schema::getColumnType(get_called_class()::getTableName(), $key);
            if ($type == 'boolean') {
                $array[$key] = ("".$array[$key]) == "true" ? 1 : 0;
            } else if (is_string($array[$key]) && get_called_class()::is_JSON($array[$key])) {
                $array[$key] = json_decode($array[$key]);
            }
        }
        file_put_contents(public_path('array.json'), json_encode($array));

        $item = get_called_class()::create($array);

        return $item;
    }

    public static function is_JSON($var) {
        if (!is_string($var)) return false;
        json_decode($var);
        return (json_last_error()===JSON_ERROR_NONE);
    }
}
