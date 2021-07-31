<div class="row">
    <div class="container" style="">
        <h3 style="width: 100%" class="over-card-title">Comentários</h3>
        <div class="comment hide" id="comment_template">
            <div class="comment-header">
                <img class="circle" src="/storage/uploads/users/avatars/sq21539613001mhj-mhm1xgxwf.jpg" alt="">
                <span class="text-bold">José Silva</span>
            </div>

            <div class="comment-content" style="width: auto">
                <comment class="flow-text">Isto é o comentário Isto é o</comment>
            </div>
        </div>

        <div style="margin: 0 0 10px 0">

            @if (count($comments))
                @foreach($comments as $comment)
                    <div class="comment">
                        <div class="comment-header">
                            <img class="circle" src="{{ $comment->user->profile->getPicture() }}" alt="">
                            <span class="text-bold">{{ $comment->user->name }}</span>
                        </div>

                        <div class="comment-content" style="width: auto">
                            <comment class="flow-text">{{ $comment->content }}</comment>
                        </div>

                        @foreach($comment->child_comments as $child_comment)
                            <div class="comment" style="margin-left: 40px">
                                <div class="comment-header">
                                    <img class="circle" src="{{ $child_comment->user->profile->getPicture() }}" alt="">
                                    <span class="text-bold">{{ $child_comment->user->name }}</span>
                                </div>

                                <div class="comment-content" style="width: auto">
                                    <comment class="flow-text">{{ $child_comment->content }}</comment>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <p class="flow-text text-center">Ainda não existem comentários</p>
            @endif

        </div>

        @if(\Illuminate\Support\Facades\Auth::user())
            <div class="row" id="new_comment">
                <div class="col s12">
                    <form style="background-color: #FFFFFF; box-shadow: #c3c3c3 0 2px 2px 2px; width: 100%; display: table;"
                          method="POST"
                          action="{{ route('api.article_comments.comment', ['article_id' => $article->id ]) }}"
                          class="">
                        <div class="" style="height: 7px; display: table-row;"></div>
                        <div class="" style="padding: 10px; display: table-row;">

                            <div class="" style="display: table-cell; width: 60px; vertical-align: middle;">
                                <img style="width: 40px; margin: 0 10px;" class="circle"
                                     src="/storage/uploads/users/avatars/sq21539613001mhj-mhm1xgxwf.jpg" alt="">
                            </div>

                            <div class="" style="display: table-cell; width: auto">
                                <div class="input-field">
                                    <input id="comment" type="text" class="validate">
                                    <label for="comment">Comentário</label>
                                </div>
                            </div>

                            <div class="" style="display: table-cell; width: 60px; vertical-align: middle;">
                                <a class="" style="cursor: pointer; color: #107db7; margin: 0 10px"><i
                                            class="material-icons">send</i></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <p class="text-center center">
                <a href="{{ route('login') }}" class="flow-text text-center">Inicie sessão para comentar</a>
            </p>
        @endif

    </div>


</div>