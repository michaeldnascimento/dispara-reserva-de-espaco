<?php
ob_start();
header('Content-Type: text/html; charset=utf-8');
set_time_limit(0);

//DEFINE TIMEZONE SISTEMA
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

//COMPOSER - AUTOLOAD
require __DIR__ . '/../vendor/autoload.php';
//require __DIR__ . '/../includes/topo.php';

use \App\Utils\View;
use \App\Common\Environment;
use \App\Http\Middleware\Queue as MiddlewareQueue;
use \App\Http\Controller\Api\TTLock;

//CARREGA AS VARIAVEIS DE AMBIENTE DO PROJETO
Environment::load(__DIR__. '/../');

//DEFINE AS CONFIGURAÇÕES DE ACESSO API
$ttlock = New TTLock(getenv('CLIENT_ID'), getenv('CLIENT_SECRET'));

//DEFINE A CONSTANTE DE URL
define('URL', getenv('URL'));

//DEFINE O VALOR PADRÃO DAS VARIAVEIS
View::init([
    'URL' => URL
]);

//DEFINE O MAPEAMENTO DE MIDDLEWARE
MiddlewareQueue::setMap([
    'maintenance'              => \App\Http\Middleware\Maintenance::class,
    'api'                      => \App\Http\Middleware\Api::class,
]);


//DEFINE O MAPEAMENTO DE MIDDLEWARE PADRÕES (EXECUTADOS EM TODAS AS ROTAS)
MiddlewareQueue::setDefault([
    'maintenance'
]);