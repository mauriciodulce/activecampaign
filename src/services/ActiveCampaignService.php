<?php
/**
 * ActiveCampaign plugin for Craft CMS 3.x
 *
 * Active Campaign plugin for Craft CMS
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

namespace kuriousagency\activecampaign\services;

use kuriousagency\activecampaign\ActiveCampaign;

use Craft;
use craft\base\Component;
use GuzzleHttp\Client;

/**
 * @author    Kurious Agency
 * @package   ActiveCampaign
 * @since     1.0.0
 */
class ActiveCampaignService extends Component
{
    // Public Methods
    // =========================================================================


	public function init()
    {
        $settings = ActiveCampaign::$plugin->getSettings();
        $this->apiKey = $settings->apiKey;
        $this->account = $settings->account;

        $this->client = new Client("https://{$this->account}.api-us1.com");
        
        parent::init();
    }

    /*
     * @return mixed
     */
    public function createContact()
    {
		
		// $client = new Client;
		// $response = $client->request('POST', $endpoint, [
		// 	'form_params' => [
		// 		'applicationNo' => $applicationNo, 	//Application number
		// 		'id' => $this->_accountId,			//username
		// 		'id2' => $this->_password,			//pasword
		// 		'status' => $status					//status  G or C see above 
		// 	]
		// ]);	
		
		
       
      

        return $result;
    }
}
