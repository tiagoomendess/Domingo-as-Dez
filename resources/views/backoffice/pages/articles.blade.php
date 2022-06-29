@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.articles') }}</title>
@endsection

@section('content')
    <div class="row no-margin-bottom">
        <div class="col s8">
            <h1>{{ trans('models.articles') }}</h1>
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
            @if(!$articles || $articles->count() == 0)
                <p class="flow-text">{{ trans('models.no_articles') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.title') }}</th>
                        <th></th>
                        <th>{{ trans('general.date') }}</th>
                    </tr>
                    </thead>

                    @foreach($articles as $article)
                        <tr>
                            <td>
                                <a href="{{ route('articles.show', ['article' => $article]) }}">{{ $article->title }}</a>
                            </td>
                            <td>
                                <!-- Modal Trigger -->
                                @if (empty($article->facebook_post_id))
                                    <a class="blue darken-3 waves-effect waves-light btn modal-trigger"
                                       style="padding: 0 12px"
                                       href="#post-on-facebook-for-{{ $article->id }}">f</a>
                                @else
                                    <a class="waves-effect waves-light btn modal-trigger disabled"
                                       style="padding: 0 12px"
                                       href="#post-on-facebook-for-{{ $article->id }}">f</a>
                            @endif
                            <!-- Modal Structure -->
                                <div id="post-on-facebook-for-{{ $article->id }}" class="modal">
                                    <div class="modal-content">
                                        <h4 class="center">Publicar artigo no Facebook</h4>
                                        <p class="center">A mensagem não é obrigatória e só é possível publicar uma
                                            vez.</p>
                                        <form action="{{ route('articles.post_on_facebook', ['article' => $article->id]) }}"
                                              method="POST">
                                            {{ csrf_field() }}
                                            <div class="row">
                                                <div class="input-field col s12 m8 offset-m2">
                                                    <textarea autocomplete="off" name="message"
                                                              id="message_for_post_{{ $article->id }}" type="text"
                                                              data-length="144"
                                                              class="validate materialize-textarea"></textarea>
                                                    <label class="active" for="message_for_post_{{ $article->id }}">Mensagem</label>
                                                </div>

                                                <div class="input-field col s12 center">
                                                    <button onclick="handleFacePostClick()" type="submit"
                                                            class="green darken-3 waves-effect waves-light btn"><i
                                                                class="material-icons right">send</i>Publicar
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="javascript:void(0)"
                                           class="modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
                                    </div>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::createFromTimeString($article->created_at)->format("d/m/Y")}}</td>
                        </tr>

                    @endforeach
                </table>

                <div class="row">
                    <div class="col s12">
                        {{ $articles->links() }}
                    </div>
                </div>

            @endif
        </div>
    </div>

    @if(Auth::user()->hasPermission('articles.edit'))
        <div class="fixed-action-btn">
            <a class="btn-floating btn-large green waves-effect" href="{{ route('articles.create') }}">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endif

@endsection

@section('scripts')
    <script>
        const handleFacePostClick = () => {
            $('button').addClass('disabled');
            $('.modal-close').addClass('disabled')
        }

        $(document).ready(function () {
            $('.modal').modal({
                dismissible: false,
                opacity: .5,
                inDuration: 150,
                outDuration: 75,
                startingTop: '4%',
                endingTop: '10%',
            });
        })

    </script>
@endsection