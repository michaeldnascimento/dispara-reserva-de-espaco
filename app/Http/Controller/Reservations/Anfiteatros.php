<?php

namespace App\Http\Controller\Reservations;

use App\Http\Controller\Api\TTLock;
use App\Http\Request;
use App\Model\Entity\Reservations as EntityReservations;
use PDOStatement;

class Anfiteatros {

    /**
     * Método responsável por gravar dados reservas/cracha na api
     * @param int $lockId
     * @param int $num_cracha
     * @param string $nome
     * @param string $data_agendamento
     * @param string $hora_inicio
     * @param string $hora_fim
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function addCardTTLock($lockId, $num_cracha, $nome, $data_agendamento, $hora_inicio, $hora_fim){

        //DEFINE AS CONFIGURAÇÕES DE ACESSO API
        $ttlock = New TTLock(getenv('CLIENT_ID'), getenv('CLIENT_SECRET'));

        //SET TOKEN DE ACESSO
        $ttlock->identityCard->setAccessToken(getenv('ACCESS_TOKEN'));

        //DATA E HORA ATUAL EM MILISSEGUNDO
        $dateTimeInputApi = $ttlock->getDateTimeMillisecond(date('Y-m-d H:i:s'));

        //CONVERTE HORA INICIO E HORA FIM NO PADRÃO DATEIME
        $dataTimeInit   = $data_agendamento. " " . $hora_inicio;
        $dateTimeFinish = $data_agendamento . " " . $hora_fim;

        //DATA INICIO E DATA FIM DA RESERVA EM MILISSEGUNDO
        $startDateReservation = $ttlock->getDateTimeMillisecond($dataTimeInit);
        $endDateReservartion = $ttlock->getDateTimeMillisecond($dateTimeFinish);

        //GRAVA CRACHÁ DO RESPONSAVEL NA API
        return $ttlock->identityCard->addCard($lockId, $num_cracha, $nome, $startDateReservation, $endDateReservartion, $dateTimeInputApi);

    }


    /**
     * Método responsável por gerenciar resultados e gravar solicitante na api
     * @param array $reservations
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function createdCrachaSolicitante($reservations)
    {

        //GRAVA O RESPONSÁVEL NA API E RETORNA O RESULTADO
        $returnApiSolicitante = self::addCardTTLock($reservations['lockId'], $reservations[0]['solicitante_num_cracha'], $reservations[0]['solicitante_nome'], $reservations['data_agendamento'], $reservations['hora_inicio'], $reservations['hora_fim']);

        //REGISTRAR RETORNO
        if ($returnApiSolicitante['cardId'] != '') {

            //REGISTRAR O RETORNO POSITIVO NO BANCO DE DADOS
            self::updateChachaSendApi($reservations['id_agendamento'], 'Solicitante', 1);

            //RET0RNA SE GRAVOU O CRACHÁ SOLICITANTE
            return "Crachá: " . $reservations[0]['solicitante_num_cracha'] . " do Solicitante: " . $reservations[0]['solicitante_email'] . " - <b>salvo com sucesso via api</b>";

        } elseif ($returnApiSolicitante['errcode'] == 1){


            //CASO RETORNE COM ERRO, REGISTAR NO BANCO DE DADOS
            self::updateChachaSendApi($reservations['id_agendamento'], 'Solicitante', 2);

            //MSG DE ERRO API
            return "Crachá: " . $reservations[0]['solicitante_num_cracha'] . " do Solicitante: " . $reservations[0]['solicitante_email'] . " - <b>Falha ao se conectar ". $reservations['sala'] ."</b>";

        } elseif ($returnApiSolicitante['errcode'] == -2012){

            //CASO RETORNE COM ERRO, REGISTAR NO BANCO DE DADOS
            self::updateChachaSendApi($reservations['id_agendamento'], 'Solicitante', 2);

            //MSG DE ERRO API
            return "Crachá: " . $reservations[0]['solicitante_num_cracha'] . " do Solicitante: " . $reservations[0]['solicitante_email'] . " - <b>Gatway Não conectado a fechadura ". $reservations['sala'] ."</b>";

        }else {

            //CASO RETORNE COM ERRO, REGISTAR NO BANCO DE DADOS
            self::updateChachaSendApi($reservations['id_agendamento'], 'Solicitante', 2);

            //MSG DE ERRO API
            return "Crachá: " . $reservations[0]['solicitante_num_cracha'] . " do Solicitante: " . $reservations[0]['solicitante_email'] . " - <b>Error ao cadastrar o crachá via api</b>";
        }


    }


    /**
     * Método responsável por gerenciar resultados e gravar responsável na api
     * @param array $reservations
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function createdCrachaResponsavel($reservations)
    {

        //GRAVA O RESPONSÁVEL NA API E RETORNA O RESULTADO
        $returnApiResponsavel = self::addCardTTLock($reservations['lockId'], $reservations[0]['responsavel_num_cracha'], $reservations[0]['responsavel_nome'], $reservations['data_agendamento'], $reservations['hora_inicio'], $reservations['hora_fim']);

        //REGISTRAR RETORNO
        if ($returnApiResponsavel['cardId'] != '') {

            //REGISTRAR O RETORNO POSITIVO NO BANCO DE DADOS
            self::updateChachaSendApi($reservations['id_agendamento'], 'Responsavel', 1);

            //RET0RNA SE GRAVOU O CRACHÁ RESPONSÁVEL
            return "Crachá: " . $reservations[0]['responsavel_num_cracha'] . " do Resposável: " . $reservations[0]['responsavel_email'] . " - <b>salvo com sucesso via api</b>";

        } elseif ($returnApiResponsavel['errcode'] == 1){

            //CASO RETORNE COM ERRO, REGISTAR NO BANCO DE DADOS
            self::updateChachaSendApi($reservations['id_agendamento'], 'Responsavel', 2);

            //MSG DE ERRO API
            return "Crachá: " . $reservations[0]['responsavel_num_cracha'] . " do Resposável: " . $reservations[0]['responsavel_email'] . " - <b>Falha ao se conectar ". $reservations['sala'] ."</b>";

        } elseif ($returnApiResponsavel['errcode'] == -2012){

            //CASO RETORNE COM ERRO, REGISTAR NO BANCO DE DADOS
            self::updateChachaSendApi($reservations['id_agendamento'], 'Responsavel', 2);

            //MSG DE ERRO API
            return "Crachá: " . $reservations[0]['responsavel_num_cracha'] . " do Resposável: " . $reservations[0]['responsavel_email'] . " - <b>Gatway Não conectado a fechadura ". $reservations['sala'] ."</b>";

        } else {

            //CASO RETORNE COM ERRO, REGISTAR NO BANCO DE DADOS
            self::updateChachaSendApi($reservations['id_agendamento'], 'Responsavel', 2);

            //MSG DE ERRO API
            return "Crachá: " . $reservations[0]['responsavel_num_cracha'] . " do Resposável: " . $reservations[0]['responsavel_email'] . " - <b>Error ao cadastrar o crachá via api</b>";
        }


    }


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

                    echo "<p>" . "ID Agendamento: ". $mergeReservationsDay['id_agendamento']. " / Sala: ".$mergeReservationsDay['sala']. "/ Data: ".$mergeReservationsDay['data_agendamento']. " / Hora Inicio: ".$mergeReservationsDay['hora_inicio']. "/ Hora Fim: " .$mergeReservationsDay['hora_fim']. "<p/>";


                    //VERIFICA SE EXISTE O LOCK ID NA SALA (LOCK ID FECHADURA POR SALA DE AULA)
                    if ($mergeReservationsDay['lockId'] != ''){

                        //VERIFICA SE O CRACHÁ DO RESPONSÁVEL SE JÁ FOI ENVIADO
                        if ($mergeReservationsDay[0]['responsavel_send_api'] != 1) {

                            //VERIFICA SE EXISTE O NÚMERO DO CRACHÁ DO RESPONSAVEL
                            if ($mergeReservationsDay[0]['responsavel_num_cracha'] != '') {

                                $msgReturno = self::createdCrachaResponsavel((array)$mergeReservationsDay);

                                echo $msgReturno . "<br/>";

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

                                $msgReturno = self::createdCrachaSolicitante((array)$mergeReservationsDay);

                                echo $msgReturno . "<hr/>";

                            }else {
                                //CASO NÃO EXISTIR O NÚMERO DO CRACHÁ SOLICITNANTE
                                echo "Crachá Solicitante não localizado"  . "<hr/>";
                            }

                        }else {
                            //CHACHÁ JÁ ESTÁ SALVO NA API
                            echo "Chachá Solicitante já cadastrado na api." . "<hr/>";
                        }

                    }else {
                        //SE NÃO EXISTIR O LOCK ID REGISTRADO NO BANCO
                        echo "Número Sala e Lock ID não localizado!"  . "<hr/>";
                    }


                } else {

                    echo "Contato não localizado" . "<hr/>";

                }

            }

        } else {

            return 'Não possui reservas Anfiteatro no dia';

        }

        return 'Carga Reservas Finalizada.';
    }


    }