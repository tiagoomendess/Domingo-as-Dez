@extends('backoffice.layouts.default-page')

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>Dashboard</h1>
            <p class="flow-text">Isto é a página inicial do painel de controlo do website.
                No menu lateral encontra as opções a que tem permissão.
                Não consegue ver nada no menu é porque não possui permissões para fazer nada.</p>
        </div>
        <div class="col s12">
            <h4>Tutoriais:</h4>
            <p class="flow-text">Isto é uma playlist de vídeos a explicar como realizar algumas tarefas no website.
                Consoante as permissões associadas à sua conta,
                poderá não conseguir visualizar todas as opções que aparecem nos vídeos. Se preferir pode visualizar os
                vídeos diretamente no youtube clicando no link:
                <a href="https://www.youtube.com/watch?v=iuVF45lBRxY&list=PL_GkJl6tG6PckYIYCDM5RtLCc38L63lQk">https://www.youtube.com/watch?v=iuVF45lBRxY&list=PL_GkJl6tG6PckYIYCDM5RtLCc38L63lQk</a>
            </p>
            <iframe width="853" height="480"
                    src="https://www.youtube.com/embed/videoseries?list=PL_GkJl6tG6PckYIYCDM5RtLCc38L63lQk"
                    frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
        </div>

        @if(has_permission('admin'))
            <div class="col s12 m8 l6">
                <h4>Utilizadores com Permissões:</h4>
                <div class="collection">
                    @foreach($usersWithPermissions as $user)
                        <a class="collection-item"
                           href="{{ route('users.show', ['user' => $user->id]) }}">{{ $user->name }}</a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection