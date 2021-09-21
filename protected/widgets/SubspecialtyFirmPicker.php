<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class SubspecialtyFirmPicker extends \BaseFieldWidget
{
    public $institutions;
    public $subspecialties;
    public $firms = [];
    public $model;

    public $template = "<tr><td>{InstitutionLabel}</td><td>{InstitutionDropDown}</td></tr>
                        <tr><td>{SubspecialtyLabel}</td><td>{SubspecialtyDropDown}</td></tr>
                        <tr><td>{ContextLabel}</td><td>{ContextDropDown}</td></tr>";

    public $institution_id;
    public $firm_id;
    public $subspecialty_id;

    public $layoutColumns = array(
        'label' => 2,
        'field' => 5,
    );

    public function init()
    {
        parent::init();
        $this->institutions = Institution::model()->getList(false);
        $this->subspecialties = \Subspecialty::model()->findAll();
        if ($this->model->subspecialty_id) {
            $this->firms = \Firm::model()->getList(Yii::app()->session['selected_institution_id'], $this->model->subspecialty_id);
        }
    }

    /**
     * @param $view
     * @param $data
     * @param $return
     *
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

    public function renderInstitutionLabel()
    {
        echo 'Institution';
    }

    public function renderInstitutionDropDown()
    {
        echo CHtml::activeDropDownList(
            $this->model,
            'institution_id',
            Institution::model()->getList(true),
            ['class' => 'cols-full']
        );
    }

    public function renderSubspecialtyLabel()
    {
        echo 'Subspecialty';
    }

    public function renderSubspecialtyDropDown()
    {
        echo CHtml::activeDropDownList(
            $this->model,
            'subspecialty_id',
            Subspecialty::model()->getList(),
            ['empty' => 'Select', 'class' => 'js-subspecialty-dropdown cols-full']
        );
    }


    public function renderContextLabel()
    {
        echo Firm::contextLabel();
    }

    public function renderContextDropDown()
    {
        $firms = $this->model->subspecialty_id ? Firm::model()->getList(Yii::app()->session['selected_institution_id'], $this->model->subspecialty_id) : [];
        echo CHtml::activeDropDownList(
            $this->model,
            'firm_id',
            $firms,
            [
                'class' => 'js-firm-dropdown cols-full',
                'empty' => 'All ' . Firm::contextLabel() . 's',
                'disabled' => !$firms ? 'disabled' : false,
            ]
        );
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
