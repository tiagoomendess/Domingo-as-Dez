@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ config('custom.site_name') }}</title>
@endsection

@section('content')
    <div class="row">

        <div class="col s12 m4 l4">
            <div class="card" id="edit-profile-pic" style="margin-bottom: 0">
                <div class="card-image">
                    <div>
                        <i class="material-icons">add_a_photo</i>
                    </div>
                    <img src="{{ $user->profile->getPicture() }}" alt="">
                </div>
            </div>
            <div id="progress-bar" class="progress hide blue" style="margin-top: 0">
                <div class="determinate blue" style="width: 50%"></div>
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
            <div class="card">
                <div class="card-content">

                    <form action="{{route('front.userprofile.update')}}" method="POST">

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

                        <div class="row">
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

        <div class="col s12 m8 l8 offset-m4 offset-l4">
            <div class="card">
                <div class="card-content">
                    @if(count($user->permissions) > 0)
                        <p class="flow-text">{{ trans('front.has_permissions') }}</p>

                        <ul>
                            @foreach($user->permissions as $permission)
                                <li>{{ trans('permissions.' . $permission->name) }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="flow-text">{{ trans('front.no_permissions') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script>

        $(document).ready(function(){

            $('#edit-profile-pic').click(function () {
                $('#edit-profile-pic-modal').modal('open');
            });

            $('#edit-profile-pic-modal').modal();

            $('#edit-profile-pic-btn').click(function () {
                $('#edit-profile-pic-modal').modal('close');
            });

            $('#edit-profile-pic-form').on('submit', function (event) {

                event.preventDefault();

                $('#progress-bar').addClass('determinate');
                $('#progress-bar').attr('style', 'width: 1%');
                $('#progress-bar').removeClass('hide');
                
                var formData = new FormData($('#edit-profile-pic-form')[0]);

                $.ajax({
                    xhr : function () {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function (ev) {

                            if (ev.lengthComputable) {

                                var percent = Math.round((ev.loaded / ev.total) * 100);

                                $('#progress-bar').attr('style', 'width: ' + percent + '%');
                            }
                        });

                        return xhr;
                    },
                    type : 'POST',
                    data : formData,
                    url : $('#edit-profile-pic-form').attr('action'),
                    processData : false,
                    contentType : false,
                    success : function () {

                        $('#progress-bar').removeClass('determinate');
                        $('#progress-bar').addClass('indeterminate');

                        var url = window.location.href;

                        $.get(url, function (data) {

                            $('#edit-profile-pic div img').attr('src', $(data).find('#edit-profile-pic div img').attr('src').trim());
                            $('li .dropdown-button .navbar-profile-pic').attr('src', $(data).find('#edit-profile-pic div img').attr('src').trim());

                        });

                        $('#progress-bar').addClass('hide');
                    },
                    error : function () {
                        $('#progress-bar').addClass('hide');
                        alert('Erro ao carregar imagem. Perfil não foi alterado!');
                    }
                });
            });

        });
    </script>

@endsection