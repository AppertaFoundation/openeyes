<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$storage = new OphInDnaextraction_DnaExtraction_Storage();
echo json_encode(CHtml::listData($storage->getAvailableCombinedList($element->storage_id), 'id', 'value'));
