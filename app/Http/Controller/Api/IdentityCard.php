<?php

namespace App\Http\Controller\Api;

class IdentityCard extends TTLockAbstract
{
    /**
     * @var string
     */
    private $accessToken = '';

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * https://api.ttlock.com/v3/identityCard/addForReversedCardNumber
     * @param int $lockId
     * @param int $cardNumber
     * @param string $cardName
     * @param int $startDate
     * @param int $endDate
     * @param int $date
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     * @author Michael D. Nascimento
     */
    public function addCard($lockId, $cardNumber, $cardName, $startDate, $endDate, $date)
    {

//        echo $lockId . '<br>';
//        echo $cardNumber . '<br>';
//        echo $cardName . '<br>';
//        echo $startDate . '<br>';
//        echo $endDate . '<br>';
//        echo $date . '<br>';
//        exit;
        $response = $this->client->request( 'POST', '/v3/identityCard/addForReversedCardNumber', [
            'form_params' => [
                'clientId'           => $this->clientId,
                'accessToken'        => $this->accessToken,
                'lockId'             => $lockId,
                'cardNumber'         => $cardNumber,
                'cardName'           => $cardName,
                'startDate'          => $startDate,
                'endDate'            => $endDate,
                'addType'            => 2, //2 = Add by cloud API via gateway
                'date'               => $date,
            ],
        ] );
        $body     = json_decode( $response->getBody()->getContents(), true );
        if( $response->getStatusCode() === 200 && !isset( $body['errcode'] ) ){
            return (array)$body;
        } else{
            throw new \Exception( "errcode {$body['errcode']} errmsg {$body['errmsg']} errmsg : {$body['errmsg']}" );
        }
    }

    /**
     * https://intranet.fm.usp.br/dispara-reserva-de-espaco/api/v3/identityCard/listCard
     * @param int $lockId
     * @param int $pageNo
     * @param int $pageSize
     * @param int $date
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     * @author Michael D. Nascimento
     */
    public function listCard($lockId, $pageNo, $pageSize, $date )
    {
        $response = $this->client->request( 'POST', '/v3/identityCard/list', [
            'form_params' => [
                'clientId'     => $this->clientId,
                'accessToken'  => $this->accessToken,
                'lockId'       => $lockId,
                'pageNo'       => $pageNo,
                'pageSize'     => $pageSize,
                'date'         => $date,
            ],
        ] );
        $body     = json_decode( $response->getBody()->getContents(), true );
        if( $response->getStatusCode() === 200 && !isset( $body['errcode'] ) ){
            return (array)$body;
        } else{
            throw new \Exception( "errcode {$body['errcode']} errmsg {$body['errmsg']} errmsg : {$body['errmsg']}" );
        }
    }

    /**
     * @param int $lockId
     * @param int $cardId
     * @param int $date
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     * @author 韩文博
     */
    public function delete($lockId, $cardId, $date)
    {
        $response = $this->client->request( 'POST', '/v3/identityCard/delete', [
            'form_params' => [
                'clientId'      => $this->clientId,
                'accessToken'   => $this->accessToken,
                'lockId'        => $lockId,
                'cardId'        => $cardId,
                'deleteType'    => 2,
                'date'          => $date,
            ],
        ] );
        $body     = json_decode( $response->getBody()->getContents(), true );
        if( $response->getStatusCode() === 200 && !isset( $body['errcode'] ) ){
            return (array)$body;
        } else{
            throw new \Exception( "errcode {$body['errcode']} errmsg {$body['errmsg']} errmsg : {$body['errmsg']}" );
        }
    }

}