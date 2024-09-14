<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            background-color: #ffffff;
            width: 90%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .logo {
            margin-bottom: 20px;
        }
        .content {
            font-size: 16px;
            color: #333333;
            line-height: 1.5;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Your Logo -->
    <div class="logo">
        <img src="{{ url('/images/domingo.png') }}" alt="Domingo às Dez" style="max-width: 150px;">
    </div>

    <!-- Email Content -->
    <div class="content">
        <h2>Olá {{ $recipientName }},</h2>
        <p>
            O Domingo às Dez pede a vossa colaboração para recolher informações sobre o jogo entre
            {{ $homeClubName }} e {{ $awayClubName }}, no passado dia {{ $day }} de {{ $month }} de {{ $year }}.
        </p>
        <p>
            Clique no botão abaixo para preencher as informações dos eventuais marcadores de golos e
            fazer uma pequena análise da partida.
        </p>

        <!-- Action Button -->
        <a href="{{ $action }}" class="button">Flash Interview</a>

        <p style="margin-top: 20px;">
            Se pedir um PIN insira o seguinte: <strong>{{ $pin }}</strong>. Por favor preencha até {{ $deadline }},
            não serão aceites respostas após esta data.
        </p>

        <p>
            Para qualquer dúvida ou questão, contactar pelos canais habituais. Não responda a este email, foi enviado
            de forma automática e não obterá resposta.
        </p>

        <p>
            Se o botão não funcionar, copie e cole o seguinte link no seu navegador: <br>
            <a href="{{ $action }}">{{ $action }}</a>
        </p>

        <p>
            Desde já obrigado,<br>
            A equipa do Domingo às Dez!
        </p>

        <span>---</span>

        <h2 style="margin-top: 20px;">O que é isto?</h2>
        <p>
            Temos recebido várias criticas sobre algumas informações nas analises semanais estarem erradas, de que
            as declarações nas crónicas não coorespondem à verdade, e apesar de se tratarem de erros pontuais e sem
            intenção, eles acontecem e queremos melhorar esse aspeto.
        </p>
        <p>
            Por isso criamos esta forma automática e fácil de dar voz aos intervenientes diretos no jogo. Sem ambiguidades e sem
            erros. Ambas as equipas têm agora um espaço onde podem atribuir os golos da sua equipa e dizer o que acharam da partida,
            ao estilo de uma flash interview, mas online.
        </p>
        <p>
            As informações escritas pelos clubes serão usadas nos artigos diretamente, assim como a atribuição de golos no website. Desta forma
            espera-se acabar com os erros e meias verdades. Quem não quiser participar não pode reclamar, serão usadas
            as informações de quem participar.
        </p>
    </div>
</div>
</body>
</html>
