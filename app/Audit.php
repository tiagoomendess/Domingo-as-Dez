<?php


namespace App;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Audit extends SearchableModel {

    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'ip_country',
        'user_agent',
        'timezone',
        'language',
        'extra_info',
        'created_at',
    ];

    protected $guarded = [];

    protected $hidden = [];

    protected $table = 'audit';

    public function user() {
        return $this->belongsTo(User::class);
    }

    public const ACTION_VIEW = 'view';
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';
    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGIN_FAILED = 'login_failed';
    public const ACTION_LOGOUT = 'logout';
    public const ACTION_REGISTER = 'register';
    public const ACTION_FORGOT_PASSWORD = 'forgot_password';
    public const ACTION_RESET_OTHER = 'other';

    public const ACTIONS = [
        self::ACTION_VIEW => self::ACTION_VIEW,
        self::ACTION_CREATE => self::ACTION_CREATE,
        self::ACTION_UPDATE => self::ACTION_UPDATE,
        self::ACTION_DELETE => self::ACTION_DELETE,
        self::ACTION_LOGIN => self::ACTION_LOGIN,
        self::ACTION_LOGIN_FAILED => self::ACTION_LOGIN_FAILED,
        self::ACTION_LOGOUT => self::ACTION_LOGOUT ,
        self::ACTION_REGISTER => self::ACTION_REGISTER,
        self::ACTION_FORGOT_PASSWORD => self::ACTION_FORGOT_PASSWORD
    ];

    public const SEARCH_FIELDS = [
        'id' => [
            'name' => 'id',
            'type' => 'integer',
            'trans' => 'Id',
            'allowSearch' => false,
            'compare' => '=',
            'validation' => 'nullable|integer'
        ],
        'user_id' => [
            'name' => 'user_id',
            'type' => 'integer',
            'trans' => 'User Id',
            'allowSearch' => true,
            'compare' => '=',
            'validation' => 'nullable|min:1|integer'
        ],
        '_action' => [
            'name' => '_action',
            'type' => 'enum',
            'trans' => 'Action',
            'allowSearch' => true,
            'compare' => '=',
            'validation' => 'nullable|string',
            'enumItems' => [
                [
                    'name' => 'View',
                    'value' => 'view'
                ],
                [
                    'name' => 'Create',
                    'value' => 'create'
                ],
                [
                    'name' => 'Update',
                    'value' => 'update'
                ],
                [
                    'name' => 'Delete',
                    'value' => 'delete'
                ],
                [
                    'name' => 'Login',
                    'value' => 'login'
                ],
                [
                    'name' => 'Login Failed',
                    'value' => 'login_failed'
                ],
                [
                    'name' => 'Logout',
                    'value' => 'logout'
                ],
                [
                    'name' => 'Register',
                    'value' => 'register'
                ],
                [
                    'name' => 'Forgot Password',
                    'value' => 'forgot_password'
                ],
                [
                    'name' => 'Other',
                    'value' => 'other'
                ],
            ]
        ],
        'model' => [
            'name' => 'model',
            'type' => 'enum',
            'trans' => 'Modelo',
            'allowSearch' => true,
            'compare' => '=',
            'validation' => 'nullable|string|in:Article,Club,Competition,Game,GameGroup,Goal,Player,Transfer,User',
            'enumItems' => [
                [
                    'name' => 'Article',
                    'value' => 'Article'
                ],
                [
                    'name' => 'Club',
                    'value' => 'Club'
                ],
                [
                    'name' => 'Competition',
                    'value' => 'Competition'
                ],
                [
                    'name' => 'Game',
                    'value' => 'Game'
                ],
                [
                    'name' => 'Game Group',
                    'value' => 'GameGroup'
                ],
                [
                    'name' => 'Goal',
                    'value' => 'Goal'
                ],
                [
                    'name' => 'Player',
                    'value' => 'Player'
                ],
                [
                    'name' => 'Transfer',
                    'value' => 'Transfer'
                ],
                [
                    'name' => 'User',
                    'value' => 'User'
                ],
            ]
        ],
        'created_at' => [
            'name' => 'created_at',
            'type' => 'date',
            'trans' => 'Data',
            'allowSearch' => false
        ],
    ];

    /**
     * Adds an audit log entry
     *
     * @param  string  $action
     * @param  string  $model
     * @param  array|null  $oldValues
     * @param  array|null  $newValues
     * @return void
     */
    public static function add(string $action, string $model = null, $oldValues = null, $newValues = null, $extraInfo = null) {

        // Make sure this does not stop the normal code execution
        try {
            self::doAdd($action, $model, $oldValues, $newValues, $extraInfo);
        } catch (Exception $e) {
            Log::error("Error adding audit: " . $e->getMessage());
        }
    }

    private static function doAdd($action, $model, $oldValues, $newValues, $extraInfo) {

        if (empty($action) || empty($model)) {
            return;
        }

        if ($oldValues === $newValues && (!empty($oldValues) || !empty($newValues))) {
            return;
        }

        $request = request();
        $ip_address = Str::limit($request->getClientIp(), 45, '');
        $ip_country = Str::limit($request->header('CF-IPCountry', 'Unknown'), 45, '');
        $user = Auth::user();
        $model_id = null;

        if (is_array($oldValues) && isset($oldValues["id"])) {
            $model_id = $oldValues["id"];
        } else if (is_array($newValues) && isset($newValues["id"])) {
            $model_id = $newValues["id"];
        }

        $timezone = null;
        try {
            $timezone = !empty($_COOKIE['timezone']) ? Str::limit($_COOKIE['timezone'], 30, '') : null;
        } catch (Exception $e) {
            Log::error("Error getting timezone: " . $e->getMessage());
        }

        if (empty($ip_address)) {
            $ip_address = !empty($_COOKIE['ip']) ? Str::limit($_COOKIE['ip'], 45, '') : 'Unknown';
            Log::info("Got IP From Cookie: $ip_address");
        }

        $lang = null;
        try {
            $lang = !empty($_COOKIE['lang']) ? Str::limit($_COOKIE['lang'], 30, '') : null;
        } catch (Exception $e) {
            Log::error("Error getting language: " . $e->getMessage());
        }

        $oldValues = json_encode($oldValues, JSON_PRETTY_PRINT);
        $newValues = json_encode($newValues, JSON_PRETTY_PRINT);

        $audit = new Audit();
        $audit->user_id = $user ? $user->id : null;
        $audit->action = $action;
        $audit->model = $model;
        $audit->model_id = $model_id;
        $audit->old_values = $oldValues ? Str::limit($oldValues, 65531) : null;
        $audit->new_values = $newValues ? Str::limit($newValues, 65531) : null;
        $audit->ip_address = $ip_address;
        $audit->ip_country = $ip_country;
        $audit->user_agent = Str::limit($request->userAgent(), 255, '');
        $audit->timezone = $timezone;
        $audit->language = $lang;
        $audit->extra_info = $extraInfo ? Str::limit($extraInfo, 155, '') : null;

        $audit->save();

        Log::debug("Audit Registered: $action $model id($model_id) from $ip_address in country $ip_country");
    }
}
