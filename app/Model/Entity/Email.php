<?php

namespace App\Model\Entity;

use \App\Db\Database;
use \App\Utils\Email\TinyMinify;
use \PDO;
use PDOException;
use PDOStatement;


class Email
{
    /**
     * ID Email
     * @var integer
     */
    public $id;

    /**
     * Valor recover
     * @var array
     */
    public $valeu;

    /**
     * Método responsável por gravar o e-mail no banco de disparo
     * @param string $content
     * @return bool
     */
    public function sendContentResp($content)
    {

        //minificar o html
        $mensagemMini = TinyMinify::html($content);

        //echo $mensagemMini;
        //exit;

        //INSERE A INSTANCIA NO BANCO
        $this->id = (new Database('central', 'CNTEMAIL'))->insert([
            'de' => 'no-reply@sistemas.fm.usp.br',
            'para' => 'sistemas.nti@fm.usp.br',
            'assunto' => 'Carga Fechadura',
            'delogin' => 'Anf. Fechadura',
            'codmotivo' => 681,
            'enviado' => 'n',
            'mensagem' => $mensagemMini,
            'datahora' => date('Y-m-d H:i:s')
        ]);

        //SUCESSO
        return $this->id;

    }

}