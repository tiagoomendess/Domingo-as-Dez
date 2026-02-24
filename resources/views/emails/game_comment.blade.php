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
            text-align: justify;
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
            margin-bottom: 25px;
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
        <img src="{{ url('/images/domingo.png') }}" alt="Domingo às Dez" style="max-width: 100px;">
    </div>

    <!-- Email Content -->
    <div class="content">
        <h2>Olá {{ $recipientName }},</h2>
        <p>
            O Domingo às Dez pede a sua colaboração para recolher informações sobre o jogo entre
            {{ $homeClubName }} e {{ $awayClubName }}, no dia {{ $day }} de {{ $month }} de {{ $year }}.
        </p>
        <p>
            Clique no botão abaixo para preencher as informações dos eventuais marcadores de golos e
            fazer uma pequena análise da partida. Pode preencher parcialmente, nenhum campo é obrigatório.
        </p>

        <!-- Action Button -->
        <a href="{{ $action }}" class="button" style="color: white">Flash Interview</a>

        <p style="margin-top: 25px;">
            Se pedir um PIN insira o seguinte <strong>{{ $pin }}</strong>. Por favor preencha até {{ $deadline }},
            não serão aceites respostas após esta data.
        </p>

        <p>
            Se não deseja receber mais estes emails, <b>por favor não marque como SPAM</b>, clique no seguinte link para
            <a href="{{ $unsubscribe }}" style="color: red">desativar o envio destes emails</a>.
        </p>

        <p>
            Se o botão não funcionar, copie e cole o seguinte link no seu navegador: <br>
            <a href="{{ $action }}">{{ $action }}</a>
        </p>

        <p>
            Se já passou o prazo para responder, pode ainda enviar as informações para o email
            <a href="mailto:geral@domingoasdez.com">geral@domingoasdez.com</a> se o artigo ainda não foi
            publicado.
        </p>

        <p>
            Agradecemos desde já a sua disponibilidade, ficamos à espera do seu contributo. Com os melhores cumprimentos,<br>
            A equipa do Domingo às Dez.
        </p>

        <small>
            Esta mensagem foi enviada para este email porque foi identificado como sendo a pessoa ideal para falar pelo
            {{ $recipientName }} nestas questões. Se não for o caso, por favor entre em contacto pelas redes nossas sociais,
            ou enviando um email para <a href="mailto:geral@domingoasdez.com">geral@domingoasdez.com</a>.
            Não responda a este email diretamente, foi enviado de forma automática e não obterá resposta. Para parar de
            receber estes emails, clique no seguinte link para <a href="{{ $unsubscribe }}">cancelar a subscrição</a>.
        </small>
    </div>
</div>
</body>
</html>
