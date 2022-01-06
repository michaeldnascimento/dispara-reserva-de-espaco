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


class User extends TTLockAbstract
{
	/**
	 * @param string $username
	 * @param string $password
	 * @param int    $date
	 * @return array
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function register($username, $password, $date)
	{
		$response = $this->client->request( 'POST', '/v3/user/register', [
			'form_params' => [
				'clientId'     => $this->clientId,
				'clientSecret' => $this->clientSecret,
				'username'     => $username,
				'password'     => md5($password),
				'date'         => $date,
			],
		] );
		$body     = json_decode( $response->getBody()->getContents(), true );
		if( $response->getStatusCode() === 200 ){
			return (array)$body;
		} else{
			throw new \Exception( "errcode {$body['errcode']} errmsg {$body['errmsg']} errmsg : {$body['errmsg']}",$body['errcode']);
		}

	}

	/**
	 * @param string $username
	 * @param string $password
	 * @param int    $date
	 * @return bool
	 * @throws \GuzzleHttp\Exception\GuzzleException  | \Exception
	 * @author 韩文博
	 */
	public function resetPassword($username, $password, $date )
	{
		$response = $this->client->request( 'POST', '/v3/user/resetPassword', [
			'form_params' => [
				'clientId'     => $this->clientId,
				'clientSecret' => $this->clientSecret,
				'username'     => $username,
				'password'     => md5($password),
				'date'         => $date,
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
	 * @param int $startDate
	 * @param int $endDate
	 * @param int $pageNo
	 * @param int $pageSize
	 * @param int $date
	 * @return array
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function listUser($startDate, $endDate, $pageNo, $pageSize, $date )
	{
		$response = $this->client->request( 'POST', '/v3/user/list', [
			'form_params' => [
				'clientId'     => $this->clientId,
				'clientSecret' => $this->clientSecret,
				'startDate'    => $startDate,
				'endDate'      => $endDate,
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
	 * @param string $username
	 * @param int    $date
	 * @return bool
	 * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
	 * @author 韩文博
	 */
	public function delete($username, $date )
	{
		$response = $this->client->request( 'POST', '/v3/user/delete', [
			'form_params' => [
				'clientId'     => $this->clientId,
				'clientSecret' => $this->clientSecret,
				'username'     => $username,
				'date'         => $date,
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