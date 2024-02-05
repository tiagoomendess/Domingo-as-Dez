@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.partners') }}</title>
@endsection

@section('content')
    <div class="row no-margin-bottom">
        <div class="col s8">
            <h1>{{ trans('models.partners') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$partners || $partners->count() == 0)
                <p class="flow-text">{{ trans('models.no_partners') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.name') }}</th>
                        <th>Prioridade</th>
                        <th>{{trans('general.visible')}}</th>
                        <th>{{ trans('general.created_at') }}</th>
                        <th>Imagem</th>
                    </tr>
                    </thead>

                    @foreach($partners as $partner)
                        <tr>
                            <td>
                                <a href="{{ route('partners.show', ['partner' => $partner]) }}">
                                    <img style="height: 15px" src="{{ $partner->picture }}">
                                    {{ $partner->name }}
                                </a>
                            </td>
                            <td>{{ $partner->priority }}</td>
                            <td>{{ $partner->visible ? 'Sim' : 'Não' }}</td>
                            <td>{{ $partner->created_at }}</td>
                            <td><a class="waves-effect waves-light btn blue-grey" style="padding: 0; width: 36px" href="{{ route('partners.show_generate_image', ['partner' => $partner->id]) }}"><i class="material-icons">image</i></a></td>
                        </tr>

                    @endforeach
                </table>

                <div class="row">
                    <div class="col s12">
                        {{ $partners->links() }}
                    </div>
                </div>

            @endif
        </div>
    </div>

    @if(Auth::user()->hasPermission('partners.edit'))
        <div class="fixed-action-btn">
            <a class="btn-floating btn-large green waves-effect" href="{{ route('partners.create') }}">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endif

@endsection