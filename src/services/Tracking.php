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
class Tracking extends Component
{

	/**
	 * NOTICE: this API endpoint is currently not working - 404 returned.
	 * 
	 *  */	
	// public function getSiteTracking()
	// {
		
	// 	if($this->getTrackingStatus()) {
	// 		$response = ActiveCampaign::$plugin->api->get('track/site/code');
	// 	}

	// 	return $response->siteTracking;
	// }
	
}
