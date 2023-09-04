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
class MultiSelectListFreeText extends BaseFieldWidget
{
    public $default_options = array();
    public $filtered_options = array();
    public $relation;
    public $selected_ids = array();
    public $descriptions = array();
    public $relation_id_field;
    public $options;
    public $inline = false;
    public $showRemoveAllLink = false;
    public $sorted = false;
    public $noSelectionsMessage;
    public $widgetOptionsJson;
    public $model;

    public function init()
    {
        $this->filtered_options = $this->options;

        if (!$this->model) {
            $relations = $this->element->relations();
            if (!empty($relations[$this->relation])) {
                $relation = $relations[$this->relation];
                $model = $relation[1];
            }
        } else {
            $model = $this->model;
        }

        if (!empty($model)) {
            if ($model::model()->hasAttribute('display_order')) {
                foreach ($this->options as $value => $option) {
                    if ($item = $model::model()->findByPk($value)) {
                        $this->htmlOptions['options'][$value]['data-display_order'] = $item->display_order;
                    }
                }
            }

            if (@$this->htmlOptions['requires_description_field']) {
                $requires_description_field = $this->htmlOptions['requires_description_field'];

                foreach ($this->options as $value => $option) {
                    if ($item = $model::model()->findByPk($value)) {
                        if ($item->$requires_description_field) {
                            $this->htmlOptions['options'][$value]['data-requires-description'] = true;
                        }
                    }
                }
            }
        }

        if (empty($_POST) || !$this->element->isNewRecord) {
            if ($this->element && $this->element->{$this->relation}) {
                foreach ($this->element->{$this->relation} as $item) {
                    $this->selected_ids[] = $item->{$this->relation_id_field};

                    if (!empty($model)) {
                        $_item = $model::model()->findByPk($item->{$this->relation_id_field});

                        if (@$requires_description_field && $_item->$requires_description_field) {
                            $this->descriptions[$item->{$this->relation_id_field}] = $item->description;
                        }
                    }

                    unset($this->filtered_options[$item->{$this->relation_id_field}]);
                }
            } elseif (!$this->element || !$this->element->id) {
                if (is_array($this->default_options)) {
                    $this->selected_ids = $this->default_options;
                    foreach ($this->default_options as $id) {
                        unset($this->filtered_options[$id]);
                    }
                }
            }
        } else {
            if (!empty($_POST[$this->field])) {
                foreach ($_POST[$this->field] as $id) {
                    $this->selected_ids[] = $id['id'];
                    unset($this->filtered_options[$id['id']]);
                }
            }
            // when the field being used contains the appropriate square brackets for defining the associative array, the original (above)
            // approach for retrieving the posted value does not work. The following (more standard) approach does
            elseif (isset($_POST[CHtml::modelName($this->element)][$this->relation])) {
                if (is_array($_POST[CHtml::modelName($this->element)][$this->relation])) {
                    foreach ($_POST[CHtml::modelName($this->element)][$this->relation] as $id) {
                        if (is_array($id)) {
                            $this->selected_ids[] = $id['id'];
                            unset($this->filtered_options[$id['id']]);

                            if (!empty($model)) {
                                $item = $model::model()->findByPk($id['id']);

                                if (@$requires_description_field && $item->$requires_description_field) {
                                    $this->descriptions[$id['id']] = @$id['description'];
                                }
                            }
                        }
                    }
                } else {
                    $this->selected_ids = array();
                }
            } elseif (!isset($_POST[$this->field]) && !isset($_POST[CHtml::modelName($this->element)][$this->relation])) {
                $this->selected_ids = array();
            }
        }

        // if the widget has javascript, load it in
        if (file_exists('protected/widgets/js/' . get_class($this) . '.js')) {
            $this->assetFolder = Yii::app()->getAssetManager()->getPublishedPathOfAlias('application.widgets.js');
        }

        // if the widget has javascript, load it in
        if (file_exists('protected/widgets/js/' . get_class($this) . '.js')) {
            $assetManager = Yii::app()->getAssetManager();
            $asset_folder = $assetManager->getPublishedPathOfAlias('application.widgets.js');

            // Workaround for ensuring js included with ajax requests that are using renderPartial
            if (Yii::app()->request->isAjaxRequest) {
                Yii::app()->clientScript->registerScriptFile($asset_folder . '/' . get_class($this) . '.js', CClientScript::POS_BEGIN);
            } else {
                $assetManager->registerScriptFile('js/' . get_class($this) . '.js', 'application.widgets');
            }
        }

        //NOTE: don't call parent init as the field behaviour doesn't work for the relations attribute with models
    }
}
