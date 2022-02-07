<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PatientTicketing\widgets;

class BaseTicketAssignment extends \CWidget
{
    public $shortName;
    public $ticket;
    public $label_width = 2;
    public $data_width = 4;
    public $form_name;
    public $assetFolder;
    public $jsPriority = null;
    public $queue = null;
    public ?string $label = null;
    public array $assignment_field = [];

    public ?int $episode_id = null;
    public ?\Episode $episode = null;
    public $is_template = false;

    public function init()
    {
        // if the widget has javascript, load it in
        $cls_name = explode('\\', get_class($this));
        $this->shortName = array_pop($cls_name);
        $path = dirname(__FILE__);
        if (file_exists($path.'/js/'.$this->shortName.'.js')) {
            $assetManager = \Yii::app()->getAssetManager();
            $this->assetFolder = $assetManager->publish($path . '/js/', true);
            $assetManager->registerScriptFile('js/'.$this->shortName.'.js', 'application.modules.PatientTicketing.widgets', $this->jsPriority);
        }

        if ($this->episode_id) {
            $this->episode = \Episode::model()->findByPk($this->episode_id);
        }

        parent::init();
    }

    /**
     * Extract the data into a storable (usable) form from the form $_POST data.
     *
     * @param $form_data
     */
    public function extractFormData($form_data)
    {
        // should be implemented in the child class
    }

    /**
     * Validate the submitted form data for this widget.
     *
     * @param $form_data
     */
    public function validate($form_data)
    {
        // should be implemented in the child class
    }

    /**
     * For widgets that need to process assignment data in the wider patient record context.
     *
     * @param $ticket
     * @param $data
     */
    public function processAssignmentData($ticket, $data)
    {
        // should be implemented in the child class as necessary
    }

    /**
     * Generates a string for display/reporting purposes.
     *
     * @param $data
     */
    public function getReportString($data)
    {
        // should be implemented in the child class as necessary
    }

    /**
     * renders the widget view.
     */
    public function run()
    {
        $this->render($this->shortName);
    }
}
