<?php
/* @var $this OphCiExaminationRiskController */
/* @var $model OphCiExaminationRisk */
?>

<div class="box admin">
    <h2>Required Risk Set</h2>

<?php

    $columns = array(
        'checkboxes' => array(
            'header' => '',
            'type' => 'raw',
            'value' => function($data, $row){
                return CHtml::checkBox("OEModule_OphCiExamination_models_OphCiExaminationRisk[][id]",false, ['value' => $data->id]);
            },
            'cssClassExpression' => '"checkbox"',
        ),
        'name',
        array(
            'header' => 'Subspecialty',
            'name' => 'subspecialty_id',
            'type' => 'raw',
            'value' => function($data, $row){
                return $data->subspecialty ? $data->subspecialty->name : null;
            },
        ),
        array(
            'header' => 'Firm',
            'name' => 'firm_id',
            'type' => 'raw',
            'value' => function($data, $row){
                return $data->firm ? $data->firm->name : null;
            }
        ),
        array(
            'header' => 'Sex Specific',
            'name' => 'gender',
            'type' => 'raw',
            'value' => function($data, $row){

                if($data->gender){
                    return \Patient::model()->getGenderString($data->gender);
                }
                return null;
            }
        ),
        array(
            'header' => 'Age Specific (Min - Max)',
            'name' => 'age_min',
            'type' => 'raw',
            'value' => function($data, $row){
                if( !$data->age_min && !$data->age_max) {
                    return null;
                };
                return $data->age_min . ' - ' . $data->age_max;
            }
        ),

    );

    $dataProvider = $model->search();
    $dataProvider->pagination = false;


    $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $dataProvider,
        'id' => 'generic-admin-form',
        'itemsCssClass' => 'generic-admin grid',
        //'template' => '{items}',
        "emptyTagName" => 'span',
        'summaryText' => false,
        'rowHtmlOptionsExpression'=>'array("data-row"=>$row)',
        'enableSorting' => false,
        'enablePagination' => false,
        'columns' => $columns,
        'rowHtmlOptionsExpression' => 'array("data-id" => $data->id)',
        'rowCssClass' => array('clickable'),

    ));
?>

    <button class="small primary event-action" name="add" type="submit" id="et_add">Add</button>
    <button data-object="OphCiExaminationRisk" data-uri="/OphCiExamination/oeadmin/RisksAssignment/delete" class="small primary event-action" name="delete" type="submit" id="et_delete">Delete</button>

</div>

<script>
    $(document).ready(function(){
        $('table.generic-admin tbody').on('click', 'tr td:not(".checkbox")', function(){
            var id = $(this).closest('tr').data('id');
            window.location.href = '/OphCiExamination/oeadmin/RisksAssignment/update/' + id;
        });

        $('#et_add').click(function(){
            window.location.href = '/OphCiExamination/oeadmin/RisksAssignment/create/';
        });

       /* $('#et_delete').click(function(e){
            e.preventDefault();

            var confirmDialog = new OpenEyes.UI.Dialog.Confirm({
                title: "Remove Patient Signature",
                'content': 'Are you sure you want to delete the selected sets ?',
                'okButton': 'Remove'
            });
            confirmDialog.open();

            confirmDialog.content.off('click', '.ok');
            // manage form submission and response
            confirmDialog.content.on('click', '.ok', function() {
                $.ajax({
                    'type': 'POST',
                    'url': '/OphCiExamination/oeadmin/RisksAssignment/delete',
                    'data': $('#profile_sites').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
                    'success': function(html) {
                        if (html === "success") {
                            window.location.reload();
                        } else {
                            new OpenEyes.UI.Dialog.Alert({
                                content: "There was an unexpected error deleting the sites, please try again or contact support for assistance."
                            }).open();
                        }
                    },
                    'error': function() {
                        new OpenEyes.UI.Dialog.Alert({
                            content: "Sorry, There was an unexpected error deleting the sites, please try again or contact support for assistance."
                        }).open();
                    }
                });
            });





        });*/

    });
</script>