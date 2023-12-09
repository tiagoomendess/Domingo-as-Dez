@extends('errors.master-error')

@section('head-content')
    <title>{{trans('errors.http_503_title')}}</title>
@endsection

@section('content')
    <div class="error-wrapper">
        <div class="center-message">
            <h1>{{ trans('errors.http_503') }}</h1>
            <span class="details">
                {{ trans('errors.http_503_details') }}
            </span>
            <div class="actions">
                <a onclick="reloadPage()" class="btn-flat waves-effect waves-light" href="javascript:void(0);">{{ trans('errors.http_503_update_page') }}</a>
            </div>
        </div>
    </div>

    <script>
        const reloadPage = () => {
            window.location.reload();
        }
    </script>
@endsection
