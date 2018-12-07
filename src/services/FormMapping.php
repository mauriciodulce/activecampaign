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
use kuriousagency\activecampaign\models\FormMapping as FormMappingModel;
use kuriousagency\activecampaign\records\FormMapping as FormMappingRecord;

use Craft;
use craft\base\Component;
use craft\db\Query;
use Solspace\Freeform\Freeform;
use Solspace\Freeform\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;

/**
 * @author    Kurious Agency
 * @package   ActiveCampaign
 * @since     1.0.0
 */
class FormMapping extends Component
{
    // Public Methods
    // =========================================================================


	public function getForms()
	{

		$forms = [];
		
		$freeFormForms = Freeform::getInstance()->forms->getAllForms();

		foreach($freeFormForms as $form) {	
			$forms[] = [
				'id' => $form->id,
				'name' => $form->name,
				'tags' => ActiveCampaign::$plugin->tags->getTagNamesByFormId($form->id),
			];
		}

		return $forms;

	}

	/**
	 * returns array of free form fields for the form
	 */
	public function getFormFields($formId)
	{
		
		$fields = [];

		$form = Freeform::getInstance()->forms->getFormById($formId);

        if ($form) {
			foreach($form->getLayout()->getFields() as $field) {
				if($field->getId() > 0) {
					$fields[$field->getId()] = $field->getLabel();
				}
			}
		}

		// Craft::dd($fields);

		return $fields;

	}

	public function saveFormMapping(FormMappingModel $model)
	{

		if ($model->id) {
            $record = FormMappingRecord::findOne($model->id);

            if (!$record->id) {
                throw new Exception(Craft::t('activecampaign', 'No Active Campaign form mapping exists with the ID "{id}"',
                    ['id' => $model->id]));
            }
        } else {
            $record = new FormMappingRecord();
        }

		$record->formId = $model->formId;
		$record->fieldMappingJson = $model->fieldMappingJson;
		$record->tagsJson = $model->tagsJson;

		$db = Craft::$app->getDb();
        $transaction = $db->beginTransaction();

        try {

            // Save it
            $record->save(false);

            // Now that we have a record ID, save it on the model
            $model->id = $record->id;

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();

            throw $e;
        }

        return true;
	}

	public function getFormMappingByFormId($formId)
	{
		$result = $this->_createFieldMappingQuery()
		->where(['formId' => $formId])
		->one();

		return new FormMappingModel($result);
	}

	public function getFormMappingById($id)
	{
		$result = $this->_createFieldMappingQuery()
		->where(['id' => $id])
		->one();

		return new FormMappingModel($result);
	}
	
	private function _createFieldMappingQuery()
    {
        return (new Query())
            ->select([
                'id',
                'formId',
                'fieldMappingJson',
                'tagsJson',
            ])
            ->from(['{{%activecampaign_form_mapping}}']);
    }
}
