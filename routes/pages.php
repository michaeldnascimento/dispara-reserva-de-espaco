<?php

use \App\Http\Response;
use \App\Http\Controller\Pages;

//ROTA HOME
$obRouter->get('/', [
    'middlewares' => [
        //'cache'
        //'required-login',
    ],
    function(){
        return new Response(200, Pages\Home::getHome());
    }
]);