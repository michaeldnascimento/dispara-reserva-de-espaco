<?php

use \App\Http\Response;

//ROTA PARA REGISTRAR USUÁRIO
$obRouter->get('/api/v3/user', [
    function() use ($ttlock) {
        $date = $ttlock->getDateTimeMillisecond(date('Y-m-d H:i:s'));
        return new Response(201, $ttlock->user->register(getenv('ACCESS_TOKEN'), getenv('PASSWORD'), $date), 'application/json');
    }
]);

//ROTA PARA RESETAR A SENHA
$obRouter->get('/api/v3/user/resetPassword', [
    function() use ($ttlock) {
        $date = $ttlock->getDateTimeMillisecond(date('Y-m-d H:i:s'));
        return new Response(200, $ttlock->user->resetPassword(getenv('USERNAME'), getenv('PASSWORD'), $date), 'application/json');
    }
]);

//ROTA PARA CONSULTAR USUÁRIO REGISTRADOS
$obRouter->get('/api/v3/user/list', [
    function() use ($ttlock) {
        $date = $ttlock->getDateTimeMillisecond(date('Y-m-d H:i:s'));
        return new Response(200, $ttlock->user->listUser('', '', '1', '20', $date ), 'application/json');
    }
]);

//ROTA PARA DELETAR USUÁRIO REGISTRADO
$obRouter->get('/api/v3/user/delete', [
    function() use ($ttlock) {
        $date = $ttlock->getDateTimeMillisecond(date('Y-m-d H:i:s'));
        return new Response(200, $ttlock->user->delete(getenv('USERNAME'), $date), 'application/json');
    }
]);
