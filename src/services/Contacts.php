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
    public function createOrUpdateContact($formId,$formData)
    {
		$formatedData = $this->_formatData($formId,$formData);

		$data['contact'] = [];

		if(array_key_exists('EMAIL',$formatedData)) {
			$data['contact']['email'] = $formatedData['EMAIL'][0];
			unset($formatedData['EMAIL']);
		}

		if(array_key_exists('FIRSTNAME',$formatedData)) {
			$data['contact']['firstName'] = $formatedData['FIRSTNAME'];
			unset($formatedData['FIRSTNAME']);
		}

		if(array_key_exists('LASTNAME',$formatedData)) {
			$data['contact']['lastName'] = $formatedData['LASTNAME'];
			unset($formatedData['LASTNAME']);
		}

		if(array_key_exists('PHONE',$formatedData)) {
			$data['contact']['phone'] = $formatedData['PHONE'];
			unset($formatedData['PHONE']);
		}

		if(array_key_exists('email',$data['contact'])) {

			$response = ActiveCampaign::$plugin->api->post('contact/sync', ['json'=>$data]);

			// update custom fields
			$contactId = $response->contact->id;

			if($contactId > 0) {
				
				foreach($formatedData as $handle=>$value) {

					// custom field values
					if(!is_array($value)) {
						$fieldModel = ActiveCampaign::$plugin->fields->getFieldByAttribute(['handle'=>$handle]);

						$this->createCustomFieldValue($contactId,$fieldModel->acFieldId,$value);
					}

					// notes
					if($handle == "NOTE") {
						$this->addNote($contactId,$value);
					}
					
				}

				// add tags
				$tagIds = ActiveCampaign::$plugin->tags->getTagsByFormId($formId);

				foreach($tagIds as $tagId) {
					$this->addTag($contactId,$tagId);
				}

			}

		}

		return true;
	}

	/***
	 * create the contacts custom field value
	 */
	public function createCustomFieldValue($contactId,$fieldId,$value)
	{
		
		$data['fieldValue'] = [
			'contact' => $contactId,
			'field' => $fieldId,
			'value' => $value,
		];
		
		$response = ActiveCampaign::$plugin->api->post('fieldValues/', ['json'=>$data]);

		return true;

	}

	/***
	 * add tags
	 */
	public function addTag($contactId,$tagId)
	{
		$data['contactTag'] = [
			'contact' => $contactId,
			'tag' => $tagId,
		];
		
		$response = ActiveCampaign::$plugin->api->post('contactTags', ['json'=>$data]);

	}

	public function addNote($contactId,$note)
	{
		$data['note'] = [
			'note' => $note,
			'relid' => $contactId,
			"reltype" => "Subscriber",
		];
		
		$response = ActiveCampaign::$plugin->api->post('notes', ['json'=>$data]);
	}

	// public function getCustomFields()
	// {
	// 	return ActiveCampaign::$plugin->api->get('fields');
	// }

	private function _formatData($formId,$data)
	{
		$acData = [];
		
		$formMappingModel = ActiveCampaign::getInstance()->formMapping->getFormMappingByFormId($formId);
		$formMapping = json_decode($formMappingModel['fieldMappingJson'],true);

		foreach($formMapping as $formFieldId => $acField) {
			$acData[$acField] = $data[$formFieldId];
		}

		// echo "<pre>";
		// print_r($acData);
		// echo "</pre>";
		// exit();

		return $acData;
	
		
	}
	
}
