@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.competitions') }}</title>
    <link rel="stylesheet" href="/css/front/competitions-styles.css">

    <meta property="og:title" content="{{ trans('models.competitions') . ' - ' . config('app.name') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="{{ trans('front.footer_desc') }}" />

@endsection

@section('content')
    <div class="container">
        <h1 class="hide-on-med-and-down">{{ trans('models.competitions') }}</h1>

        <div class="card">
            <div class="card-content">
                <ul class="list-a competitions-list">
                    @foreach($competitions as $competition)
                        <li>
                            <a href="{{ $competition->getPublicUrl() }}">
                                <img src="{{ $competition->getPicture() }}" alt="{{$competition->name}}">
                                <span class="flow-text">{{ $competition->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
@endsection