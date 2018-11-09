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
use kuriousagency\activecampaign\models\Tag as TagModel;

use Craft;
use craft\base\Component;
use craft\db\Query;

/**
 * @author    Kurious Agency
 * @package   ActiveCampaign
 * @since     1.0.0
 */
class Contacts extends Component
{
    // Public Methods
    // =========================================================================


    /*
     * @return mixed
     */
    public function createContact($formId)
    {
		
		$data['contact'] = [
			'email' => $formData->email,
			'firstName' => $formData->firstName,
			'lastName' => $formData->test,
		];

		$response = ActiveCampaign::$plugin->api->post('contacts', ['json'=>$data]);

        return $response;
	}
	
	public function getCustomFields()
	{
		
		$response = ActiveCampaign::$plugin->api->get('fields');



		return $response;

	}

	public function getTags()
	{
		$response = ActiveCampaign::$plugin->api->get('tags');

		

		return $response;
	}
}
