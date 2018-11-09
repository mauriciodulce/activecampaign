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
use kuriousagency\activecampaign\models\Field as FieldModel;
use kuriousagency\activecampaign\records\Field as FieldRecord;

use Craft;
use craft\base\Component;
use craft\db\Query;

/**
 * @author    Kurious Agency
 * @package   ActiveCampaign
 * @since     1.0.0
 */
class Fields extends Component
{
    // Public Methods
    // =========================================================================


	public function syncFields()
	{
		$response = ActiveCampaign::$plugin->api->get('fields');

		// echo "<pre>";
		// print_r($response->fields);
		// echo "</pre>";
		// exit();

		foreach($response->fields as $field) {

			$fieldModel = $this->getFieldByHandle($field->perstag);

			if (!$fieldModel) {
				$fieldModel = new FieldModel();
			}

			$fieldModel->name = $field->title;
			$fieldModel->handle = $field->perstag;

			$this->saveField($fieldModel);

		}

		return true;
	}

	public function getAllFields()
	{
		$tags = [];

		$rows = $this->_createFieldQuery()->all();

		foreach ($rows as $row) {
            $tags[] = new FieldModel($row);
		}
		
		return $tags;
	}

	public function saveField(FieldModel $model)
	{

		if ($model->id) {
            $record = FieldRecord::findOne($model->id);

            if (!$record->id) {
                throw new Exception(Craft::t('activecampaign', 'No Active Campaign field exists with the ID "{id}"',
                    ['id' => $model->id]));
            }
        } else {
            $record = new FieldRecord();
        }

		$record->name = $model->name;
		$record->handle = $model->handle;

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

	public function getFieldByHandle($handle)
    {
        $result = $this->_createFieldQuery()
            ->where(['handle' => $handle])
            ->one();

        return new FieldModel($result);
	}
	
	private function _createFieldQuery()
    {
        return (new Query())
            ->select([
                'id',
                'name',
                'handle',
            ])
            ->from(['{{%activecampaign_field}}']);
    }
}
