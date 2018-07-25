<div class="fixed-action-btn horizontal click-to-toggle">
    <a class="btn-floating btn-large yellow darken-3">
        <i class="material-icons">menu</i>
    </a>
    <ul>

        @if(isset($edit_route))
            <li><a class="btn-floating blue" href="{{ $edit_route }}"><i class="material-icons">edit</i></a></li>
        @else
            <li><a disabled class="btn-floating blue" href="#"><i class="material-icons">edit</i></a></li>
        @endif

        @if(isset($delete_route))
            <li><a class="btn-floating red modal-trigger" href="#delete_modal"><i class="material-icons">delete</i></a></li>

        @else
            <li><a disabled class="btn-floating red" href="#"><i class="material-icons">delete</i></a></li>
        @endif

    </ul>
</div>


@if(isset($delete_route))
    <!-- Modal Structure -->
    <div id="delete_modal" class="modal">
        <div class="modal-content">
            <h4 class="center">{{ trans('general.warning') }}</h4>
            <p class="flow-text center">{{ trans('models.delete_warning') }}</p>

            <div class="row">
                <div class="col s6">
                    <a href="#" class="modal-close waves-effect waves-light btn-large green right">{{ trans('general.no') }}</a>
                </div>

                <div class="col s6">
                    <form action="{{ $delete_route }}" method="POST">

                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}

                        <button class="btn waves-effect waves-light red darken-1 btn-large" type="submit" name="action">{{ trans('general.yes') }}
                        </button>

                    </form>
                </div>

            </div>
        </div>
    </div>

    @section('scripts')
    <script>

        $(document).ready(function(){

            $('#delete_modal').modal();
        });

    </script>
    @endsection

@endif

