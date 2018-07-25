@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ config('custom.site_name') }}</title>
    <link rel="stylesheet" href="/css/front/edit_profile-style.css">
@endsection

@section('content')
    <div class="container">
        <div class="row">

            <div class="col s12 hide-on-med-and-down">
                <h1>{{trans('front.user_profile', ['name' => $user->name])}}</h1>
            </div>

            <div class="col s12 m4 l4">
                <h2 class="over-card-title">{{ trans('front.photograph') }}</h2>
                <div class="card" id="edit-profile-pic">
                    <div class="card-image">
                        <div class="hover-icon">
                            <i class="material-icons">add_a_photo</i>
                        </div>
                        <img src="{{ $user->profile->getPicture() }}" alt="">
                    </div>
                </div>
                <div id="progress-bar" class="progress hide blue">
                    <div class="determinate blue"></div>
                </div>

                <div id="edit-profile-pic-modal" class="modal">
                    <div class="modal-content">
                        <h4 class="center">{{ trans('front.edit_profile_pic') }}</h4>
                        <p class="flow-text center">{{ trans('front.edit_profile_pic_help') }}</p>
                        <div class="row">
                            <div class="col s12">
                                <div class="row">
                                    <form id="edit-profile-pic-form" action="{{ route('front.userprofilephoto.update') }}" method="POST" enctype="multipart/form-data">

                                        {{ csrf_field() }}

                                        <div class="file-field input-field col s10 offset-s1 center">
                                            <div class="btn blue">
                                                <span>{{ trans('front.choose_photo') }}</span>
                                                <input name="photo" type="file">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" type="text">
                                            </div>
                                        </div>

                                        <div class="col s10 offset-s1 center">
                                            <button id="edit-profile-pic-btn" class="btn waves-effect waves-light green darken-1" type="submit" name="action">{{ trans('front.change') }}
                                                <i class="material-icons right">send</i>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <div class="col s12 m8 l8">

                <h2 class="over-card-title">{{ trans('front.data') }}</h2>
                <div class="card">
                    <div class="card-content">

                        <form action="{{route('front.userprofile.update')}}" method="POST" id="user_data_form">

                            {{ csrf_field() }}

                            <div class="row">
                                <div class="input-field col s12 m6 l6">
                                    <input type="text" id="name" disabled value="{{ $user->name }}">
                                    <label for="name">{{ trans('models.name') }}</label>
                                </div>

                                <div class="input-field col s12 m6 l6">
                                    <input type="text" id="member_since" disabled value="{{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $user->created_at)->format("d/m/Y") }}">
                                    <label for="member_since">{{ trans('front.member_since') }}</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="input-field col s12 m6 l6">
                                    <input type="email" id="email" disabled value="{{ $user->email }}">
                                    <label for="email">{{ trans('general.email') }}</label>
                                </div>

                                <div class="input-field col s12 m6 l6">
                                    <input type="text" name="phone" id="phone" value="{{ $user->profile->phone }}" autocomplete="off">
                                    <label for="phone">{{ trans('general.phone') }}</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="input-field col s12">
                                    <textarea id="bio" name="bio" class="materialize-textarea" autocomplete="off">{{ $user->profile->bio }}</textarea>
                                    <label for="bio">{{ trans('general.bio') }}</label>
                                </div>
                            </div>

                            <div class="row hide" id="save_btn">
                                <div class="col s12">
                                    <button class="btn waves-effect waves-light green darken-2 right" type="submit" name="action">{{trans('general.save')}}
                                        <i class="material-icons right">save</i>
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')

    <script src="/js/front/edit_profile-scripts.js"></script>

@endsection