<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    protected $fillable = ['name', 'value'];
    public $incrementing = false;
    protected $primaryKey = 'name';

    public static function getValue($name) {

        $var = Variable::where('name', $name)->first();
        if (empty($var)) {
            return null;
        }

        return $var->value;
    }

    public static function set($name, $value) {

        $var = Variable::where('name', $name)->first();

        if (empty($var))
            Variable::create([
                'name' => $name,
                'value' => $value
            ]);
        else {
            $var->value = $value;
            $var->save();
        }
    }

    public static function exists($name) {
        if (Variable::where('name', $name)->get())
            return true;
        else
            return false;
    }
}
