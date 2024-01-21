<html>
<p>Ocorreu um erro no website. Os detalhes estão abaixo</p>
<h1>Pedido</h1>
<ul>
    <li>Metodo: {{ $request->method() }}</li>
    <li>URL: {{ $request->fullUrl() }}</li>
    <li>HTTPS: {{ $request->secure() ? 'Sim' : 'Não' }}</li>
    <li>IP: {{ $request->getClientIp() }}</li>
    <li>Body: <code>{{ json_encode($request->json()) }}</code></li>
</ul>

<h1>Excepção:</h1>

<ul>
    <li>Nome: {{ get_class($exception) }}</li>
    <li>Data e Hora: {{ \Carbon\Carbon::now('Europe/Lisbon')->format('d/m/Y - H:i:s') }}</li>
    <li>Stack Trace: {!! preg_replace('/\#[0-9]+/', '<br>', $exception->getTraceAsString()) !!}</li>
</ul>
</html>