@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.competitions') }}</title>
    <link rel="stylesheet" href="/css/front/competitions-styles.css">
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
                                <img src="{{ $competition->picture }}" alt="{{$competition->name}}">
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