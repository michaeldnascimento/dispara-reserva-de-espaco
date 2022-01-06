<?php

use \App\Http\Response;

//ROTA GERAR TOKEN DE AUTORIZAÇÃO API
$obRouter->get('/api/v3/oauth2', [
   function() use ($ttlock) {
    return new Response(201, $ttlock->oauth2->token(getenv('USERNAME'), getenv('PASSWORD'), getenv('URL')), 'application/json');
   }
]);

//ROTA DE REFRESH NO TOKEN DE AUTORIZAÇÃO - OBS: NESCESSARIO SALVAR O NOVO TOKEN NO .ENV
$obRouter->get('/api/v3/oauth2/refresh', [
    function() use ($ttlock) {
        return new Response(200, $ttlock->oauth2->refreshToken(getenv('REFRESH_TOKEN'), getenv('URL')), 'application/json');
    }
]);