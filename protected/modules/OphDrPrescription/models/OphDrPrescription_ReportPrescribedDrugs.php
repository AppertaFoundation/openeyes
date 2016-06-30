<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OphDrPrescription_ReportPrescribedDrugs
 *
 * @author szabo_000
 */
class OphDrPrescription_ReportPrescribedDrugs extends BaseReport
{
    public $drugs;
    public $start_date;
    public $end_date;
    
    private $items;
    
    public function attributeLabels()
    {
        return array(
            'drugs' => 'Prescribed drugs',
            'start_date' => 'Date from',
            'start_end' => 'Date end',
        );
    }
    
    public function rules()
    {
        return array(
            array('start_date, end_date, drugs', 'safe'),
            array('drugs', 'required'),
        );
    }
    
    public function run()
    {
        $this->items = Yii::app()->db->createCommand('select * from patient limit 10')->queryAll();
    }
    
    public function toCSV()
    {
        $output = "hos_num\n";
        foreach($this->items as $item){
            $output .= $item['hos_num'] . "\n";
        }
        
        return $output;
    }
}
