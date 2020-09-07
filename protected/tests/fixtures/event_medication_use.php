<?php

 return array(
     'prescription_item1' => array(
         'id' => 1,
         'event_id' => 1,
         'usage_type' => 'test',
         'prescription_id' => 1,
         'medication_id' => 1,
         'dose' => '10',
         'route_id' => 1,
         'frequency_id' => 1,
         'duration_id' => 1,
         'dose_unit_term' => 'mL',
         'dispense_condition_id' => 5,
         'comments' => 'Single taper',
         'start_date' => date('Y-m-d')
     ),
     'prescription_item2' => array(
         'id' => 2,
         'event_id' => 1,
         'usage_type' => 'test',
         'prescription_id' => 1,
         'medication_id' => 2,
         'dose' => '10',
         'route_id' => 1,
         'frequency_id' => 1,
         'duration_id' => 1,
         'dose_unit_term' => 'capsule(s)',
         'dispense_condition_id' => 5,
         'comments' => 'No tapers',
         'start_date' => date('Y-m-d')
     ),
     'prescription_item3' => array(
         'id' => 3,
         'event_id' => 1,
         'usage_type' => 'test',
         'prescription_id' => 1,
         'medication_id' => 1,
         'dose' => '10',
         'route_id' => 1,
         'frequency_id' => 1,
         'duration_id' => 1,
         'dose_unit_term' => 'mL',
         'dispense_condition_id' => 4,
         'comments' => 'Not FP10 printable.',
         'start_date' => date('Y-m-d')
     ),
     'prescription_item4' => array(
         'id' => 4,
         'event_id' => 1,
         'usage_type' => 'test',
         'prescription_id' => 1,
         'medication_id' => 1,
         'dose' => '10',
         'route_id' => 1,
         'frequency_id' => 1,
         'duration_id' => 1,
         'dose_unit_term' => 'mL',
         'dispense_condition_id' => 5,
         'comments' => 'Multiple tapers',
         'start_date' => date('Y-m-d')
     ),
     'prescription_item5' => array(
         'id' => 5,
         'event_id' => 1,
         'usage_type' => 'test',
         'prescription_id' => 1,
         'medication_id' => 1,
         'dose' => '10',
         'route_id' => 1,
         'frequency_id' => 1,
         'duration_id' => 1,
         'dose_unit_term' => 'mL',
         'dispense_condition_id' => 5,
         'start_date' => date('Y-m-d')
         // No comment
     ),
     'prescription_item6' => array(
         'id' => 6,
         'event_id' => 1,
         'usage_type' => 'test',
         'prescription_id' => 1,
         'medication_id' => 1,
         'dose' => '10',
         'route_id' => 1,
         'frequency_id' => 1,
         'duration_id' => 12,
         'dose_unit_term' => 'mL',
         'dispense_condition_id' => 5,
         'start_date' => date('Y-m-d')
         // No comment
     ),
 );
