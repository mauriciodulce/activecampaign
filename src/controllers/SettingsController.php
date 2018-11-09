<?php
/**
 * Support plugin for Craft CMS 3.x
 *
 * Simple support system for tracking, prioritising and solving customer support tickets.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace kuriousagency\activecampaign\controllers;

use kuriousagency\activecampaign\ActiveCampaign;

use Craft;
use craft\web\Controller;

use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

class SettingsController extends Controller
{
    // Public Properties
    // =========================================================================

    public $settings;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        $this->settings = ActiveCampaign::$plugin->getSettings();
        if (!$this->settings->validate()) {
            throw new InvalidConfigException('Support settings don’t validate.');
        }
    }

    public function actionIndex()
    {
        // $systemEmail = Craft::$app->systemSettings->getSetting('email', 'fromEmail');
        // $systemSender = Craft::$app->systemSettings->getSetting('email', 'fromName');
        // $settings = $this->settings;
		// $plugin = Support::$plugin;
		
		$settings = $this->settings;

        $variables = [
			'settings' => $settings,
          	'apiKey'   => $settings->apiKey,
          	'account'  => $settings->account,
        ];

        return $this->renderTemplate('activecampaign/_layouts/settings', $variables);
    }
}
