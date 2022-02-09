<?php

namespace App\Model\Entity;

use \App\Db\Database;
use \PDO;
use PDOException;
use PDOStatement;

class Reservations {

    /**
    * ID Reserva
    * @var integer
    */
    public $id;

    /**
     * Nome disciplina
     * @var string
     */
    public $nomeDisciplina;

    /**
     * Data agendamento
     *  @var string
     */
    public $dataAgendamento;

    /**
    * Hora Inicio
     *  @var string
    */
    public $horaInicio;

    /**
     * Hora Fim
     *  @var string
     */
    public $horaFim;

    /**
     * Sala
     *  @var string
     */
    public $sala;

    /**
     * Número
     *  @var string
     */
    public $numero;

    /**
     * Situação
     *  @var string
     */
    public $reservado;

    /**
     * nome Curso
     *  @var string
     */
    public $nomeCurso;

    /**
     * Tipos Reservas ID
     *  @var int
     */
    public $tipoReservasId;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return bool
     */
    public function cadastrar()
    {

        $this->created_date = date('Y-m-d H:i:s');
        $this->updated_date = date('Y-m-d H:i:s');

        //INSERE A INSTANCIA NO BANCO
        $this->id = (new Database('nefroz','usuarios'))->insert([
            'nome'  => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha,
            'created_date' => $this->created_date,
            'updated_date' => $this->updated_date
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar se foi salvo o chachá solicitante na api
     * @param int $id_agendamento
     * @param int $value
     * @return bool
     */
    public static function updateSolicitanteChacha($id_agendamento, $value)
    {

        //REGISTRAR SE FOI ENVIADO O CHACHÁ SOLICITANTE VIA API
        return (new Database('anfiteatrofm','agendamentos_contatos'))->update('id_agendamento = '. $id_agendamento, [
            'solicitante_send_api'  => $value
        ]);
    }

    /**
     * Método responsável por atualizar se foi salvo o chachá responsavel na api
     * @param int $id_agendamento
     * @param int $value
     * @return bool
     */
    public static function updateReponsavelChacha($id_agendamento, $value)
    {

        //REGISTRAR SE FOI ENVIADO O CHACHÁ RESPONSÁVEL VIA API
        return (new Database('anfiteatrofm','agendamentos_contatos'))->update('id_agendamento = '. $id_agendamento, [
            'responsavel_send_api'  => $value
        ]);
    }

    /**
     * Método responsável por excluir um usuário do banco de dados
     * @return boolean
     */
    public function excluir()
    {
        //EXCLUI O DEPOIMENTO DO BANCO DE DADOS
        return (new Database('nefroz','usuarios'))->delete('id = '.$this->id);
    }


    /**
     * Método responsável por retornar um usuário com base no seu ID
     *
     * @param integer $id
     * @return Reservations
     */
    public static function getUserById($id)
    {
        return self::getUsers('id = '.$id)->fetchObject(self::class);
    }

    /**
     * Método responsavel por retornar um usuário com base em seu e-mail
     * @param int $numSala
     * @return Reservations
     */
    public static function getLockIdSala($numSala)
    {

        return self::getReservationsDay(
            'lockId',
            'salas',
            '',
            'numero =  '.$numSala.' ',
            '',
            ''
        );
    }

    /**
     * Método responsavel por retornar um usuário com base em seu e-mail
     * @param int $id
     * @return Reservations
     */
    public static function getContactReservations($id)
    {

        return self::getReservationsDay(
            'id_agendamento,
                    responsavel_nome,
                    responsavel_nome,
                    responsavel_email,
                    responsavel_num_cracha,
                    responsavel_send_api,
                    solicitante_nome,
                    solicitante_email,
                    solicitante_num_cracha,
                    solicitante_send_api',
            'agendamentos_contatos',
            '',
            'id_agendamento =  '.$id.' ',
            '',
            ''
        );
    }

    /**
     * Método responsavel por retornar as reservas do dia
     * @param string $key
     * @return Reservations
     */
    public static function getReservationsDayByKey($key)
    {

        return self::getReservationsDay(
            'agendamentos.id as id_agendamento,
                    agendamentos.data_agendamento,
                    agendamentos.hora_inicio,
                    agendamentos.hora_fim,
                    
                    reservas.nome_disciplina,
                    salas.sala,
                    salas.numero,
                    salas.lockId,
                    
                    reserva_situacao.situacao,
                    reservas.id_reserva_situacao,
                    
                    tipos_reserva.nome,
                    tipos_reserva.id as tipos_reservaid',
            'reservas',
            'agendamentos ON reservas.id_agendamento = agendamentos.id
                  INNER JOIN salas ON reservas.id_sala = salas.id
                  INNER JOIN reserva_situacao ON reservas.id_reserva_situacao = reserva_situacao.id
                  INNER JOIN tipos_reserva ON reservas.id_tipo_reserva = tipos_reserva.id',
            '(reservas.chave = "'. $key.'" AND reservas.id_reserva_situacao = 4) 
                    AND (salas.numero = "1303" OR salas.numero = "3104" OR salas.numero = "3303" 
                    OR salas.numero = "2104" OR salas.numero = "4303" OR salas.numero = "2303" 
                    OR salas.numero = "1104" OR salas.numero = "4104" OR salas.numero = "1" OR salas.numero = "s/n")',
            'salas.andar,reservas.id_sala, agendamentos.hora_inicio, agendamentos.hora_fim ASC',
            ''
        );
       //return (new Database('usuarios'))->select('email = "'. $email.'"')->fetchObject(self::class);
    }


    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getReservationsDay($fields = null, $from = null, $join = null, $where = null, $order = null, $limit = null)
    {
        return (new Database('anfiteatrofm', $from))->select($fields, $join, $where, $order, $limit);
    }

}