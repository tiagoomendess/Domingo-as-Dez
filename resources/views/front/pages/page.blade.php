@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ $page->title }}</title>
    <meta name="description" content="{{ $description }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $page->title }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="{{ route('page.show', ['slug' => $page->slug]) }}"/>
    @if($page->picture)
        <meta property="og:image" content="{{ url($page->picture) }}">
    @endif
    <meta property="og:description" content="{{ $description }}"/>

    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $page->title }}">
    <meta itemprop="description" content="{{ $description }}">
    @if($page->picture)
        <meta itemprop="image" content="{{ $page->picture }}">
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="container">
            <h1 class="hide-on-med-and-down">{{ $page->title }}</h1>
            {!! $page->body !!}
        </div>
    </div>

    <div class="row">
        <div class="container">
            <!-- Custom Pages -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-3518000096682897"
                 data-ad-slot="3386411476"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </div>
@endsection

@section('scripts')

@endsection