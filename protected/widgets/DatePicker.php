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

 /**
  * @deprecated Since 6.3.1
  * @link /docs/adr/0004-standardise-on-native-datepicker-functionality-with-a-consistent-widget.md
  */
class DatePicker extends BaseFieldWidget
{
    public $name;
    public $options = array();

    /**
     * Run the widget
     */
    public function run()
    {
        if (!Yii::app()->request->isPostRequest) {
            if ($this->element->{$this->field}) {
                if (preg_match('/^[0-9]+ [a-zA-Z]+ [0-9]+$/', $this->element->{$this->field})) {
                    $this->value = $this->element->{$this->field};
                } else {
                    $this->value = date('j M Y', strtotime($this->element->{$this->field}));
                }
            } else {
                if (array_key_exists('null', $this->htmlOptions) && $this->htmlOptions['null']) {
                    $this->value = null;
                } else {
                    $this->value = date('j M Y');
                }
            }
        } else {
            if ($this->name) {
                if ($this->getPostValue($this->name)) {
                    $this->value = $this->getPostValue($this->name);
                } else {
                    if (isset($this->htmlOptions['null']) && $this->htmlOptions['null']) {
                        $this->value = null;
                    } else {
                        $this->value = date('d M Y');
                    }
                }
            } else {
                $this->value = Yii::app()->request->getPost(get_class($this->element))[$this->field];
            }
        }

        parent::run();
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function getPostValue($name)
    {
        $data = $_POST;
        foreach (explode('[', $name) as $i => $key) {
            $lkup = $key;
            if ($i > 0) {
                $lkup = substr($key, 0, -1);
            }
            $data = @$data[$lkup];
        }

        return $data;
    }
}
