@if(!empty($searchFields))
    <div class="right" style="margin-top: 15px">
        <a id="search_button" onclick="searchButtonCkicked()" class="waves-effect waves-light btn-flat"><i
                    class="material-icons right">search</i>Pesquisar</a>
        <a id="close_search_button" onclick="closeSearchButtonCkicked()" class="hide waves-effect waves-light btn-flat"><i
                    class="material-icons right">close</i>Fechar Pesquisa</a>
    </div>
    <script>
        function searchButtonCkicked() {
            $('#search_form').removeClass('hide');
            $('#search_button').addClass('hide');
            $('#close_search_button').removeClass('hide');
        }

        function closeSearchButtonCkicked() {
            $('#search_button').removeClass('hide');
            $('#search_form').addClass('hide');
            $('#close_search_button').addClass('hide');
        }
    </script>
@endif