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
        <div id="highlight">
            <div id="highlighted_match">

            </div>
            <div class="brand">
                <small style="opacity: 0.5">Desenvolvido por:</small>
                <img src="/images/domingo_as_dez_logo_mono.png" alt="Domingo às Dez" style="width: 100px; margin: 10px 0 5px">
                <h1 style="font-weight: 200; margin: 0;">Domingo às Dez</h1>
            </div>
            <div id="sponsor">
                @foreach($partners as $partner)
                    <img class="hide partner-image" style="height: 200px" src="{{ $partner->picture }}" alt="{{ $partner->name }}">
                @endforeach
            </div>
        </div>

        <div id="all_matches">

        </div>
    </main>
</body>
<script src="/js/front/scores-aggregator.js"></script>
</html>
