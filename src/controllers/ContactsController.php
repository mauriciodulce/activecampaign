<?php
/**
 * ActiveCampaign plugin for Craft CMS 3.x
 *
 * Active Campaign plugin for Craft CMS
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

namespace kuriousagency\activecampaign\controllers;

use kuriousagency\activecampaign\ActiveCampaign;

use Craft;
use craft\web\Controller;

/**
 * @author    Kurious Agency
 * @package   ActiveCampaign
 * @since     1.0.0
 */
class ContactsController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['create-or-update'];

    // Public Methods
    // =========================================================================


	public function actionCreateOrUpdate() {
		
		$this->requirePostRequest();
		$this->requireAcceptsJson();
		
		$data = json_decode(Craft::$app->getRequest()->getRequiredBodyParam('formData'),true);

		ActiveCampaign::$plugin->contacts->createOrUpdateContact($data['formId'],$data['fieldData']);
       
        return $this->asJson(['success' => true]);

	}

}
