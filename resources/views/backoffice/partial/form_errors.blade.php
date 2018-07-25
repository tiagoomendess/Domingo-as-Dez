<div class="left">
    @if (count($errors) > 0)

        <blockquote>
            <ul style="color: red;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </blockquote>

    @endif
</div>