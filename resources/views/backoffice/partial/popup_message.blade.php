<div id="popup_message_modal" class="modal">
    <div class="modal-content" style="padding-bottom: 0">
        <h4 class="center">{{ trans('general.message') }}</h4>
        <div class="row">
            <div class="col s12">
                <ul class="flow-text">
                    @if(isset(Session::get('popup_message')->messages()['error']))
                        @foreach(Session::get('popup_message')->messages()['error'] as $message)
                            <li class="red-text">{{ $message }}</li>
                        @endforeach
                    @endif

                    @if(isset(Session::get('popup_message')->messages()['warning']))
                        @foreach(Session::get('popup_message')->messages()['warning'] as $message)
                            <li class="orange-text">{{ $message }}</li>
                        @endforeach
                    @endif

                    @if(isset(Session::get('popup_message')->messages()['info']))
                        @foreach(Session::get('popup_message')->messages()['info'] as $message)
                            <li class="blue-text">{{ $message }}</li>
                        @endforeach
                    @endif

                    @if(isset(Session::get('popup_message')->messages()['success']))
                        @foreach(Session::get('popup_message')->messages()['success'] as $message)
                            <li class="green-text">{{ $message }}</li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>

        <div class="row no-margin-bottom">
            <div class="col s12 center">
                <div class="divider"></div>
            </div>
        </div>

    </div>

    <div class="modal-footer">
        <div class="row no-margin-bottom">
            <div class="col s12">
                <a href="#" class="modal-action modal-close waves-effect btn-flat">{{ trans('general.ok') }}</a>
            </div>
        </div>
    </div>
</div>

<script>
    setTimeout(() => {
        $(document).ready(function () {
            $('#popup_message_modal').modal();
            $('#popup_message_modal').modal('open');
        });
    }, 10)
</script>