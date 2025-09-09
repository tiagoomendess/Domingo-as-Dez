<?php

namespace App\Http\Controllers;

use App\Audit;
use App\GroupRules;
use App\GroupRulesPosition;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class GroupRulesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:group_rules.edit')->only(['edit', 'update']);
        $this->middleware('permission:group_rules.create')->only(['create', 'store']);
        $this->middleware('permission:group_rules');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $group_rules = GroupRules::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));
        return view('backoffice.pages.group_rules', ['group_rules' => $group_rules]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backoffice.pages.create_group_rule');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'string|max:154|required',
            'type' => 'string|max:154|nullable',
            'promotes' => 'integer|nullable',
            'relegates' => 'integer|nullable',
            'tie_breaker_script' => 'string|nullable',
            'positions' => 'array|nullable',
            'positions.*.positions' => 'string|required_with:positions.*',
            'positions.*.color' => 'string|required_with:positions.*',
            'positions.*.label' => 'string|required_with:positions.*',
        ]);

        $group_rule = GroupRules::create([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'promotes' => $request->input('promotes'),
            'relegates' => $request->input('relegates'),
            'tie_breaker_script' => $request->input('tie_breaker_script'),
        ]);

        // Handle positions
        if ($request->has('positions')) {
            foreach ($request->input('positions') as $position_data) {
                if (!empty($position_data['positions']) && !empty($position_data['color']) && !empty($position_data['label'])) {
                    GroupRulesPosition::create([
                        'group_rules_id' => $group_rule->id,
                        'positions' => $position_data['positions'],
                        'color' => $position_data['color'],
                        'label' => $position_data['label'],
                    ]);
                }
            }
        }

        Audit::add(Audit::ACTION_CREATE, 'GroupRules', null, $group_rule->toArray());

        return redirect()->route('group_rules.show', ['group_rule' => $group_rule]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group_rule = GroupRules::with('positions')->findOrFail($id);
        return view('backoffice.pages.edit_group_rule', ['group_rule' => $group_rule]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $group_rule = GroupRules::with('positions')->findOrFail($id);
        return view('backoffice.pages.edit_group_rule', ['group_rule' => $group_rule]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $group_rule = GroupRules::findOrFail($id);
        $old_group_rule = $group_rule->toArray();

        $request->validate([
            'name' => 'string|max:154|required',
            'type' => 'string|max:154|nullable',
            'promotes' => 'integer|nullable',
            'relegates' => 'integer|nullable',
            'tie_breaker_script' => 'string|nullable',
            'positions' => 'array|nullable',
            'positions.*.positions' => 'string|required_with:positions.*',
            'positions.*.color' => 'string|required_with:positions.*',
            'positions.*.label' => 'string|required_with:positions.*',
        ]);

        $group_rule->name = $request->input('name');
        $group_rule->type = $request->input('type');
        $group_rule->promotes = $request->input('promotes');
        $group_rule->relegates = $request->input('relegates');
        $group_rule->tie_breaker_script = $request->input('tie_breaker_script');

        $group_rule->save();

        // Handle positions - delete existing and recreate
        $group_rule->positions()->delete();
        
        if ($request->has('positions')) {
            foreach ($request->input('positions') as $position_data) {
                if (!empty($position_data['positions']) && !empty($position_data['color']) && !empty($position_data['label'])) {
                    GroupRulesPosition::create([
                        'group_rules_id' => $group_rule->id,
                        'positions' => $position_data['positions'],
                        'color' => $position_data['color'],
                        'label' => $position_data['label'],
                    ]);
                }
            }
        }

        Audit::add(Audit::ACTION_UPDATE, 'GroupRules', $old_group_rule, $group_rule->toArray());

        return redirect()->route('group_rules.show', ['group_rule' => $group_rule]);
    }
}
