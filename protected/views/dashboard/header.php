<?php
if(Yii::app()->controller->action->id == 'cataract'){
    $this->renderPartial('//dashboard/header_cataract');
}else if(Yii::app()->controller->action->id == 'oescape'){
    $this->renderPartial('//dashboard/header_oescape');
}