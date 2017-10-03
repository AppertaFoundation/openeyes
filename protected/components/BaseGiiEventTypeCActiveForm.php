<?php
/**
 * OpenEyes.
 *
 * 
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class BaseGiiEventTypeCActiveForm extends CActiveForm
{
    /**
     * @var CCodeModel the code model associated with the form
     */
    public $model;

    /**
     * Initializes the widget.
     * This renders the form open tag.
     */
    public function init()
    {
        echo <<<EOD
<div class="form gii2">
	<p class="note">
		Fields with <span class="required">*</span> are required.
		Click on the <span class="sticky">highlighted fields</span> to edit them.
	</p>
EOD;
        parent::init();
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        $templates = array();
        foreach ($this->model->getTemplates() as $i => $template) {
            $templates[$i] = basename($template).' ('.$template.')';
        }

        $this->renderFile(Yii::getPathOfAlias('gii.views.common.generator').'.php', array(
            'model' => $this->model,
            'templates' => $templates,
        ));

        parent::run();

        echo '</div>';
    }
}
