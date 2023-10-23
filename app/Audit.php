<?php


namespace App;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Audit extends SearchableModel {

    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
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
        'action' => [
            'name' => 'action',
            'type' => 'enum',
            'trans' => 'Ação',
            'allowSearch' => true,
            'compare' => '=',
            'validation' => 'nullable|in:view,create,update,delete,login,login_failed,logout,register,forgot_password,other',
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
            'validation' => 'nullable|in:Article,Club,Competition,Game,GameGroup,Goal,Player,Transfer,User',
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

        $user = Auth::user();

        $model_id = null;
        if (is_array($oldValues) && isset($oldValues["id"])) {
            $model_id = $oldValues["id"];
        } else if (is_array($newValues) && isset($newValues["id"])) {
            $model_id = $newValues["id"];
        }

        $oldValues = json_encode($oldValues, JSON_PRETTY_PRINT);
        $newValues = json_encode($newValues, JSON_PRETTY_PRINT);

        $audit = new Audit();
        $audit->user_id = $user ? $user->id : null;
        $audit->action = $action;
        $audit->model = $model;
        $audit->model_id = $model_id;
        $audit->old_values = $oldValues ? str_limit($oldValues, 65531) : null;
        $audit->new_values = $newValues ? str_limit($newValues, 65531) : null;
        $audit->ip_address = $_COOKIE['ip'] ?? request()->getClientIp();
        $audit->user_agent = request()->userAgent();
        $audit->timezone = $_COOKIE['timezone'] ?? null;
        $audit->language = $_COOKIE['lang'] ?? null;
        $audit->extra_info = $extraInfo;

        $audit->save();
    }
}