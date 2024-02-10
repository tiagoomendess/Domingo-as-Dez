@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>Lista de Exportações</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>Lista de Exportações</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <table class="responsive-table">
                <thead>
                <tr>
                    <th>Modelo</th>
                    <th>Nome</th>
                    <th>Estado</th>
                    <th>Mensagem</th>
                    <th>Ações</th>
                </tr>
                </thead>

                @foreach($exports as $export)
                    <tr>
                        <td>{{ $export->model }}</td>
                        <td>{{ $export->name }}</td>
                        <td>{{ $export->status }}</td>
                        <td>{{ $export->message }}</td>
                        <td>-</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
