<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /**
     * Add 1 to an integer stored in value. The increment happens in the query
     * so concurrent requests do not lose counts.
     */
    public static function incrementValue($name)
    {
        $now = now()->toDateTimeString();

        DB::statement(
            'INSERT INTO `variables` (`name`, `value`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE `value` = CAST(`value` AS UNSIGNED) + 1, `updated_at` = ?',
            [$name, '1', $now, $now, $now]
        );
    }

    public static function exists($name) {
        if (Variable::where('name', $name)->get())
            return true;
        else
            return false;
    }
}
