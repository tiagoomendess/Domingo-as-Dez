<html>
<p>Foi enviada uma nova informação para o website:</p>
<ul>
    <li>Utilizador: {{ $info_report->user_id != null ? $info_report->user->email : 'Anónimo' }}</li>
    <li>Data e Hora: {{ \Carbon\Carbon::now('Europe/Lisbon')->format('d/m/Y - H:i:s') }}</li>
    <li>Código: {{ $info_report->code }}</li>
    <li>Fonte: {{ $info_report->source }}</li>
    <li>Informação: {{ $info_report->content }}</li>
</ul>
</html>
