@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{trans('general.edit')}} {{ trans('models.user') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{trans('general.edit')}} {{ trans('models.user') }}</h1>
        </div>
    </div>

    <div class="row">

        <div class="col s12 l4">
            <h4>{{ trans('models.user') }}</h4>
            <div class="divider"></div>
            <br>

            <div class="row">
                <div class="input-field col s12">
                    <input disabled value="{{ $user->id }}" id="user_id" type="number" class="validate">
                    <label for="user_id">{{ trans('models.id') }}</label>
                </div>

                <div class="input-field col s12">
                    <input disabled value="{{ $user->name }}" id="user_name" type="text" class="validate">
                    <label for="user_name">{{ trans('models.name') }}</label>
                </div>

                <div class="input-field col s12">
                    <input disabled value="{{ $user->email }}" id="email" type="text" class="validate">
                    <label for="email">{{ trans('models.email') }}</label>
                </div>

                <div class="row">
                    <div class="col s12">
                        <a class="waves-effect waves-light btn red modal-trigger" href="#ban_modal"><i class="material-icons right">gavel</i>{{ trans('general.ban') }}</a>
                    </div>
                </div>

                @include('backoffice.partial.ban_modal')

            </div>


        </div>

        <div class="col s12 l4">
            <h4>{{ trans('models.profile') }}</h4>
            <div class="divider"></div>

            <form action="{{ route('users_profile.update', ['profile' => $profile]) }}" method="POST">

                {{ csrf_field() }}

                {{ method_field('PUT') }}
                <br>

                <div class="row">
                    <div class="input-field col s12">
                        <input name="picture" placeholder="{{ trans('general.url') }}" id="picture" type="text" class="validate" value="{{ $profile->picture }}">
                        <label for="picture">{{ trans('models.picture') }}</label>
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12">
                        <input name="phone" id="phone" type="text" class="validate" value="{{ $profile->phone }}">
                        <label for="phone">{{ trans('general.phone') }}</label>
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12">
                        <textarea name="bio" id="bio" class="materialize-textarea" data-length="280">{{ $profile->bio }}</textarea>
                        <label for="bio">{{ trans('models.bio') }}</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col s12">
                        @include('backoffice.partial.save_button')
                    </div>
                </div>


            </form>
        </div>

        <div class="col s12 l4">
            <h4>{{ trans('models.permissions') }}</h4>
            <div class="divider"></div>

            @if(!isset($permissions) || count($permissions) < 1)
                <p class="flow-text">{{ trans('models.no_permissions') }}</p>
            @else
                <table>
                    @foreach($permissions as $perm)
                        <tr>
                            <td>

                            </td>

                            <td>

                            </td>
                        </tr>
                    @endforeach
                </table>


            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){

            $('#ban_modal').modal();
        });
    </script>
@endsection