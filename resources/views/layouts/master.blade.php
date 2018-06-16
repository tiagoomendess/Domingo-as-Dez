<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="author" content="{{ config('custom.author') }}">

    <!-- Chrome, Firefox OS and Opera -->
    <meta name="theme-color" content="#1E88E5">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#1E88E5">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-status-bar-style" content="#1E88E5">

    <meta name="apple-mobile-web-app-capable" content="yes">

    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    @yield('head')
</head>

<body>
    @yield('body')

    <script>

        $(document).ready(function () {
            try{
                var title = document.title;
                var h1 = $('.navbar-title h1');

                if (title == null) {
                    h1.removeClass('hide');
                    console.log('Title non existent, using default site name.');
                    return;
                }

                if (h1.text().trim() === '{{ config('custom.site_name') }}') {

                    h1.text(title);
                    h1.removeClass('hide');

                } else {

                    h1.removeClass('hide');
                    console.log('Title not changed! Custom title already in place.');
                    return;

                }

            } catch (e) {
                console.log('Navbar title not found, title not aplied.');
            }
        });

    </script>

</body>
</html>