@extends('layouts.master')

@section('head')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="/materialize/css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="/css/backoffice/style.css" media="screen,projection"/>
    @yield('head-content')
@endsection

@section('body')

    <div>

        @include('backoffice.partial.sidenav')

        @include('backoffice.partial.navbar')

        <div class="row">
            <div class="content-wrapper">
                <div class="col s12">

                    @yield('content')
                </div>
            </div>
        </div>

    </div>

    <!-- End of page, load scripts -->
    <script type="text/javascript" src="/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/materialize/js/materialize.min.js"></script>
    <script type="text/javascript">

        $('.button-collapse').sideNav({
                menuWidth: 300,
                edge: 'left',
                closeOnClick: false,
                draggable: true
            }
        );

        $(document).ready(function () {
            // the "href" attribute of the modal trigger must specify the modal ID that wants to be triggered
            $('#modal_logout').modal();

            // set browser language in a cookie
            var lang = navigator.language || navigator.userLanguage;
            document.cookie = "lang=" + lang;

            // if no ip cookie is found, set it
            if (document.cookie.indexOf("ip=") < 0) {
                getIp("https://api.my-ip.io/v1/ip", async (ip) => {
                    if (ip) {
                        document.cookie = "ip=" + ip;
                    }
                });
            }

            if (document.cookie.indexOf("timezone=") < 0) {
                document.cookie = "timezone=" + Intl.DateTimeFormat().resolvedOptions().timeZone;
            }
        });

        async function getIp(theUrl, callback) {
            let xmlHttp = new XMLHttpRequest();
            xmlHttp.onreadystatechange = function () {
                if (xmlHttp.readyState === 4) {
                    if (xmlHttp.status === 200)
                        callback(xmlHttp.responseText)
                    else
                        callback(null)
                }
            }
            xmlHttp.open("GET", theUrl, true);
            xmlHttp.send(null);
        }

    </script>

    @if(Session::has('popup_message'))
        @include('backoffice.partial.popup_message')
    @endif

    @yield('scripts')
@endsection