@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.users') }}</title>
@endsection

@section('content')
    <div class="row no-margin-bottom">
        <div class="col s8">
            <h1>{{ trans('models.users') }}</h1>
        </div>
        <div class="col s4">
            @include('backoffice.partial.search_box_btn')
        </div>
    </div>

    <div class="row no-margin-bottom">
        @include('backoffice.partial.search_box')
    </div>

    <div class="row">
        <div class="col s12 l8">
            <h4>{{ trans('general.all_users') }}</h4>
            <div class="divider"></div>
            @if(!$users || $users->count() == 0)
                <p class="flow-text">{{ trans('models.no_users') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>{{ trans('general.name') }}</th>
                        <th>{{ trans('general.email') }}</th>
                        <th>{{ trans('general.created_at') }}</th>
                    </tr>
                    </thead>

                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td><a href="{{ route('users.show', ['user' => $user]) }}">{{ $user->name }}</a></td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at }}</td>
                        </tr>

                    @endforeach

                </table>

            @endif
        </div>

        <div class="col s12 l4">
            <h4>{{ trans('general.admins') }}</h4>
            <div class="divider"></div>
            @if(!$admins || $admins->count() == 0)
                <p class="flow-text">{{ trans('models.no_admins') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.name') }}</th>
                    </tr>
                    </thead>

                    @foreach($admins as $admin)
                        <tr>
                            <td><a href="{{ route('users.show', ['user' => $admin]) }}">{{ $admin->name }}</a></td>
                        </tr>

                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $users->links() }}
        </div>
    </div>

@endsection