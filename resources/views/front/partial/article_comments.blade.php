<div class="row">
    <div class="container" style="">
        <h3 style="width: 100%" class="over-card-title">Comentários</h3>
        <div class="comment hide" id="comment_template">
            <div class="comment-header">
                <img class="circle" src="/storage/uploads/users/avatars/sq21539613001mhj-mhm1xgxwf.jpg" alt="">
                <span class="text-bold">José Silva</span>
                <small style="margin-left: 5px; color: grey; font-size: 11px">6/6/2021 às 21:40</small>
                <form class="{{ has_permission('admin') ? '' : 'hide' }}" method="POST" action="">
                    {{ csrf_field() }}
                    <button style="border: none; background-color: #ffffff" href="javascript:void(0)" type="submit" class=""><i class="material-icons red-text">delete</i></button>
                </form>
            </div>

            <div class="comment-content" style="width: auto">
                <comment class="flow-text">Isto é o comentário Isto é o</comment>
            </div>
        </div>

        <div id="all_comments" style="margin: 0 0 10px 0" data-user-id="{{ isset($user) ? $user->id : 'null' }}">
            <p class="flow-text text-center hide">Ainda não existem comentários</p>
        </div>

        @if(\Illuminate\Support\Facades\Auth::user())
            <div class="row" id="new_comment">
                <div class="col s12">
                    <form action="{{ route('article_comments.comment', ['article_id' => $article->id]) }}"
                          method="POST">
                        <div style="background-color: #FFFFFF; box-shadow: #c3c3c3 0 2px 2px 2px; width: 100%; display: table;">
                            <div class="" style="height: 7px; display: table-row;"></div>
                            <div class="" style="padding: 10px; display: table-row;">


                                <div class="" style="display: table-cell; width: 60px; vertical-align: middle;">
                                    <img style="width: 40px; margin: 0 10px;" class="circle"
                                         src="{{ \Illuminate\Support\Facades\Auth::user()->profile->getPicture() }}"
                                         alt="">
                                </div>

                                {{ csrf_field() }}

                                <div class="" style="display: table-cell; width: auto">
                                    <div class="input-field">
                                        <input id="comment" name="comment" type="text" class="validate">
                                        <label for="comment">Comentário</label>
                                    </div>
                                </div>

                                <div class="" style="display: table-cell; width: 60px; vertical-align: middle;">
                                    <button type="submit" style="border:none; background-color: white; cursor: pointer; color: #107db7; margin: 0 10px"><i
                                                class="material-icons">send</i></button>
                                </div>

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