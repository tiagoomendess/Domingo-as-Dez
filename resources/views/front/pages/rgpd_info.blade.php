@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('front.rgpd_long') }}</title>
    <link rel="stylesheet" href="/css/front/rgpd-styles.css">
@endsection

@section('content')
    <div class="container">
        <h1 class="hide-on-med-and-down">{{ trans('front.rgpd_long') }}</h1>

        <div class="card">
            <div class="card-content">
                <p class="flow-text">{{ trans('front.rgpd_info_p1') }}</p>

                <p class="flow-text">{{ trans('front.rgpd_info_p2') }}</p>

                <p class="flow-text">{{ trans('front.rgpd_info_p3') }}</p>
            </div>
        </div>

        <ul class="collapsible" data-collapsible="accordion">
            <li>
                <div class="collapsible-header"><i class="material-icons">assignment_turned_in</i>{{ trans('front.rgpd_info_consent') }}</div>
                <div class="collapsible-body">

                    <form action="{{ route('rgpd_info.settings') }}" method="POST">
                        {{ csrf_field() }}
                        <ul class="list-normal">
                            <li>
                                <div class="settings-item">
                                    <div class="info">
                                        <span class="name">{{ trans('front.rgpd_analytics_cookies') }}</span>
                                        <span class="desc">{{ trans('front.rgpd_analytics_cookies_desc') }}</span>
                                    </div>

                                    <div class="value" id="rgpd_analytics_cookies_switch">
                                        <div class="switch">
                                            <label>
                                                Off
                                                <input name="rgpd_analytics_cookies_switch" type="hidden" value="false">
                                                @if ($rgpd_analytics_cookies)
                                                    <input name="rgpd_analytics_cookies_switch" type="checkbox" value="true" checked>
                                                @else
                                                    <input name="rgpd_analytics_cookies_switch" type="checkbox" value="true">
                                                @endif
                                                <span class="lever"></span>
                                                On
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            </li>

                            @if(Auth::check())
                                <li>
                                    <div class="settings-item">
                                        <div class="info">
                                            <span class="name">{{ trans('front.rgpd_user_data_collect') }}</span>
                                            <span class="desc">{{ trans('front.rgpd_user_data_collect_desc') }}</span>
                                        </div>

                                        <div class="value" id="rgpd_user_data_collect_switch">
                                            <div class="switch">
                                                <label>
                                                    Off
                                                    <input type="checkbox" checked>
                                                    <span class="lever"></span>
                                                    On
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="settings-item">
                                        <div class="info">
                                            <span class="name">{{ trans('front.rgpd_all_data_collect') }}</span>
                                            <span class="desc">{{ trans('front.rgpd_all_data_collect_desc') }} {{ Auth::user()->profile->all_data_consent }}</span>
                                        </div>

                                        <div class="value" id="rgpd_all_data_collect_switch">
                                            <div class="switch">
                                                <label>
                                                    Off
                                                    <input name="rgpd_all_data_collect_switch" type="hidden" value="false">
                                                    @if ($rgpd_all_data_collect)
                                                        <input name="rgpd_all_data_collect_switch" type="checkbox" value="true" checked>
                                                    @else
                                                        <input name="rgpd_all_data_collect_switch" type="checkbox" value="true">
                                                    @endif

                                                    <span class="lever"></span>
                                                    On
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                </li>

                            @endif
                        </ul>

                        <div class="consents-submit-btn hide">
                            <button type="submit" class="btn green darken-2">{{ trans('general.save') }}</button>
                        </div>
                    </form>

                </div>
            </li>

            <li>
                <div class="collapsible-header"><i class="material-icons">accessibility_new</i>{{ trans('front.rgpd_info_right_access') }}</div>
                <div class="collapsible-body">
                    @if (!is_null($user_info))
                        <ul class="multi-list">
                            <li>
                                <span class="text-bold">{{ trans('models.user') }}</span>

                                <ul>
                                    <li><span class="text-bold">{{ trans('models.name') }}:</span> {{ $user_info->user->name }}</li>
                                    <li><span class="text-bold">{{ trans('models.email') }}:</span> {{ $user_info->user->email }}</li>
                                    <li><span class="text-bold">{{ trans('auth.password') }}:</span> {{ trans('front.dont_save_password') }}</li>
                                </ul>
                            </li>

                            <li>
                                <span class="text-bold">{{ trans('models.profile') }}</span>

                                <ul>
                                    <li><span class="text-bold">{{ trans('general.bio') }}:</span> {{ $user_info->profile->bio }}</li>
                                    <li><span class="text-bold">{{ trans('general.phone') }}:</span> {{ $user_info->profile->phone }}</li>
                                    <li><span class="text-bold">{{ trans('front.photograph') }}:</span> <a href="{{ $user_info->profile->picture }}">{{ $user_info->profile->picture }}</a></li>
                                </ul>
                            </li>

                            <li>
                                <span class="text-bold">{{ trans('front.other_identifiable_data') }}:</span>

                                <ul>
                                    @foreach($user_info->other as $index => $info)
                                        <li><span class="text-bold">{{ $index }}:</span> {{ $info }}</li>
                                    @endforeach

                                    @if(count($user_info->other) < 1)
                                        {{trans('general.none')}}
                                    @endif
                                </ul>
                            </li>
                        </ul>
                        @else
                        <span>{{ trans('front.rgpd_no_info_stored') }}</span>
                    @endif
                </div>
            </li>

            <li>
                <div class="collapsible-header"><i class="material-icons">file_copy</i>{{ trans('front.rgpd_info_portability') }}</div>
                <div class="collapsible-body">

                    <div class="all-centered">
                        <a href="{{ route('front.userprofile.download') }}" target="_blank" class="waves-effect waves-light btn blue darken-2"><i class="material-icons right">cloud_download</i>{{ trans('front.download') }}</a>
                    </div>

                </div>
            </li>

            <li>
                <div class="collapsible-header"><i class="material-icons">edit</i>{{ trans('front.rgpd_info_right_edit') }}</div>
                <div class="collapsible-body">
                    @if(Auth::check())
                        <div class="all-centered">
                            <a href="{{ route('front.userprofile.edit') }}" class="waves-effect waves-light btn green darken-2"><i class="material-icons right">edit</i>{{ trans('front.click_here') }}</a>
                        </div>
                    @else
                        <span>Não aplicável.</span>
                    @endif
                </div>
            </li>

            <li>
                <div class="collapsible-header"><i class="material-icons">delete_forever</i>{{ trans('front.rgpd_info_right_erasure') }}</div>
                <div class="collapsible-body">
                    @if(Auth::check())
                        <div class="all-centered">
                            <a href="{{ route('front.userprofile.edit') }}" class="waves-effect waves-light btn red darken-2"><i class="material-icons right">delete_forever</i>{{ trans('front.delete_account') }}</a>
                        </div>
                    @else
                        <span>Não aplicável.</span>
                    @endif
                </div>
            </li>
        </ul>
    </div>
@endsection

@section('scripts')
    <script src="/js/front/rgpd-scripts.js"></script>
@endsection