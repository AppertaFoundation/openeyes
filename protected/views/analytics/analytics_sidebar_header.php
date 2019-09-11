<div class="select-analytics flex-layout">
    <h3>Select options</h3>
    <ul class="oescape-icon-btns">
        <li class="icon-btn">
            <a id="js-all-specialty-tab" href="/analytics/allSubspecialties" class="active <?= $specialty == 'All' ? 'selected' : '' ?>">All</a>
        </li>
        <li class="icon-btn">
            <a id="js-ca-specialty-tab" href="/analytics/cataract" class="active <?= $specialty == 'Cataract' ? 'selected' : '' ?>">CA</a>
        </li>
        <li class="icon-btn">
            <a id="js-gl-specialty-tab" href="/analytics/glaucoma" class="active <?= $specialty == 'Glaucoma' ? 'selected' : '' ?>">GL</a>
        </li>
        <li class="icon-btn analytics-btn" data-specialty="Medical Retina">
            <a id="js-mr-specialty-tab" href="/analytics/medicalRetina"
               class="active <?= $specialty == 'Medical Retina' ? 'selected' : '' ?>">MR</a>
        </li>
        <li class="icon-btn" data-specialty="Medical Retina">
            <?php 
                echo CHtml::ajaxLink(
                    "MR", 
                    CController::createUrl('/analytics/medicalRetina'), 
                    array('update'=> '#data'),
                    array(
                        'id'=>'js-mr-specialty-tab', 
                        'class'=>'active', 
                        // 'cssClassExpression'=> $specialty == 'Medical Retina' ? 'selected' : '',
                    )
                );
            ?>
        </li>
    </ul>
</div>