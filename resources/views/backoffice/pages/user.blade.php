@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.user') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.user') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12 l4">
            <h4>{{ trans('models.user') }}</h4>
            <div class="divider"></div>
            <p class="flow-text">
                <b>{{ trans('general.id') }} : </b> {{ $user->id }} <br>
                <b>{{ trans('general.name') }} : </b> {{ $user->name }} <br>
                <b>{{ trans('general.verified') }} : </b> {{ $user->verified }} <br>
                <b>{{ trans('general.created_at') }} : </b> {{ $user->created_at }} <br>
                <b>{{ trans('general.updated_at') }} : </b> {{ $user->updated_at }} <br>
            </p>
        </div>

        <div class="col s12 l4">
            <h4>{{ trans('models.profile') }}</h4>
            <div class="divider"></div>
            <p class="flow-text">
                <img class="responsive-img" style="width: 100%" src="{{ $profile->picture }}" alt="">
                <b>{{ trans('general.bio') }} : </b> {{ $profile->bio }} <br>
                <b>{{ trans('general.phone') }} : </b> {{ $profile->phone }} <br>
            </p>
        </div>

        <div class="col s12 l4">
            <h4>{{ trans('models.permissions') }}</h4>
            <div class="divider"></div>

            @if(!isset($permissions) || count($permissions) < 1)
                <p class="flow-text">{{ trans('models.no_permissions') }}</p>
            @else
                <ul class="flow-text">
                    @foreach($permissions as $perm)
                        <li>{{ trans('permissions.' . $perm->name) }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    @include('backoffice.partial.model_options', ['edit_route' => route('users.edit', ['user' => $user])])


@endsection