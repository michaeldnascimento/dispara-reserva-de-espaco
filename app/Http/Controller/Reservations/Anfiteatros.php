<?php

namespace App\Http\Controller\Reservations;

use App\Http\Controller\Api\TTLock;
use App\Http\Request;
use App\Model\Entity\Reservations as EntityReservations;
use PDOStatement;

class Anfiteatros {

    /**
     * Método responsável por consultar o salas
     * @param int $numSala
     * @return EntityReservations|PDOStatement|string
     */
    public static function lockIdSala($numSala)
    {

        if($numSala != '') {
            return EntityReservations::getLockIdSala($numSala);
        }

        return 'Sala não localizado';
    }

    /**
     * Método responsável por consultar o contados
     * @param int $id
     * @return EntityReservations|PDOStatement|string
     */
    public static function getContact($id)
    {
            if($id != '') {
                return EntityReservations::getContactReservations($id);
            }

            return 'Contato não localizado';
    }

    /**
     * Método responsável por retornar as reservas agendadas no dia
     * @param Request $request
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getReservations(Request $request)
    {

        //CRIA A CHAVE COM BASE NO DA DATA ATUAL
        $key = "A" . date('dmY');

        //CONSULTAR RESERVAS DISPONIVEIS NO DIA
        $reservationsDay = EntityReservations::getReservationsDayByKey($key);

        //VERICA SE EXISTE RESERVA
        if (count(array($reservationsDay)) > 0) {

            foreach ($reservationsDay as $arr) {

                //BUSCA DADOS DO CONTATO
                $contactReservationsDay = self::getContact($arr['id_agendamento']);

                //VERIFICA SE EXISTE CONTATO
                if ($contactReservationsDay != ''){

                    //RETORNA DADOS DO CONTATO E UNIFICAM EM UM UNICO ARRAY
                    $mergeReservationsDay = array_merge($contactReservationsDay, $arr);

                    //RESPONSAVEL POR BUSCAR O LOCK ID FECHADURA POR SALA DE AULA
                    $returnlockId = self::lockIdSala($arr['numero']);

                    //VERIFICA SE EXISTE O LOCK ID NA SALA
                    if ($returnlockId['lockId'] != ''){

                        //DEFINE AS CONFIGURAÇÕES DE ACESSO API
                        $ttlock = New TTLock(getenv('CLIENT_ID'), getenv('CLIENT_SECRET'));

                        //SET TOKEN DE ACESSO
                        $ttlock->identityCard->setAccessToken(getenv('ACCESS_TOKEN'));

                        //DATA EM MILISSEGUNDO
                        $date = $ttlock->getDateTimeMillisecond(date('Y-m-d H:i:s'));

                        //GRAVA CRACHÁ DO RESPONSAVEL NA API
                        $ttlock->identityCard->addCard($returnlockId['lockId'], $mergeReservationsDay[0]['responsavel_num_cracha'], $mergeReservationsDay[0]['responsavel_nome'], $mergeReservationsDay['hora_inicio'],  $mergeReservationsDay['hora_fim'], $date);

                        //GRAVA CRACHÁ DO RESPONSAVEL NA API
                        //$ttlock->identityCard->addCard($returnlockId['lockId'], '195352793', 'Michael API Web', $startDate, $endDate, $date);

                    }else {
                        echo "Número Sala e Lock ID não localizado!";
                    }

                    echo "<pre>";
                    print_r($mergeReservationsDay);
                    //print_r($lockId);


                }

            }

        } else {

            return 'Nao possui agenda para hoje';

        }

//        echo "<pre>";
//        print_r($mergeReservationsDay);
        //print_r($obj['id']);
        exit;

        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimonyById($id);

        //VALIDA A INSTANCIA
        if(!$obTestimony instanceof EntityTestimony){
            $request->getRouter()->redirect('/admin/testimonies');
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/testimonies/form', [
            'title'    => 'Editar depoimento',
            'nome'     => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'status' => self::getStatus($request)
        ]);

        return parent::getPanel('Editar depoimento > WDEV', $content, 'testimonies');
    }

}