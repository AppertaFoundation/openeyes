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
    public $items;
    
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
        
        /*Yii::app()->db->createCommand('SELECT id, name FROM drug WHERE name LIKE "%Methotrexate%" OR NAME LIKE "%Mycophenolate%" OR NAME LIKE "%Tacrolimus%"
                                                        OR NAME LIKE "%Azathioprine%"
                                                        OR NAME LIKE "%Cyclosporine%"
                                                        OR NAME LIKE "%Cyclophosphamide%"
                                                        ORDER BY NAME')->queryAll();*/
        $this->items = Yii::app()->db->createCommand()
            ->select('patient.hos_num, contact.last_name, contact.first_name, patient.dob, address.postcode, d.created_date, drug.name, user.first_name as user_first_name, user.last_name as user_last_name, user.role, event.created_date as event_date' )
            ->from('episode')
            ->join('event', 'episode.id = event.episode_id')
            ->join('et_ophdrprescription_details d', 'event.id = d.event_id')
            ->join('ophdrprescription_item i', 'd.id = i.prescription_id')
            ->join('drug', 'i.prescription_id = drug.id')
            ->join('patient', 'episode.patient_id = patient.id')
            ->join('contact', 'patient.contact_id = contact.id')
            ->join('address', 'contact.id = address.contact_id')
            ->join('user', 'd.created_user_id = user.id')
            ->where(array('in', 'drug.id', $this->drugs))->queryAll();
    }
    
    public function toCSV()
    {
        $output = "hos_num, last_name, first_name, dob, postcode, created_date, user name, role, event_date\n";
        foreach($this->items as $item){
            $output .= $item['hos_num'] . ", " . $item['last_name'] . ", " . $item['first_name'] . ", " . $item['dob'] . ", " . $item['postcode'] . ", ";
            $output .= $item['created_date'] . ", " . $item['user_first_name'] . " " . $item['user_last_name'] . ", " . $item['role'] . ", " . $item['event_date'] ;
        }
        
        return $output;
    }
}
