@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.permissions') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.permissions') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$permissions || $permissions->count() == 0)
                <p class="flow-text">{{ trans('models.no_permissions') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.id') }}</th>
                        <th>{{ trans('general.name') }}</th>
                        <th>{{ trans('general.description') }}</th>
                    </tr>
                    </thead>

                    @foreach($permissions as $permission)

                        <tr>
                            <td>{{ $permission->id }}</td>
                            <td><a href="{{ route('permissions.show', ['permission' => $permission]) }}">{{ $permission->name }}</a></td>
                            <td>{{ trans('permissions.' . $permission->name) }}</td>
                        </tr>

                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">

            {{ $permissions->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('admin'))
        <div class="fixed-action-btn">
            <a class="btn-floating btn-large green waves-effect" href="{{ route('permissions.create') }}">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endif

@endsection