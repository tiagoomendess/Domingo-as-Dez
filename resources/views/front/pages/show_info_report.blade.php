@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('front.show_info_report_title') }}</title>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col s12 hide-on-med-and-down">
                <h1>{{ trans('front.show_info_report_title') }}</h1>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                @if(empty($info))
                    <div class="col s12 center">
                        <p class="flow-text">Não encontramos nenhuma informação com o código {{ $code }}</p>
                    </div>
                @else
                    @include('front.partial.info_report', ['info' => $info])
                @endif

                <div class="center">
                    <a href="{{ route('info.create') }}" class="waves-effect waves-light btn blue">Voltar</a>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        setTimeout(() => {
            $(document).ready(function(){
                $('#apagar_info_modal').modal();
            });
        }, 100)
    </script>
@endsection
