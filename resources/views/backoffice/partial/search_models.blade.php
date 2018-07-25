<div class="row">

    <form action="" method="POST">
        <div class="input-field inline">
            <select name="field">

                <option value="null" disabled selected>{{ trans('general.choose_option') }}</option>

                @foreach($fields as $field)
                    <option value="{{$field}}">{{ trans('models.' . $field) }}</option>
                @endforeach

            </select>
            <label>{{ trans('general.field') }}</label>
        </div>

        <div class="input-field inline">
            <input id="search_term" type="text" class="validate">
            <label for="search_term" data-error="wrong" data-success="right">{{ trans('general.search_term') }}</label>
        </div>

        <button class="btn waves-effect waves-light" type="submit" name="action">Submit
            <i class="material-icons right">send</i>
        </button>


    </form>

</div>

@section('scripts')
    <script>
        $(document).ready(function() {
            $('select').material_select();
        });
    </script>
@endsection