<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    protected $fillable = ['name', 'value'];

    public static function get($name) {
        return Variable::where('name', $name)->get();
    }

    public static function set($name, $value) {

        $var = Variable::where('name', $name)->get();

        if (!$var)
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
