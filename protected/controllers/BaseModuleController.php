<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class BaseModuleController extends BaseController
{
    /* @var Institution - the institution that user is logged in as for current action action */
    public $institution;
    /* @var Firm - the firm that user is logged in as for current action action */
    public $firm;
    /* @var string alias path for the module of this controller */
    public $modulePathAlias;
    /* @var string alias path to asset files for the module */
    public $assetPathAlias;
    /* @var string path to asset files for the module */
    public $assetPath;
    /* @var EventType event type for this controller */
    private $_event_type;
    /* @var string css class for the module */
    public $moduleNameCssClass = '';

    /**
     * Determines the assetPath for the controller from the module.
     */
    public function init()
    {
        $this->modulePathAlias = 'application.modules.'.$this->getModule()->name;
        $this->assetPathAlias = $this->modulePathAlias.'.assets';

        // Set asset path
        if (file_exists(Yii::getPathOfAlias($this->assetPathAlias))) {
            $this->assetPath = Yii::app()->assetManager->getPublishedPathOfAlias('application.modules.'.$this->getModule()->name.'.assets');
        }

        return parent::init();
    }

    /**
     * The EventType class for this module.
     *
     * @return EventType
     */
    public function getEvent_type()
    {
        if (!$this->_event_type) {
            $this->_event_type = EventType::model()->find('class_name=?', array($this->getModule()->name));
        }

        return $this->_event_type;
    }

    /**
     * Sets the firm property on the controller from the session.
     *
     * @throws CHttpException
     */
    protected function setFirmFromSession()
    {
        if (!$firm_id = Yii::app()->session->get('selected_firm_id')) {
            throw new CHttpException(400, 'Firm not selected');
        }
        if (!$this->firm || $this->firm->id != $firm_id) {
            $this->firm = Firm::model()->findByPk($firm_id);
        }
    }

    protected function setInstitutionFromSession()
    {
        if (!$institution_id = Yii::app()->session->get('selected_institution_id')) {
            throw new CHttpException(400, 'Institution not selected');
        }
        if (!$this->institution || $this->institution->id != $institution_id) {
            $this->institution = Institution::model()->findByPk($institution_id);
        }
    }

    /**
     * Sets up various standard js and css files for modules.
     *
     * @param CAction $action
     *
     * @return bool
     *              (non-phpdoc)
     *
     * @see parent::beforeAction($action)
     */
    protected function beforeAction($action)
    {
        if ($this->event_type && $this->event_type->disabled) {
            // disabled module
            $this->redirectToPatientLandingPage();
        }

        // Set the module CSS class name.
        $this->moduleNameCssClass = strtolower($this->module->id);

        // Register module assets.
        $this->registerAssets();

        return parent::beforeAction($action);
    }

    /**
     * Automatic include of various standard assets based on class and module name (including module inheritance).
     */
    protected function registerAssets()
    {
        if ($this->assetPath) {
            $assetManager = Yii::app()->getAssetManager();

            $module = $this->getModule();
            $paths = array();
            foreach (array_reverse($module->getModuleInheritanceList()) as $inherited) {
                $paths[] = $inherited->name;
            }
            $paths[] = $module->name;

            $controller_name = Helper::getNSShortname($this);
            // Register print css
            $newblue_path = 'application.assets.newblue';
            $assetManager->registerCssFile('dist/css/style_oe_print.3.css', $newblue_path, null, AssetManager::OUTPUT_PRINT);
            foreach ($paths as $p) {
                $asset_path_alias = 'application.modules.'.$p.'.assets';
                // Register module js
                if (file_exists(Yii::getPathOfAlias($asset_path_alias.'.js').'/module.js')) {
                    $assetManager->registerScriptFile('js/module.js', $asset_path_alias, 10, AssetManager::OUTPUT_SCREEN);
                }
                // Register controller specific js (note for this to work, controllers in child modules must be named the same
                // as the corresponding controller in the parent module(s)
                if (file_exists(Yii::getPathOfAlias($asset_path_alias.'.js').'/'.$controller_name.'.js')) {
                    $assetManager->registerScriptFile('js/'.$controller_name.'.js', $asset_path_alias, 10, AssetManager::OUTPUT_SCREEN);
                }
            }
        }
    }

    /**
     * Redirect to the patient episodes when the controller determines the action cannot be carried out.
     */
    protected function redirectToPatientLandingPage()
    {
        $this->redirect((new CoreAPI())->generatePatientLandingPageLink($this->patient));
    }
}
