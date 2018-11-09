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
use kuriousagency\activecampaign\records\Tag as TagRecord;

use Craft;
use craft\base\Component;
use craft\db\Query;

/**
 * @author    Kurious Agency
 * @package   ActiveCampaign
 * @since     1.0.0
 */
class Tags extends Component
{
    // Public Methods
    // =========================================================================


	public function syncTags()
	{
		$response = ActiveCampaign::$plugin->api->get('tags');

		foreach($response->tags as $tag) {

			$tagModel = $this->getTagById($tag->id);

			if (!$tagModel) {
				$tagModel = new TagModel();
			}

			$tagModel->id = $tag->id;
			$tagModel->name = $tag->tag;

			$this->saveTag($tagModel);

		}

		// foreach($response as )

		return true;
	}

	public function getAllTags()
	{
		$tags = [];

		$rows = $this->_createTagQuery()->all();

		foreach ($rows as $row) {
            $tags[] = new TagModel($row);
		}
		
		return $tags;
	}

	public function saveTag(TagModel $model)
	{

		$record = TagRecord::findOne($model->id);

		if(!$record) {
			$record = new TagRecord();
			$record->id = $model->id;
		}

		$record->name = $model->name;

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

	public function getTagById($id)
    {
        $result = $this->_createTagQuery()
            ->where(['id' => $id])
            ->one();

        return new TagModel($result);
	}
	
	private function _createTagQuery()
    {
        return (new Query())
            ->select([
                'id',
                'name',
            ])
            ->from(['{{%activecampaign_tag}}']);
    }
}
