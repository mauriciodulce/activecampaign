<?php
/**
 * ActiveCampaign plugin for Craft CMS 3.x
 *
 * Active Campaign plugin for Craft CMS
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

namespace kuriousagency\activecampaign\assetbundles\ActiveCampaign;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Kurious Agency
 * @package   ActiveCampaign
 * @since     1.0.0
 */
class ActiveCampaignAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@kuriousagency/activecampaign/assetbundles/activecampaign/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/ActiveCampaign.js',
        ];

        $this->css = [
            'css/ActiveCampaign.css',
        ];

        parent::init();
    }
}
