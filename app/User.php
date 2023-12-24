<?php

namespace App;

use App\Notifications\MyResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

class User extends Authenticatable
{
    use Notifiable;

    public const SEARCH_FIELDS = [
        'name' => [
            'name' => 'name',
            'type' => 'string',
            'trans' => 'Nome',
            'allowSearch' => true,
            'compare' => 'like',
            'validation' => 'nullable|min:3|max:30|string'
        ],
        'email' => [
            'name' => 'email',
            'type' => 'string',
            'trans' => 'Email',
            'allowSearch' => true,
            'compare' => '=',
            'validation' => 'nullable|string|min:6|max:255'
        ],
        'created_at' => [
            'name' => 'created_at',
            'type' => 'date',
            'trans' => 'Data de Criação',
            'allowSearch' => false
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'type' => 'date',
            'trans' => 'Ultima Atualização',
            'allowSearch' => false
        ]
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'verified', 'email_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     *  The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'settings' => 'array'
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function delete_requests()
    {
        return $this->hasMany(DeleteRequest::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function bans()
    {
        return $this->hasMany('App\UserBan', 'banned_user_id');
    }

    public function bansGiven()
    {
        return $this->hasMany('App\UserBan', 'banned_by_user_id');
    }

    public function socialProviders()
    {
        return $this->hasMany('App\SocialProvider');
    }

    public function article_comments()
    {
        return $this->hasMany(ArticleComment::class);
    }

    public function userUuids()
    {
        return $this->hasMany(UserUuid::class);
    }

    public function hasPermission($permission)
    {

        $permissions = $this->permissions;

        foreach ($permissions as $perm) {
            if ($perm->name == $permission || $perm->name == 'admin')
                return true;
        }

        return false;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MyResetPasswordNotification($token));
    }

    public function isBanned()
    {

        //Get all bans
        $bans = $this->bans;

        if (count($bans) > 0) {

            foreach ($bans as $ban) {

                //If there is as ban not pardoned
                if (!$ban->pardoned)
                    return true;
            }

        } else {
            return false;
        }

        return false;
    }

    public function isSocial(): bool
    {
        return count($this->socialProviders) > 0 ? true : false;
    }

    public static function search(array $parameters)
    {
        $standardRules = [
            'order' => 'required|string|in:ascend,descend',
            'orderBy' => 'required|string|min:2|max:20',
            'search' => 'required|string|in:true,false'
        ];

        foreach (static::SEARCH_FIELDS as $searchField) {
            if (isset($searchField['validation']))
                $modelRules[$searchField['name']] = $searchField['validation'];
        }

        $validator = Validator::make($parameters, array_merge($standardRules, $modelRules));

        if ($validator->fails())
            return new LengthAwarePaginator([], 0, 1);

        $order = $parameters['order'] === 'descend' ? 'desc' : 'asc';
        $orderBy = array_key_exists($parameters['orderBy'], static::SEARCH_FIELDS) ? $parameters['orderBy'] : 'id';

        unset($parameters['order']);
        unset($parameters['orderBy']);
        unset($parameters['_token']);
        unset($parameters['search']);

        foreach ($parameters as $key => $parameter) {
            if (empty($parameter))
                unset($parameters[$key]);
        }

        $whereClause = [];
        foreach ($parameters as $key => $param) {
            if (static::SEARCH_FIELDS[$key]['allowSearch']) {

                $param = static::SEARCH_FIELDS[$key]['compare'] === 'like' ? '%' . $param . '%' : $param;
                $whereClause[] = [
                    $key, static::SEARCH_FIELDS[$key]['compare'], $param
                ];
            }
        }
        $whereClause[] = ['verified', '=', 1];

        $results = static::where($whereClause)->orderBy($orderBy, $order)->paginate(config('custom.results_per_page'));

        return $results;
    }
}
