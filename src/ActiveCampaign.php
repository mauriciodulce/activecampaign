<?php
/**
 * ActiveCampaign plugin for Craft CMS 3.x
 *
 * Active Campaign plugin for Craft CMS
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

namespace kuriousagency\activecampaign;

use kuriousagency\activecampaign\services\Api as ApiService;
use kuriousagency\activecampaign\services\Contacts as ContactsService;
use kuriousagency\activecampaign\services\Tags as TagsService;
use kuriousagency\activecampaign\services\Fields as FieldsService;
use kuriousagency\activecampaign\services\FormMapping as FormMappingService;
use kuriousagency\activecampaign\services\Tracking as TrackingService;

use kuriousagency\activecampaign\variables\ActiveCampaignVariable;
use kuriousagency\activecampaign\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use Solspace\Freeform\Services\FormsService;
use Solspace\Freeform\Services\SubmissionsService;
use Solspace\Freeform\Events\Forms\AfterSubmitEvent;
use Solspace\Freeform\Events\Submissions\SubmitEvent;


use yii\base\Event;

/**
 * Class ActiveCampaign
 *
 * @author    Kurious Agency
 * @package   ActiveCampaign
 * @since     1.0.0
 *
 * @property  ContactsService $activeCampaignService
 */
class ActiveCampaign extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var ActiveCampaign
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
		self::$plugin = $this;
		
		$this->setComponents([
			'api' => ApiService::class,
			'contacts' => ContactsService::class,
			'tags' => TagsService::class,
			'fields' => FieldsService::class,
			'formMapping' => FormMappingService::class,
			'tracking' => TrackingService::class,
		]);

        // Event::on(
        //     UrlManager::class,
        //     UrlManager::EVENT_REGISTER_SITE_URL_RULES,
        //     function (RegisterUrlRulesEvent $event) {
        //         $event->rules['siteActionTrigger1'] = 'activecampaign/default';
        //     }
        // );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['activecampaign/settings'] = 'activecampaign/settings/index';
                $event->rules['activecampaign/forms'] = 'activecampaign/form-mapping/index';
                $event->rules['activecampaign/forms/<formId:\d+>'] = 'activecampaign/form-mapping/edit';
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('activeCampaign', ActiveCampaignVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
		);
		
		// Freeform events
		Event::on(
            FormsService::class,
            FormsService::EVENT_AFTER_SUBMIT,
            function (AfterSubmitEvent $event) {
                $form  = $event->getForm();
				$submission = $event->getSubmission();

				foreach($submission->fieldMetadata as $fieldData) {
					$data[$fieldData->getId()] = $fieldData->getValue();
				}

				ActiveCampaign::$plugin->contacts->createOrUpdateContact($submission->formId,$data);
            
            }
		);
		
		// Event::on(
        //     SubmissionsService::class,
        //     SubmissionsService::EVENT_AFTER_SUBMIT,
        //     function (SubmitEvent $event) {
        //         $submission = $event->getElement();
		// 		$form       = $event->getForm();

		// 		echo "<pre>";
		// 		print_r($submission);
		// 		echo "</pre>";
			

				
		// 		// Craft::d($submission);
			
        //     }
        // );
		
		


        Craft::info(
            Craft::t(
                'activecampaign',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
	}
	
	public function getCpNavItem()
    {
        $ret = parent::getCpNavItem();

        $ret['label'] = $this->name;

        $ret['subnav']['forms'] = [
            'label' => 'Forms',
            'url'   => 'activecampaign/forms',
        ];

        if (Craft::$app->getUser()->getIsAdmin()) {
            $ret['subnav']['settings'] = [
                'label' => 'Settings',
                'url'   => 'activecampaign/settings',
            ];
        }

        return $ret;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
		
		$settings = $this->getSettings();
        $settings->validate();
		
		return Craft::$app->view->renderTemplate(
            'activecampaign/settings',
            [
                'settings' => $settings
            ]
        );
    }
}
