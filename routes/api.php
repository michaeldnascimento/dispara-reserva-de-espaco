<?php

//INCLUI ROTAS DE AUTENTICAÇÃO (TOKEN) DA API
include __DIR__. '/api/v3/oauth2.php';

//INCLUI ROTAS DE USUÁRIOS
include __DIR__. '/api/v3/user.php';

//INCLUI ROTAS DE INICIALIZAÇÃO
include __DIR__ . '/api/v3/identityCard.php';
