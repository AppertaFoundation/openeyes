<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class DynamicFiltersWidget extends BaseCWidget
{
    public array $adder_itemset;
    public ?string $prefix = 'dynamic_filters';
    public string $title;
    public array $preselected;

    public function init()
    {
        parent::init();
        $this->assetManager->registerScriptFile('js/OpenEyes.UI.CommonEventListeners.js', null, 1);

        $widget_path = \Yii::app()->assetManager->publish('protected/widgets/js');
        \Yii::app()->clientScript->registerScriptFile($widget_path . '/PatientPanelPopupMulti.js');
    }

    public function run()
    {
        $this->render('dynamicfilters/dynamicFilters');
    }

    public function getSearchParameters(): array
    {
        $filters = [];
        foreach ($this->preselected as $key => $item) {
            $filters[] = [
                'key' => $key,
                'main_label' => $item['type'],
                'parameter_name' => $this->prefix ?: 'dynamic_filters',
                'value_label' => $item['value'],
                'operation_label' => $item['operation'] ?? null,
            ];
        }

        return $filters;
    }
}
