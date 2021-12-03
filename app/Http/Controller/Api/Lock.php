<?php
/**
 *
 * Copyright  FaShop
 * License    http://www.fashop.cn
 * link       http://www.fashop.cn
 * Created by FaShop.
 * User: hanwenbo
 * Date: 2018/9/13
 * Time: 下午8:59
 *
 */

namespace App\Http\Controller\Api;


class Lock extends TTLockAbstract
{

	/**
	 * @var string
	 */
	private $accessToken = '';

    /**
     * @param string $accessToken
     * @return void
     */
	public function setAccessToken($accessToken)
	{
		$this->accessToken = $accessToken;
	}

	/**
	 * @param string $lockData
	 * @param string $lockAlias
	 * @param int    $date
	 * @return array
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function initialize($lockData, $lockAlias, $date )
	{
		$response = $this->client->request( 'POST', '/v3/lock/initialize', [
			'form_params' => [
				'clientId'    => $this->clientId,
				'accessToken' => $this->accessToken,
				'lockData'    => $lockData,
				'lockAlias'   => $lockAlias,
				'date'        => $date,
			],
		] );
		$body = json_decode( $response->getBody()->getContents(), true );
		if( $response->getStatusCode() === 200 && !isset( $body['errcode'] ) ){
			return (array)$body;
		} else{
			throw new \Exception( "errcode {$body['errcode']} errmsg {$body['errmsg']} errmsg : {$body['errmsg']}" );
		}

	}

	/**
	 * @param int $pageNo
	 * @param int $pageSize
	 * @param int $date
	 * @return array
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function list($pageNo, $pageSize, $date )
	{
		$response = $this->client->request( 'POST', '/v3/lock/list', [
			'form_params' => [
				'clientId'    => $this->clientId,
				'accessToken' => $this->accessToken,
				'pageNo'      => $pageNo,
				'pageSize'    => $pageSize,
				'date'        => $date,
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
	 * @param int $pageNo
	 * @param int $pageSize
	 * @param int $date
	 * @return array
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function listKey($lockId, $pageNo, $pageSize, $date )
	{
		$response = $this->client->request( 'POST', '/v3/lock/listKey', [
			'form_params' => [
				'clientId'    => $this->clientId,
				'accessToken' => $this->accessToken,
				'lockId'      => $lockId,
				'pageNo'      => $pageNo,
				'pageSize'    => $pageSize,
				'date'        => $date,
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
	 * @param string $lockId
	 * @param int    $date
	 * @return bool
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function deleteAllKey($lockId, $date )
	{
		$response = $this->client->request( 'POST', '/v3/lock/deleteAllKey', [
			'form_params' => [
				'clientId'    => $this->clientId,
				'accessToken' => $this->accessToken,
				'lockId'      => $lockId,
				'date'        => $date,
			],
		] );
		$body     = json_decode( $response->getBody()->getContents(), true );
		if( $response->getStatusCode() === 200 && isset( $body['errcode'] ) && $body['errcode'] === 0 ){
			return true;
		} else{
			throw new \Exception( "errcode {$body['errcode']} errmsg {$body['errmsg']} errmsg : {$body['errmsg']}" );
		}
	}

	/**
	 * @param int $lockId
	 * @param int $pageNo
	 * @param int $pageSize
	 * @param int $date
	 * @return array
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function listKeyboardPwd($lockId, $pageNo, $pageSize, $date )
	{
		$response = $this->client->request( 'POST', '/v3/lock/listKeyboardPwd', [
			'form_params' => [
				'clientId'    => $this->clientId,
				'accessToken' => $this->accessToken,
				'lockId'      => $lockId,
				'pageNo'      => $pageNo,
				'pageSize'    => $pageSize,
				'date'        => $date,
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
	 * @param int    $lockId
	 * @param string $password
	 * @param int    $date
	 * @return bool
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function changeAdminKeyboardPwd($lockId, $password, $date )
	{
		$response = $this->client->request( 'POST', '/v3/lock/changeAdminKeyboardPwd', [
			'form_params' => [
				'clientId'    => $this->clientId,
				'accessToken' => $this->accessToken,
				'lockId'      => $lockId,
				'password'    => md5( $password ),
				'date'        => $date,
			],
		] );
		$body     = json_decode( $response->getBody()->getContents(), true );
		if( $response->getStatusCode() === 200 && isset( $body['errcode'] ) && $body['errcode'] === 0 ){
			return true;
		} else{
			throw new \Exception( "errcode {$body['errcode']} errmsg {$body['errmsg']} errmsg : {$body['errmsg']}" );
		}
	}

	/**
	 * @param int    $lockId
	 * @param string $password
	 * @param int    $date
	 * @return bool
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function changeDeletePwd($lockId, $password, $date )
	{
		$response = $this->client->request( 'POST', '/v3/lock/changeDeletePwd', [
			'form_params' => [
				'clientId'    => $this->clientId,
				'accessToken' => $this->accessToken,
				'lockId'      => $lockId,
				'password'    => md5( $password ),
				'date'        => $date,
			],
		] );
		$body     = json_decode( $response->getBody()->getContents(), true );
		if( $response->getStatusCode() === 200 && isset( $body['errcode'] ) && $body['errcode'] === 0 ){
			return true;
		} else{
			throw new \Exception( "errcode {$body['errcode']} errmsg {$body['errmsg']} errmsg : {$body['errmsg']}" );
		}
	}

	/**
	 * @param int    $lockId
	 * @param string $lockAlias
	 * @param int    $date
	 * @return bool
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function rename($lockId, $lockAlias, $date )
	{
		$response = $this->client->request( 'POST', '/v3/lock/rename', [
			'form_params' => [
				'clientId'    => $this->clientId,
				'accessToken' => $this->accessToken,
				'lockId'      => $lockId,
				'lockAlias'   => $lockAlias,
				'date'        => $date,
			],
		] );
		$body     = json_decode( $response->getBody()->getContents(), true );
		if( $response->getStatusCode() === 200 && isset( $body['errcode'] ) && $body['errcode'] === 0 ){
			return true;
		} else{
			throw new \Exception( "errcode {$body['errcode']} errmsg {$body['errmsg']} errmsg : {$body['errmsg']}" );
		}
	}

	/**
	 * @param int $lockId
	 * @param     $date
	 * @return bool
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function resetKey($lockId, $date )
	{
		$response = $this->client->request( 'POST', '/v3/lock/resetKey', [
			'form_params' => [
				'clientId'    => $this->clientId,
				'accessToken' => $this->accessToken,
				'lockId'      => $lockId,
				'date'        => $date,
			],
		] );
		$body     = json_decode( $response->getBody()->getContents(), true );
		if( $response->getStatusCode() === 200 && isset( $body['errcode'] ) && $body['errcode'] === 0 ){
			return true;
		} else{
			throw new \Exception( "errcode {$body['errcode']} errmsg {$body['errmsg']} errmsg : {$body['errmsg']}" );
		}
	}

	/**
	 * @param int    $lockId
	 * @param string $pwdInfo
	 * @param int    $timestamp
	 * @param int    $date
	 * @return bool
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function resetKeyboardPwd($lockId, $pwdInfo, $timestamp, $date )
	{
		$response = $this->client->request( 'POST', '/v3/lock/resetKeyboardPwd', [
			'form_params' => [
				'clientId'    => $this->clientId,
				'accessToken' => $this->accessToken,
				'lockId'      => $lockId,
				'pwdInfo'     => $pwdInfo,
				'date'        => $date,
			],
		] );
		$body     = json_decode( $response->getBody()->getContents(), true );
		if( $response->getStatusCode() === 200 && isset( $body['errcode'] ) && $body['errcode'] === 0 ){
			return true;
		} else{
			throw new \Exception( "errcode {$body['errcode']} errmsg {$body['errmsg']} errmsg : {$body['errmsg']}" );
		}
	}

	/**
	 * @param int $lockId
	 * @param int $date
	 * @return array
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function getKeyboardPwdVersion($lockId, $date )
	{
		$response = $this->client->request( 'POST', '/v3/lock/getKeyboardPwdVersion', [
			'form_params' => [
				'clientId'    => $this->clientId,
				'accessToken' => $this->accessToken,
				'lockId'      => $lockId,
				'date'        => $date,
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
	 * @param int $electricQuantity
	 * @param int $date
	 * @return bool
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function updateElectricQuantity($lockId, $electricQuantity, $date )
	{
		$response = $this->client->request( 'POST', '/v3/lock/updateElectricQuantity', [
			'form_params' => [
				'clientId'         => $this->clientId,
				'accessToken'      => $this->accessToken,
				'lockId'           => $lockId,
				'electricQuantity' => $electricQuantity,
				'date'             => $date,
			],
		] );
		$body     = json_decode( $response->getBody()->getContents(), true );
		if( $response->getStatusCode() === 200 && isset( $body['errcode'] ) && $body['errcode'] === 0 ){
			return true;
		} else{
			throw new \Exception( "errcode {$body['errcode']} errmsg {$body['errmsg']} errmsg : {$body['errmsg']}" );
		}
	}

	/**
	 * @param string $receiverUsername
	 * @param string $lockIdList
	 * @param int    $date
	 * @return bool
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function transfer($receiverUsername, $lockIdList, $date )
	{
		$response = $this->client->request( 'POST', '/v3/lock/transfer', [
			'form_params' => [
				'clientId'         => $this->clientId,
				'accessToken'      => $this->accessToken,
				'receiverUsername' => $receiverUsername,
				'lockIdList'       => $lockIdList,
				'date'             => $date,
			],
		] );
		$body     = json_decode( $response->getBody()->getContents(), true );
		if( $response->getStatusCode() === 200 && isset( $body['errcode'] ) && $body['errcode'] === 0 ){
			return true;
		} else{
			throw new \Exception( "errcode {$body['errcode']} errmsg {$body['errmsg']} errmsg : {$body['errmsg']}" );
		}
	}
}