@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>Regras de Grupo</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>Regras de Grupo</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$group_rules || $group_rules->count() == 0)
                <p class="flow-text">Nenhuma regra de grupo encontrada.</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.id') }}</th>
                        <th>{{ trans('general.name') }}</th>
                        <th>Tipo</th>
                        <th>Promoções</th>
                        <th>Despromoções</th>
                        <th>{{ trans('general.created_at') }}</th>
                        <th>{{ trans('general.updated_at') }}</th>
                    </tr>
                    </thead>

                    @foreach($group_rules as $group_rule)

                        <tr>
                            <td>{{ $group_rule->id }}</td>
                            <td><a href="{{ route('group_rules.show', ['group_rule' => $group_rule]) }}">{{ $group_rule->name }}</a></td>
                            <td>{{ $group_rule->type ?? '-' }}</td>
                            <td>{{ $group_rule->promotes ?? '-' }}</td>
                            <td>{{ $group_rule->relegates ?? '-' }}</td>
                            <td>{{ $group_rule->created_at }}</td>
                            <td>{{ $group_rule->updated_at }}</td>
                        </tr>

                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $group_rules->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('group_rules.edit'))
        @include('backoffice.partial.add_model_button', ['route' => route('group_rules.create')])
    @endif

@endsection
