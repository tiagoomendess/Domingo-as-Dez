@extends('front.layouts.default-page')

@section('title')
    {{ config('custom.site_name') }}
@endsection

@section('content')
    <h1>Home Page</h1>
    <p>Isto será a home page do site. Vai ter um resumo do que se está a passar no mundo do futebol, entre artigos, proximos jogos e resultados.</p>
@endsection