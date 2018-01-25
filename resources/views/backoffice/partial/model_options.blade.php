<div class="fixed-action-btn horizontal click-to-toggle">
    <a class="btn-floating btn-large yellow darken-3">
        <i class="material-icons">menu</i>
    </a>
    <ul>
        <li><a class="btn-floating blue" href="{{ $edit_route }}"><i class="material-icons">edit</i></a></li>
        <li><a class="btn-floating red modal-trigger" href="#delete_modal"><i class="material-icons">delete</i></a></li>
    </ul>
</div>

<!-- Modal Structure -->
<div id="delete_modal" class="modal">
    <div class="modal-content">
        <h4 class="">{{ trans('general.warning') }}</h4>
        <p class="flow-text">{{ trans('models.delete_warning') }}</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat">{{ trans('general.no') }}</a>
        <a href="#" class="modal-action waves-effect waves-green btn-flat">{{ trans('general.yes') }}</a>
    </div>
</div>

@section('scripts')
    <script>

        $(document).ready(function(){

            $('#delete_modal').modal();
        });

    </script>
@endsection