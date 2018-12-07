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
use kuriousagency\activecampaign\models\FormMapping as FormMappingModel;

use Craft;
use craft\helpers\ArrayHelper;
use craft\web\Controller;

/**
 * @author    Kurious Agency
 * @package   ActiveCampaign
 * @since     1.0.0
 */
class FormMappingController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = [];

    // Public Methods
    // =========================================================================

	public function actionIndex() {
		
		$forms = ActiveCampaign::$plugin->formMapping->getForms();

		$variables = [
			'forms' => $forms,
		];

		return $this->renderTemplate('activecampaign/_layouts/forms/index',$variables);

	}

	public function actionEdit(int $formId = null, FormMappingModel $formMapping = null)
	{

		$variables = [
			'formId'	  => $formId,
			'formMapping' => $formMapping,
		];

		if (!$variables['formMapping']) {
			if ($variables['formId']) {
				$variables['formMapping'] = ActiveCampaign::getInstance()->formMapping->getFormMappingByFormId($variables['formId']);

				if (!$variables['formMapping']) {
					throw new NotFoundHttpException('Form Mapping not found');
				}
			} else {
				$variables['formMapping'] = new FormMappingModel();
			}
		}

		$tags = ActiveCampaign::getInstance()->tags->getAllTags();
		$variables['tags'] = ArrayHelper::map($tags, 'id', 'name');
		$variables['fields'] = ActiveCampaign::getInstance()->fields->getAllFields();
		$variables['formFields'] = ActiveCampaign::getInstance()->formMapping->getFormFields($formId);

		// current values
		$variables['tagIds'] = json_decode($variables['formMapping']['tagsJson'],true);
		$variables['acFieldValues'] = json_decode($variables['formMapping']['fieldMappingJson'],true);

		// if ($variables['formMapping']->id) {
		// 	$variables['title'] = $variables['formMapping']->name;
		// } else {
		// 	$variables['title'] = 'Create a new ticket status';
		// }

		// Craft::dd($variables['acFieldValues']);
		$variables['title'] = "Form Mapping";

		return $this->renderTemplate('activecampaign/_layouts/forms/edit',$variables);

	}

	public function actionSave()
	{
		$this->requirePostRequest();
		$request = Craft::$app->getRequest();
		$id = $request->post('id');
		$formMapping = ActiveCampaign::getInstance()->formMapping->getFormMappingById($id);

		if (!$formMapping) {
            $formMapping = new FormMappingModel();
		}

		// Craft::dd($request->post('tags'));
		
		$formMapping->formId = $request->post('formId');
		$formMapping->fieldMappingJson = json_encode(array_filter($request->post('acField')));
		$formMapping->tagsJson = json_encode($request->post('tags'));

		// Save it
		$save = ActiveCampaign::getInstance()->formMapping->saveFormMapping($formMapping);

		if ($save) {
			Craft::$app->getSession()->setNotice('Form Mapping saved.');
			$this->redirectToPostedUrl();
		} else {
			Craft::$app->getSession()->setError('Couldn’t save form mappaing status.');
		}
 
		 Craft::$app->getUrlManager()->setRouteParams(compact('formMapping'));

	}
	
}
