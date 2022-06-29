<div class="card blue-grey lighten-5">
    <div class="card-content">
        <div class="col s6">
            <p class="flow-text text-bold">{{ $info->code }}</p>
        </div>
        <div class="col s6">
            <p class="flow-text right">
                {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $info->created_at)->timezone('Europe/Lisbon')->format("d/m/Y") }}
            </p>
        </div>

        @php
            $user = \Illuminate\Support\Facades\Auth::user();
        @endphp

        <div class="col s12">
            <div class="divider"></div>
            <div class="vertical-spacer"></div>
            <p class="flow-text">{{ $info->content }}</p>
            <div class="vertical-spacer"></div>
            <div class="divider"></div>
            @if(!empty($user) && $user->id == $info->user_id)
                <p class="left red-text" style="font-weight: 300; margin: 5px 0 10px 0; text-align: right">
                    <a class="modal-trigger red-text" href="#apagar_info_modal">Apagar</a>
                </p>
            @endif
            <p class="right grey-text" style="font-weight: 300; margin: 5px 0 10px 0; text-align: right">
                {{ trans('front.info_report_status_' . $info->status) }}
            </p>
            <div class="vertical-spacer"></div>
        </div>

        <div id="apagar_info_modal" class="modal">
            <div class="modal-content">
                <h4 class="center">Apagar Informação?</h4>
                <p class="flow-text center">Tem a certeza que pretende apagar a informação?</p>
            </div>
            <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; margin-bottom: 20px">
                <a href="#!"
                   style="margin: 0 20px;"
                   class="green darken-3 modal-action modal-close waves-effect btn">Não</a>
                <form method="POST" action="{{ route('info.delete') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="code" value="{{ $info->code }}">
                    <button type="submit" class="red darken-1 modal-action modal-close waves-effect btn">
                        Sim
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
