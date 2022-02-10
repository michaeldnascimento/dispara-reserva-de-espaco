<?php

use \App\Http\Response;
use \App\Http\Controller\Reservations\Anfiteatros;

//ROTA PARA CRIAR NOVO CARTÃO DE ACESSO
$obRouter->get('/api/v3/identityCard/addCard', [
   function() use ($ttlock) {
    //SET TOKEN DE ACESSO
    $ttlock->identityCard->setAccessToken(getenv('ACCESS_TOKEN'));
    //DATA EM MILISSEGUNDO
    $date = $ttlock->getDateTimeMillisecond(date('Y-m-d H:i:s'));
    //DATA INICIO E DATA FIM DA RESERVA
    $startDate = $ttlock->getDateTimeMillisecond(date('Y-m-d H:i:s'));
    $endDate = $ttlock->getDateTimeMillisecond(date('Y-m-d H:i:s',time()+3600));
    return new Response(201, $ttlock->identityCard->addCard('4131746', '195352793', 'Michael API Web', $startDate, $endDate, $date), 'application/json');
   }
]);

//ROTA PARA CRIAR NOVO CARTÃO DE ACESSO DAY (CRON)
$obRouter->get('/api/v3/identityCard/addCardDayCron', [
    function($request) {

        //ROTA RESPONSÁVEL POR CADASTRAR RESERVAS DO DIA NA API
        return new Response(201, Anfiteatros::getReservations($request), 'text/html');

    }
]);

//ROTA PARA GERAR LISTA DE CARTÃO CADASTRADO
$obRouter->get('/api/v3/identityCard/listCard', [
    function() use ($ttlock) {
        //SET TOKEN DE ACESSO
        $ttlock->identityCard->setAccessToken(getenv('ACCESS_TOKEN'));
        //DATA EM MILISSEGUNDO
        $date = $ttlock->getDateTimeMillisecond(date('Y-m-d H:i:s'));
        return new Response(200, $ttlock->identityCard->listCard('3005712', '1', '20', $date), 'application/json');
    }
]);

//ROTA PARA DELETAR O CARTÃO DE ACESSO
$obRouter->get('/api/v3/identityCard/delete', [
    function() use ($ttlock) {
        //SET TOKEN DE ACESSO
        $ttlock->identityCard->setAccessToken(getenv('ACCESS_TOKEN'));
        //DATA EM MILISSEGUNDO
        $date = $ttlock->getDateTimeMillisecond(date('Y-m-d H:i:s'));
        return new Response(200, $ttlock->identityCard->delete('3005712', '11380986', $date), 'application/json');
    }
]);