<?php
/**
 * Active Campaign plugin for Craft CMS 3.x
 *
 * ActiveCampaign
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

namespace kuriousagency\activecampaign\services;

use kuriousagency\activecampaign\ActiveCampaign;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

use Craft;
use craft\base\Component;

/**
 * @author    Kurious Agency
 * @package   ActiveCampaign
 * @since     2.0.0
 */
class Api extends Component
{
	private $_account;
	private $_apiKey;

	public function init()
    {
        $settings = ActiveCampaign::$plugin->getSettings();
        $this->_apiKey = $settings->apiKey;
        $this->_account = $settings->account;
        
        parent::init();
    }

    // Public Methods
	// =========================================================================

	public function get($uri, $params=[])
	{
		return $this->_request($uri, $params);
		//Craft::dd($response);
	}

	public function post($uri, $params=[])
	{
		return $this->_request($uri, $params, 'POST');
	}


	// Private Methods
	// =========================================================================
	
	private function _request($uri, $params=[], $method="GET", $type="json")
	{
		$url = 'https://'.$this->_account.'.api-us1.com/api/3/';
		$client = new Client(['base_uri'=>$url]);
		//Craft::dd($url);

		$params = array_merge_recursive([
			'headers' => [
				'Api-Token' => $this->_apiKey,
				'Accept' => 'application/json',
			]
		], $params);

		try {
			//$request = new Request($method, $uri, $params);
			//Craft::dd($request);
			//$response = $client->send($request);
			$response = $client->request($method, $uri, $params);
			//Craft::dd($response->getBody());
			return json_decode($response->getBody());

		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
}
