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
        $this->items = Yii::app()->db->createCommand()
            ->select('patient.hos_num, contact.last_name, contact.first_name, patient.dob, address.postcode, d.created_date, drug.name, user.first_name as user_first_name, user.last_name as user_last_name, user.role, event.created_date as event_date' )
            ->from('episode')
            ->join('event', 'episode.id = event.episode_id')
            ->join('et_ophdrprescription_details d', 'event.id = d.event_id')
            ->join('ophdrprescription_item i', 'd.id = i.prescription_id')
            ->join('drug', 'i.drug_id = drug.id')
            ->join('patient', 'episode.patient_id = patient.id')
            ->join('contact', 'patient.contact_id = contact.id')
            ->join('address', 'contact.id = address.contact_id')
            ->join('user', 'd.created_user_id = user.id')
            ->where(array('in', 'drug.id', $this->drugs))->queryAll();
    }
    
    public function toCSV()
    {
        $output = "Patient's no,  Patient's Surname, Patient's First name,  Patient's DOB, Patient's Post code, Date of Prescription, Drug name, Prescribed Clinician's name, Prescribed Clinician's Job-role, Prescription event date\n";
        foreach($this->items as $item){
            $output .= $item['hos_num'] . ", " . $item['last_name'] . ", " . $item['first_name'] . ", " . ($item['dob'] ? date('j M Y',strtotime($item['dob'])) : 'Unknown') . ", " . $item['postcode'] . ", ";
            $output .= (date('j M Y',strtotime($item['created_date'])) . " " . (substr($item['created_date'],11,5)) ) . ", " . $item['name'] . ", ";
            $output .= $item['user_first_name'] . " " . $item['user_last_name'] . ", " . $item['role'] . ", " . ( date('j M Y',strtotime($item['event_date'])) . " " . (substr($item['event_date'],11,5)) ) ;
            $output .= "\n";
        }
        
        return $output;
    }
}
