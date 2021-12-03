<?php

use \App\Http\Response;

//ROTA AUTORIZAÇÃO DA API
$obRouter->get('/api/v1/oauth2', [
   function($request){
    return new Response(201, (new App\Http\Controller\Api\Oauth2)->token($request), 'application/json');
   }
]);