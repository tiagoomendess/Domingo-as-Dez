@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.article') }}</title>

    <script>
        function resizeIframe(obj) {
            obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
            var test = $(obj);
            test.contents().find('header').hide();
        }
    </script>
@endsection

@section('content')

    <iframe id="article_preview" onload="resizeIframe(this)" class="article-preview" scrolling="no" src="{{ $article->getPublicUrl() }}" frameborder="0"></iframe>

    @if(Auth::user()->hasPermission('articles.edit'))
        @include('backoffice.partial.model_options', ['edit_route' => route('articles.edit', ['article' => $article]), 'delete_route' => route('articles.destroy', ['article' => $article])])
    @endif
@endsection



@section('scripts')
    <script>
        $(document).ready(function(){
            $('.materialboxed').materialbox();
        });
    </script>
@endsection