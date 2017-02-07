<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo json_encode(CHtml::listData(OphInDnaextraction_DnaExtraction_Storage::getAvailableCombinedList( $element->storage_id ), 'id', 'value' ));
?>

