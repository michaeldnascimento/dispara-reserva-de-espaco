<?php

require __DIR__ . '/includes/app.php';

use \App\Http\Router;

//INICIA O ROUTER
$obRouter = new Router(URL);

//INCLUI AS ROTAS DE API
include __DIR__ . '/routes/api.php';

//IMPRIME O RESPONSE DA ROTA
$obRouter->run()
    ->sendResponse();