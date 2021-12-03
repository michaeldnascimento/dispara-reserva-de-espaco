<?php
/**
 *
 * Copyright  FaShop
 * License    http://www.fashop.cn
 * link       http://www.fashop.cn
 * Created by FaShop.
 * User: hanwenbo
 * Date: 2018/9/13
 * Time: 下午9:28
 *
 */

namespace App\Http\Controller\Api;

use \App\Common\Environment;
use GuzzleHttp\Client;

/**
 * Class BaseAbstract
 */
abstract class TTLockAbstract
{

	/**
	 * @var string
	 */
	protected $clientId = '';
	/**
	 * @var string
	 */
	protected $clientSecret = '';
	/**
	 * @var Client
	 */
	protected $client;

	final function __construct($clientId, $clientSecret, Client $client )
	{
        $this->clientId     = $clientId;
		$this->clientSecret = $clientSecret;
		$this->client       = $client;
	}

	protected function getMillisecond() {
		list($t1, $t2) = explode(' ', microtime());
		return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
	}
}