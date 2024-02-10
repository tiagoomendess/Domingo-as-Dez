<?php

namespace App\Http\Controllers\Backoffice;

use App\Article;
use App\DataExport;

use App\Http\Controllers\Controller;
use App\Player;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    private $available = [
        'Article' => Article::SEARCH_FIELDS,
        'Player' => Player::SEARCH_FIELDS,
    ];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:export');
    }

    public function create(Request $request)
    {
        $modelToExport = $request->query('model');
        if (empty($modelToExport)) {
            return redirect()->back()->withErrors(['model' => 'Model is required']);
        }

        if (!isset($this->available[$modelToExport])) {
            return redirect()->back()->withErrors(['model' => 'Model is not available']);
        }

        $fields = $this->available[$modelToExport];
        $default_start_date = \Carbon\Carbon::now()->subDays(30)->format("Y-m-d");
        $default_end_date = \Carbon\Carbon::now()->format("Y-m-d");

        return view('backoffice.pages.create_export', [
            'searchFields' => $fields,
            'model' => $modelToExport,
            'default_start_date' => $default_start_date,
            'default_end_date' => $default_end_date,
        ]);
    }

    public function list()
    {
        $user_id = Auth::user()->id;
        $exports = DataExport::where('user_id', $user_id)->orderBy('id', 'desc')->paginate(config('custom.results_per_page'));

        return view('backoffice.pages.list_exports')->with(['exports' => $exports]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'model' => 'required|string',
            'created_at_start' => 'required|date',
            'created_at_end' => 'required|date',
            'order' => 'required|string|in:ascend,descend',
            'orderBy' => 'required|string|min:2|max:20',
            'search' => 'required|string|in:true,false',
            'same_file' => 'required|string|in:true,false',
        ]);

        if (!isset($this->available[$request->model])) {
            return redirect()->back()->withErrors(['model' => 'Model is not available']);
        }

        $searchFields = $this->available[$request->model];
        $query = $this->buildQueryFilters($request->all(), $searchFields);
        $order = $request->input('order') === 'descend' ? 'desc' : 'asc';
        $orderBy = $request->input('orderBy');
        $model = $request->input('model');
        $user_id = Auth::user()->id;
        $now = Carbon::now();
        $name = "$model " . $now->format("YmdHis") . "-$user_id";
        $sameFile = $request->input('same_file') === 'true';

        $data_export = DataExport::create([
            'name' => $name,
            'model' => $model,
            'fields' => '',
            'query' => json_encode($query),
            'order_by' => $orderBy,
            'order_direction' => $order,
            'format' => 'csv',
            'status' => 'pending',
            'message' => 'A aguardar processamento',
            'file_path' => '',
            'same_file' => $sameFile,
            'user_id' => $user_id,
        ]);

        //$data_export->save();

        return redirect()->route('export.list');
    }

    private function buildQueryFilters($parameters, $searchFields) {
        foreach ($searchFields as $searchField) {
            if (isset($searchField['validation']))
                $modelRules[$searchField['name']] = $searchField['validation'];
        }

        $validator = Validator::make($parameters, $modelRules);
        if ($validator->fails()) {
            return new LengthAwarePaginator([], 0, 1);
        }

        $whereClause[] = [
            'created_at', '>=', $parameters['created_at_start'] . " 00:00:00",
        ];
        $whereClause[] = [
            'created_at', '<=', $parameters['created_at_end'] . " 23:59:59",
        ];

        foreach ($parameters as $key => $parameter) {
            if (empty($parameter))
                unset($parameters[$key]);
        }

        foreach ($parameters as $key => $param) {
            if (!isset($searchFields[$key]))
                continue;

            if ($searchFields[$key]['allowSearch']) {
                $param = $searchFields[$key]['compare'] === 'like' ? '%' . $param . '%' : $param;
                $column_name = str_starts_with($key, '_') ? substr($key, 1) : $key;
                $whereClause[] = [
                    $column_name, $searchFields[$key]['compare'], $param
                ];
            }
        }

        return $whereClause;
    }
}
