<?php
/* @var $this PatientController */
/* @var $model Patient */
?>

<style type="text/css">
    
    .box.admin {
        background: #fafafa none repeat scroll 0 0;
        border-color: #f3f3f3;
        border-radius: 5px;
        border-style: solid;
        border-width: 1px;
        color: #333333;
        margin: 0 0 0.75rem;
        padding: 10px;
    }
    
    .nhs-number-wrapper div{
        font-size: 13px;
        display: inline-block;
        color:#000000;
    }
    
</style>
<h1 class="badge">Patient</h1>
<div class="box content admin-content">
<div class="large-6 column content admin large-centered">
    
    <div class="box admin">
        <h1 class="text-center">Create Patient</h1>

        <?php $this->renderPartial('_form', array(
            'patient' => $patient,
            'contact' => $contact,
            'nhs_num_statuses' => $nhs_num_statuses,
            'address' => $address,
            'countries' => $countries,
            'address_type_ids' => $address_type_ids,
            'general_practitioners' => $general_practitioners,
            'practices' => $practices,
            'genders' => $genders,
            'ethnic_groups' => $ethnic_groups,
        )); ?>
    </div>
</div>
    </div>