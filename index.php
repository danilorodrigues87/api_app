<?php

require __DIR__.'/includes/app.php';
use \App\Http\Router;

$obRouter = new Router(URL);

//INCLUI AS ROTAS DO PAINEL
include __DIR__.'/routes/site.php';

//INCLUI AS ROTAS DE APIS
include __DIR__.'/routes/api.php';

//IMPRIME O RESPONSE DA ROTA
$obRouter->run()->sendResponse();
