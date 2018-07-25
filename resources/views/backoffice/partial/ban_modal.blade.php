<div id="ban_modal" class="modal">
    <div class="modal-content">
        <h4 class="center">{{ trans('general.ban') }} {{ trans('models.user') }}</h4>
        <p class="flow-text center">{{ trans('general.ban_reason') }}</p>


        <form action="{{ route('user_bans.store') }}" method="POST">
            {{ csrf_field() }}

            <div class="row">
                <div class="input-field col s12">
                    <textarea name="reason" id="reason" class="materialize-textarea" data-length="155"></textarea>
                    <label for="reason">{{ trans('models.reason') }}</label>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <button class="btn waves-effect waves-light" type="submit" name="action">{{ trans('general.ban') }}
                        <i class="material-icons right">gavel</i>
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

