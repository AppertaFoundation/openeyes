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

class EyeSelector extends BaseCWidget
{
    public static $NOT_CHECKED = -9;

    /**
     * @var string the template to be used to control the layout of various sections in the view.
     * These tokens are recognized: {Left}, {Right} and {NA}.
     */
    public $template = "<td class='nowrap'><span class='oe-eye-lat-icons'>{Right}{Left}</span></td><td><span class='oe-eye-lat-icons'>{NA}</span></td>";

    /**
     * @var int the id of the selected eye.
     * null - nothing is selected
     * -9 - n/a, 1 - left, 2 - right, 3 - both
     */
    public $selectedEyeId;

    /**
     * @var string prefix of the input fields name attribute
     */
    public $inputNamePrefix;

    /**
     * Renders the main content of the view.
     * The content is divided into sections, such as summary, items, pager.
     * Each section is rendered by a method named as "renderXyz", where "Xyz" is the section name.
     * The rendering results will replace the corresponding placeholders in {@link template}.
     */
    public function render($view, $data = null, $return = false)
    {
        ob_start();
        echo preg_replace_callback(
            "/{(\w+)}/",
            array($this, 'renderSection'),
            $this->template
        );
        ob_end_flush();
    }

    public function renderRight()
    {
        echo CHtml::openTag('label', ['class' => 'inline highlight']);
        echo CHtml::checkBox($this->inputNamePrefix . "[right_eye]", in_array($this->selectedEyeId, [\Eye::RIGHT, \Eye::BOTH]), ['class' => 'js-right-eye', 'data-eye-side' => 'right']) . ' R';
        echo CHtml::closeTag('label');
    }

    public function renderLeft()
    {
        echo CHtml::openTag('label', ['class' => 'inline highlight']);
        echo CHtml::checkBox($this->inputNamePrefix . "[left_eye]", in_array($this->selectedEyeId, [\Eye::LEFT, \Eye::BOTH]), ['class' => 'js-left-eye', 'data-eye-side' => 'left']) . ' L';
        echo CHtml::closeTag('label');
    }

    public function renderNA()
    {
        echo CHtml::openTag('label', ['class' => 'inline highlight']);
        echo CHtml::checkBox($this->inputNamePrefix . "[na_eye]", $this->selectedEyeId == -9, ['class' => 'js-na-eye', 'name' => null, 'value' => self::$NOT_CHECKED]) . ' n/a';
        echo CHtml::closeTag('label');
    }

    /**
     * Renders a section.
     * This method is invoked by {@link render} for every placeholder found in {@link template}.
     * It should return the rendering result that would replace the placeholder.
     * @param array $matches the matches, where $matches[0] represents the whole placeholder,
     * while $matches[1] contains the name of the matched placeholder.
     * @return string the rendering result of the section
     */
    protected function renderSection($matches)
    {
        $method = 'render' . $matches[1];
        if (method_exists($this, $method)) {
            $this->$method();
            $html = ob_get_contents();
            ob_clean();

            return $html;
        } else {
            return $matches[0];
        }
    }
}
