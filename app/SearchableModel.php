<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

class SearchableModel extends BaseModel
{
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

        if ($validator->fails()) {
            return new LengthAwarePaginator([], 0, 1);
        }

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
                $column_name = str_starts_with($key, '_') ? substr($key, 1) : $key;
                $whereClause[] = [
                    $column_name, static::SEARCH_FIELDS[$key]['compare'], $param
                ];
            }
        }

        $results = static::where($whereClause)->orderBy($orderBy, $order)->paginate(100);

        return $results;
    }
}
