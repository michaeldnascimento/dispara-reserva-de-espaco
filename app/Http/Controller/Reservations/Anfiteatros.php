<?php

namespace App\Http\Controller\Reservations;

use App\Http\Controller\Api\TTLock;
use App\Http\Request;
use App\Model\Entity\Reservations as EntityReservations;
use PDOStatement;

class Anfiteatros {

    /**
     * Método responsável por gravar se foi enviado o crachá para api
     * @param int $id_agendamento
     * @param string $key
     * @param int $value
     * @return bool|string
     */
    public static function updateChachaSendApi($id_agendamento, $key, $value)
    {

        if($id_agendamento != '' AND $key == 'Responsavel') {
            return EntityReservations::updateReponsavelChacha($id_agendamento, $value);
        }

        if($id_agendamento != '' AND $key == 'Solicitante') {
            return EntityReservations::updateSolicitanteChacha($id_agendamento, $value);
        }

        return 'Id Agendamento não foi enviado';
    }

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

                    echo "ID Agendamento: ".$mergeReservationsDay['id_agendamento']."/ Sala: ".$mergeReservationsDay['sala']."/ Data: ".$mergeReservationsDay['data_agendamento']."/ Hora Inicio: ".$mergeReservationsDay['hora_inicio']."/ Hora Fim: ".$mergeReservationsDay['hora_fim']." " . "<br/>";

                    //VERIFICA SE EXISTE O LOCK ID NA SALA (LOCK ID FECHADURA POR SALA DE AULA)
                    if ($mergeReservationsDay['lockId'] != ''){

                        //DEFINE AS CONFIGURAÇÕES DE ACESSO API
                        $ttlock = New TTLock(getenv('CLIENT_ID'), getenv('CLIENT_SECRET'));

                        //SET TOKEN DE ACESSO
                        $ttlock->identityCard->setAccessToken(getenv('ACCESS_TOKEN'));

                        //DATA E HORA ATUAL EM MILISSEGUNDO
                        $dateTimeInputApi = $ttlock->getDateTimeMillisecond(date('Y-m-d H:i:s'));

                        //CONVERTE HORA INICIO E HORA FIM NO PADRÃO DATEIME
                        $dataTimeInit   = $mergeReservationsDay['data_agendamento'] . " " . $mergeReservationsDay['hora_inicio'];
                        $dateTimeFinish = $mergeReservationsDay['data_agendamento'] . " " . $mergeReservationsDay['hora_fim'];

                        //DATA INICIO E DATA FIM DA RESERVA EM MILISSEGUNDO
                        $startDateReservation = $ttlock->getDateTimeMillisecond($dataTimeInit);
                        $endDateReservartion = $ttlock->getDateTimeMillisecond($dateTimeFinish);

                        //VERIFICA SE O CRACHÁ DO RESPONSÁVEL SE JÁ FOI ENVIADO
                        if ($mergeReservationsDay[0]['responsavel_send_api'] != 1) {

                            //VERIFICA SE EXISTE O NÚMERO DO CRACHÁ DO RESPONSAVEL
                            if ($mergeReservationsDay[0]['responsavel_num_cracha'] != '') {


                                //GRAVA CRACHÁ DO RESPONSAVEL NA API
                                $returnApiResponsavel = $ttlock->identityCard->addCard($mergeReservationsDay['lockId'], $mergeReservationsDay[0]['responsavel_num_cracha'], $mergeReservationsDay[0]['responsavel_nome'], $startDateReservation, $endDateReservartion, $dateTimeInputApi);

                                //REGISTRAR RETORNO
                                if ($returnApiResponsavel == true) {

                                    //REGISTRAR O RETORNO POSITIVO NO BANCO DE DADOS
                                    self::updateChachaSendApi($mergeReservationsDay['id_agendamento'], 'Responsavel', 1);


                                    //RET0RNA SE GRAVOU O CRACHÁ RESPONSÁVEL
                                    echo "Crachá: " . $mergeReservationsDay[0]['responsavel_num_cracha'] . " do Resposável: " . $mergeReservationsDay[0]['responsavel_email'] . " - <b>salvo com sucesso via api</b>" . "<br/>";

                                }else{

                                    //CASO RETORNE COM ERRO, REGISTAR NO BANCO DE DADOS
                                    self::updateChachaSendApi($mergeReservationsDay['id_agendamento'], 'Responsavel', 2);

                                    //MSG DE ERRO API
                                    echo "Crachá: " . $mergeReservationsDay[0]['responsavel_num_cracha'] . " do Resposável: " . $mergeReservationsDay[0]['responsavel_email'] . " - <b>Error ao cadastrar o crachá via api</b>" . "<br/>";
                                }

                            } else {
                                //CASO NÃO EXISTIR O NÚMERO DO CRACHÁ RESPONSÁVEL
                                echo "Crachá Responsável não localizado" . "<br/>";
                            }

                        }else {
                            //CHACHÁ JÁ ESTÁ SALVO NA API
                            echo "Chachá Responsável já cadastrado na api." . "<br/>";
                        }

                        //VERIFICA SE O CRACHÁ DO RESPONSÁVEL SE JÁ FOI ENVIADO
                        if ($mergeReservationsDay[0]['solicitante_send_api'] != 1) {

                            //VERIFICA SE EXISTE O NÚMERO DO CRACHÁ SOLICITANTE
                            if ($mergeReservationsDay[0]['solicitante_num_cracha'] != '') {

                                //GRAVA CRACHÁ DO SOLICITANTE NA API
                                $returnApiSolicitante = $ttlock->identityCard->addCard($mergeReservationsDay['lockId'], $mergeReservationsDay[0]['solicitante_num_cracha'], $mergeReservationsDay[0]['solicitante_nome'], $mergeReservationsDay['hora_inicio'], $mergeReservationsDay['hora_fim'], $date);


                                //REGISTRAR RETORNO
                                if ($returnApiSolicitante == true) {

                                    //REGISTRAR O RETORNO POSITIVO NO BANCO DE DADOS
                                    self::updateChachaSendApi($mergeReservationsDay['id_agendamento'], 'Solicitante', 1);

                                    //RET0RNA SE GRAVOU O CRACHÁ SOLICITANTE
                                    echo "Crachá: " . $mergeReservationsDay[0]['solicitante_num_cracha'] . " do Solicitante: " . $mergeReservationsDay[0]['solicitante_email'] . " - <b>salvo com sucesso via api</b>" . "<br/>";

                                }else{

                                    //CASO RETORNE COM ERRO, REGISTAR NO BANCO DE DADOS
                                    self::updateChachaSendApi($mergeReservationsDay['id_agendamento'], 'Solicitante', 2);

                                    //MSG DE ERRO API
                                    echo "Crachá: " . $mergeReservationsDay[0]['solicitante_num_cracha'] . " do Resposável: " . $mergeReservationsDay[0]['solicitante_email'] . " - <b>Error ao cadastrar o crachá via api</b>" . "<br/>";
                                }

                            }else {
                                //CASO NÃO EXISTIR O NÚMERO DO CRACHÁ SOLICITNANTE
                                echo "Crachá Solicitante não localizado"  . "<br/>";
                            }

                        }else {
                            //CHACHÁ JÁ ESTÁ SALVO NA API
                            echo "Chachá Solicitante já cadastrado na api." . "<br/>";
                        }

                    }else {
                        //SE NÃO EXISTIR O LOCK ID REGISTRADO NO BANCO
                        echo "Número Sala e Lock ID não localizado!"  . "<br/>";
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