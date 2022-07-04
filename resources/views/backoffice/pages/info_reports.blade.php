@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.info_reports') }}</title>
@endsection

@section('content')
    <div class="row no-margin-bottom">
        <div class="col s8">
            <h1>{{ trans('models.info_reports') }}</h1>
        </div>
        <div class="col s4">
            @include('backoffice.partial.search_box_btn')
        </div>
    </div>

    <div class="row no-margin-bottom">
        @include('backoffice.partial.search_box')
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$infos || $infos->count() == 0)
                <p class="flow-text">{{ trans('models.no_info_reports') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>Info</th>
                        <th>{{trans('general.status')}}</th>
                        <th>{{ trans('general.created_at') }}</th>
                    </tr>
                    </thead>

                    @foreach($infos as $info)
                        <tr>
                            <td><a href="{{ route('info_reports.edit', ['id' => $info->id]) }}">{{ str_limit($info->content, 30) }}</a></td>
                            <td>{{ trans('front.info_report_status_' . $info->status) }}</td>
                            <td>
                                {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $info->created_at)->timezone('Europe/Lisbon')->format("d/m/Y H:i") }}
                            </td>
                        </tr>
                    @endforeach
                </table>

                <div class="row">
                    <div class="col s12">
                        {{ $infos->links() }}
                    </div>
                </div>

            @endif
        </div>
    </div>
@endsection