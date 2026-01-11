@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>Comentários de Flash Interview</title>
    <style>
        .game-comments-table {
            width: 100%;
        }
        .game-comments-table td {
            padding: 16px 12px;
            vertical-align: top;
        }
        .match-info {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: #1976d2;
        }
        .team-names {
            font-size: 0.9rem;
            color: #666;
            margin-top: 8px;
        }
        .comment-content {
            font-size: 1rem;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
        }
        .comment-team {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .comment-team.home {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .comment-team.away {
            background-color: #fff3e0;
            color: #f57c00;
        }
        .date-info {
            font-size: 0.85rem;
            color: #666;
        }
        .date-label {
            font-weight: 600;
            color: #424242;
        }
        .comment-id {
            font-weight: 600;
            color: #1976d2;
            font-size: 1rem;
        }
        thead th {
            background-color: #f5f5f5;
            font-weight: 600;
            color: #424242;
        }
        .btn-copy-link {
            padding: 4px 10px;
            font-size: 0.8rem;
            margin-top: 8px;
        }
        .btn-copy-link i {
            font-size: 1rem;
        }
    </style>
@endsection

@section('content')
    <div class="row no-margin-bottom">
        <div class="col s12">
            <h1>Comentários de Flash Interview</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$gameComments || $gameComments->count() == 0)
                <p class="flow-text">Não existem comentários de flash interview.</p>
            @else
                <table class="bordered game-comments-table">
                    <thead>
                    <tr>
                        <th style="width: 300px;">Jogo</th>
                        <th>Comentário</th>
                        <th style="width: 180px;">Datas</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($gameComments as $comment)
                        <tr>
                            <td>
                                <div class="match-info">
                                    {{ $comment->game->homeTeam->club->name }} vs {{ $comment->game->awayTeam->club->name }}
                                </div>
                                
                                <div>
                                    @if($comment->team_id == $comment->game->home_team_id)
                                        <span class="comment-team home">
                                            {{ $comment->team->club->name }}
                                        </span>
                                    @else
                                        <span class="comment-team away">
                                            {{ $comment->team->club->name }}
                                        </span>
                                    @endif
                                </div>
                                

                                <div style="margin-top: 8px;">
                                    <button type="button" 
                                            class="btn waves-effect btn-flat"
                                            onclick="copyLink('{{ route('front.game_comment', ['uuid' => $comment->uuid]) }}?pin={{ $comment->pin }}')"
                                            title="Copiar link da Flash Interview">
                                            <i class="material-icons right" style="margin-left: 0;">content_copy</i>
                                    </button>
                                </div>
                            </td>
                            
                            <td>
                                <div class="comment-content">
                                    {{ $comment->content }}
                                </div>
                            </td>
                            
                            <td>
                                <div class="date-info">
                                    <span class="date-label">Criado:</span><br>
                                    {{ $comment->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="date-info" style="margin-top: 8px;">
                                    <span class="date-label">Atualizado:</span><br>
                                    {{ $comment->updated_at->format('d/m/Y H:i') }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="row">
                    <div class="col s12">
                        {{ $gameComments->links() }}
                    </div>
                </div>

            @endif
        </div>
    </div>

    <script>
        function showToast(message, isSuccess) {
            if (typeof M !== 'undefined' && M.toast) {
                M.toast({html: message, classes: isSuccess ? 'green' : 'red'});
            } else {
                alert(message);
            }
        }

        function copyLink(url) {
            navigator.clipboard.writeText(url).then(function() {
                showToast('Link copiado!', true);
            }).catch(function(err) {
                // Fallback for older browsers
                var textArea = document.createElement("textarea");
                textArea.value = url;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    showToast('Link copiado!', true);
                } catch (err) {
                    showToast('Erro ao copiar link', false);
                }
                document.body.removeChild(textArea);
            });
        }
    </script>
@endsection

