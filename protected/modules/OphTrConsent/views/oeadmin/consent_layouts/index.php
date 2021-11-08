<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<form id="leaflets_firm" method="GET">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
    <div class="cols-6">
        <table class="standard cols-full" id="consent-assessment-table">
            <colgroup>
                <col class="cols-1">
                <col class="cols-3">
            </colgroup>
            <tbody>
            <tr>
                <td class="fade">Type:</td>
                <td>
                    <?= CHtml::dropDownList(
                        'type_id',
                        '',
                        CHtml::listData(
                            OphTrConsent_Type_Type::model()->findAll(),
                            'id',
                            'name'
                        ),
                                                      [
                                                      'empty' => '-- Select --',
                                                      'class' => 'cols-full'
                        ]
                    ) ?>
                </td>
            </tr>
            <tr>
                <td class="fade">Add Elements:</td>
                <td>
                    <?= CHtml::dropDownList(
                        'element_id',
                        '',
                        CHtml::listData(
                            ElementType::model()->findAll(
                                array(
                                    'condition' => 'event_type_id = :event_type_id',
                                    'order' => 'name asc',
                                    'params'    => array(
                                        ':event_type_id' => EventType::model()->findByAttributes(
                                            array(
                                                'class_name' => 'OphTrConsent'
                                            )

                                        )->id
                                    )
                                )
                            ),
                            'id',
                            'name'
                        ),
                        [
                            'empty' => '-- Select --',
                            'class' => 'cols-full'
                        ]
                    ) ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</form>
<div class="cols-7">
    <table class="standard cols-full" id="type-assessments-table">
        <colgroup>
            <col class="cols-1">
            <col class="cols-5">
            <col class="cols-1">
        </colgroup>
        <thead>
        <tr>
            <th>Order</th>
            <th>Name</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody id="type-assessments" class="sortable"></tbody>
    </table>
</div>
<script type = "text/javascript" >
    $(document).ready(function () {
        $('.sortable').sortable({
            update: function (event, ui) {
                let ids = [];
                $('.sortable').children('tr').map(function () {
                    ids.push($(this).attr('id'));
                });

                $.ajax({
                    'type': 'POST',
                    'url': baseUrl + '/oeadmin/ConsentLayouts/sortAssessments',
                    'data': {
                        YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                        order: ids
                    },
                    'success': function (data) {
                        new OpenEyes.UI.Dialog.Alert({
                            content: 'Re-ordered'
                        }).open();
                    }
                });
            },
            stop: function (e, ui) {
                $('#type-assessments tbody tr').each(function (index, tr) {
                    $(tr).find('.js-display-order').val(index);
                });
            }
        });
    });

    class ConsentLayouts{
        constructor() {
            this.dropdown_type = document.getElementById('type_id');
            this.dropdown_elements = document.getElementById('element_id');

            this.type_id = this.dropdown_type.value;
            this.element_id = this.dropdown_elements.value;

            this.dropdown_type.addEventListener('change', evt => this.changeType(evt));
            this.dropdown_elements.addEventListener('change', evt => this.addElement(evt));
        }

        changeType(){
            this.type_id = this.dropdown_type.value;

            if(this.type_id === ""){
                this.disableDropdownElements();
            } else {
                this.enableDropdownElements();
                this.getElementsByTypeId();
            }
        }

        getElementsByTypeId(){
            fetch('getLayoutElements?type_id=' + this.type_id,
                {
                    method: 'GET'
                })
                .then(response => response.json()) // parse response as JSON (can be res.text() for plain response)
                .then(data  => {
                    document.getElementById('type-assessments').innerHTML = data.rows;

                })
                .catch(err => {
                    console.log(err)
                });
        }

        disableDropdownElements() {
            this.dropdown_elements.disabled = true;
            this.dropdown_elements.value = "";
        }

        enableDropdownElements() {
            this.dropdown_elements.disabled = false;
        }

        addElement(){
            this.element_id = this.dropdown_elements.value;
            if(this.element_id !== ""){
                this.saveElementToLayout();
            }
        }

        saveElementToLayout(){
            const data = {
                YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                type_id: this.type_id,
                element_id: this.element_id
            };

            fetch('addLayoutElements',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                    },
                    credentials: 'include',
                    body: new URLSearchParams(data).toString()
                })
                .then(response => response.json()) // parse response as JSON (can be res.text() for plain response)
                .then(data => {
                    if(data.success === 1){
                        this.getElementsByTypeId();
                    } else {
                        new OpenEyes.UI.Dialog.Alert({
                            content: data.message
                        }).open();
                    }
                })
                .catch(err => {
                    console.log(err)
                });
        }

        deleteAssessment( row_id ){
            const data = {
                YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                type_id: this.type_id,
                row_id: row_id
            };

            fetch('deleteLayoutElements',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                    },
                    credentials: 'include',
                    body: new URLSearchParams(data).toString()
                })
                .then(response => response.json()) // parse response as JSON (can be res.text() for plain response)
                .then(data => {
                    if(data.success === 1){
                        this.getElementsByTypeId();
                    }
                })
                .catch(err => {
                    console.log(err)
                });
        }
    }

    const layouts = new ConsentLayouts();
    layouts.disableDropdownElements();
</script>
