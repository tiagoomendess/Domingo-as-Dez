<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados</title>
    <link rel="stylesheet" href="/css/front/scores-aggregator.css">
</head>
<body>
    <main>
        <header>
            <h1 style="font-weight: 200; margin-left: 10px">Domingo às Dez</h1>
            <img src="/images/domingo_as_dez_logo_mono.png" alt="Domingo às Dez">
        </header>

        <div id="highlight">
            <div id="highlighted_match">

            </div>
            <div id="sponsor">
                @foreach($partners as $partner)
                    <img style="display: none; width: 100%" src="{{ $partner->picture }}" alt="{{ $partner->name }}">
                @endforeach
            </div>
        </div>

        <div id="all_matches">

        </div>
    </main>
</body>
<script src="/js/front/scores-aggregator.js"></script>
</html>
