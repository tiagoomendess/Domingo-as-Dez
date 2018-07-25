<div class="col s12">
    <table id="permissions_table" class="bordered">
        <tr>
            <td>
                Loading
            </td>
        </tr>
    </table>
</div>

<div class="row">
    <div class="col s12">

        <select id="permissions_dropdown" class="browser-default">
            <?php
                $all_perms = App\Permission::all();

            ?>

            <option value="" disabled selected>{{ trans('general.choose_option') }}</option>

            @foreach($all_perms as $perm)
                    <option value="{{$perm->id}}">{{ trans('permissions.' . $perm->name) }}</option>
            @endforeach
        </select>
    </div>


</div>

<div class="row">
    <div class="col s12">
        <a class="right waves-effect waves-light btn green" onclick="addPermission()"><i class="material-icons left">add</i>{{ trans('general.add') }} {{ trans('models.permission') }}</a>
    </div>
</div>


