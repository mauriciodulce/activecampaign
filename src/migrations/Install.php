<?php
/**
 * ActiveCampaign plugin for Craft CMS 3.x
 *
 * Active Campaign plugin for Craft CMS
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */


namespace kuriousagency\activecampaign\migrations;

use kuriousagency\activecampaign\ActiveCampaign;
use kuriousagency\activecampaign\records\Field as FieldRecord;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;
use craft\helpers\MigrationHelper;

/**
 * @author    Kurious Agency
 * @package   ActiveCampaign
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            // $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

   /**
     * @inheritdoc
     */
    public function safeDown()
    {
		$this->driver = Craft::$app->getConfig()->getDb()->driver;
		$this->dropForeignKeys();
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%activecampaign_field}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%activecampaign_field}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
					 // Custom columns in the table
					'acFieldId' => $this->integer(255),
					'name' => $this->string(255)->notNull()->defaultValue(''),
					'handle' => $this->string(255)->notNull()->defaultValue(''),
                ]
			);
			
			$this->createTable(
                '{{%activecampaign_form_mapping}}',
                [
                    'id'            => $this->primaryKey(),
                    'dateCreated'   => $this->dateTime()->notNull(),
                    'dateUpdated'   => $this->dateTime()->notNull(),
                    'uid'           => $this->uid(),
					// Custom columns in the table
					'formId' => $this->integer(), // freeform form id
					'fieldMappingJson' => $this->text(), //handle=>id
					'tagsJson' => $this->text(),
                ]
			);

			$this->createTable(
                '{{%activecampaign_tag}}',
                [
                    'id'            => $this->primaryKey(),
                    'dateCreated'   => $this->dateTime()->notNull(),
                    'dateUpdated'   => $this->dateTime()->notNull(),
                    'uid'           => $this->uid(),
					// Custom columns in the table
					'name' => $this->text(),
                ]
			);

        }

        return $tablesCreated;
    }

    // /**
    //  * @return void
    //  */
    // protected function createIndexes()
    // {
    //     $this->createIndex(
    //         $this->db->getIndexName(
    //             '{{%activecampaign_field}}',
    //             'some_field',
    //             true
    //         ),
    //         '{{%activecampaign_field}}',
    //         'some_field',
    //         true
    //     );
    //     // Additional commands depending on the db driver
    //     switch ($this->driver) {
    //         case DbConfig::DRIVER_MYSQL:
    //             break;
    //         case DbConfig::DRIVER_PGSQL:
    //             break;
    //     }
    // }

    /**
     * @return void
     */
    protected function addForeignKeys()
    {
        // $this->addForeignKey(
        //     $this->db->getForeignKeyName('{{%activecampaign_field}}', 'siteId'),
        //     '{{%activecampaign_field}}',
        //     'siteId',
        //     '{{%sites}}',
        //     'id',
        //     'CASCADE',
        //     'CASCADE'
		// );
		
		$this->addForeignKey(null, '{{%activecampaign_form_mapping}}', ['formId'], '{{%freeform_forms}}', ['id'], null, 'CASCADE');
    }

    /**
     * @return void
     */
    protected function insertDefaultData()
    {
		// Default fields
		$data = [
            'name'      => 'First Name',
            'handle'    => 'FIRSTNAME',
        ];
		$this->insert(FieldRecord::tableName(), $data);
		
		$data = [
            'name'      => 'Last Name',
            'handle'    => 'LASTNAME',
        ];
		$this->insert(FieldRecord::tableName(), $data);
		
		$data = [
            'name'      => 'Email',
            'handle'    => 'EMAIL',
        ];
		$this->insert(FieldRecord::tableName(), $data);
		
		$data = [
            'name'      => 'Phone',
            'handle'    => 'PHONE',
        ];
		$this->insert(FieldRecord::tableName(), $data);
		
		$data = [
            'name'      => 'Note',
            'handle'    => 'NOTE',
        ];
        $this->insert(FieldRecord::tableName(), $data);
		
	}
	
	protected function dropForeignKeys()
    {
        // MigrationHelper::dropAllForeignKeysOnTable('{{%activecampaign_fieldmapping}}', $this);
    }

    /**
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists('{{%activecampaign_field}}');
        $this->dropTableIfExists('{{%activecampaign_form_mapping}}');
        $this->dropTableIfExists('{{%activecampaign_tag}}');
    }
}
