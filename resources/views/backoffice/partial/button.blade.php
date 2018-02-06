
<button class="btn waves-effect waves-light @if(isset($color)) {{ $color }} @endif" type="submit" name="action">@if(isset($text)) {{ $text }}@else {{ trans('general.send') }}@endif
    @if(isset($icon))
        <i class="material-icons right">{{ $icon }}</i>
    @endif
</button>
