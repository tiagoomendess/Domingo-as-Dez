
@if(isset($route))
    <div class="fixed-action-btn">
        <a class="btn-floating btn-large green waves-effect" href="{{ $route }}">
            <i class="large material-icons">add</i>
        </a>
    </div>
@else
    <div class="fixed-action-btn">
        <a disabled class="btn-floating btn-large green waves-effect" href="#">
            <i class="large material-icons">add</i>
        </a>
    </div>
@endif