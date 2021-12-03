<?php

namespace App\Http\Controller\Pages;


use \App\Http\Request;
use \App\Utils\View;

class Page {


    /**
     * Método responsavel por retornar o conteudo (view) da nossa home
     * @return string
     */
    public static function getPage($title, $content)
    {
        return View::render('pages/page', [
            'title'   => $title,
            'content' => $content,
        ]);
    }


}