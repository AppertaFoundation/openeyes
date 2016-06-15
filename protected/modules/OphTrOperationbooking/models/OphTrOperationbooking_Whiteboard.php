<?php /**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */


class OphTrOperationbooking_Whiteboard extends BaseActiveRecordVersioned
{

	/**
	 * Returns the static model of the specified AR class.
	 * @return the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ophtroperationbooking_whiteboard';
	}

	
	public function update($id)
	{
		// TODO: The data fetch should be performed here, which
		// should write collated whiteboard data to the Whiteboard model.
		// The fetch() method should just pull from the Whiteboard model.
	}


	public function fetch($id)
	{	
		// TODO: Group these pulls in to one join query? Or leave separate for clearer reading
		$ob = Element_OphTrOperationbooking_Operation::model()->find('event_id=?',array($id));
		$ev = Event::model()->find('id=?',array($id));
		$ep = Episode::model()->find('id=?',array($ev->episode_id));
		$patient = Patient::model()->find('id=?',array($ep->patient_id));
		$contact = Contact::model()->find('id=?',array($patient->contact_id));
		
		$allergies = Yii::app()->db->createCommand()
			->select('a.name as name')
			->from('patient_allergy_assignment pas')
			->leftJoin('allergy a', 'pas.allergy_id = a.id')
			->where("pas.patient_id = {$ep->patient_id}")
			->order('a.name')
			->queryAll();
		
		$allergyString = '';
		foreach($allergies as $a) $allergyString .= $a['name'].', ';
		if($allergyString) $allergyString = substr($allergyString,0,-2);
		if(!$allergyString) $allergyString = 'None';

		$eyes = array(1=>'Left', 2=>'Right', 3=>'Both');	// TODO: pull from DB/Join?
		$d['eye_id'] = $ob->eye_id;
		$d['eyeSide'] = $eyes[$d['eye_id']];

		$d['predictedAdditionalEquipment'] = $ob->special_equipment_details;		
		$d['comments'] = $ob->comments . "\n" . $ob->comments_rtt;

		$d['patientName'] = $contact['title'] . ' ' . $contact['first_name'] . ' ' . $contact['last_name'];
		$d['dob'] = date('j M Y',strtotime($patient['dob']));
		$d['hos_num'] = $patient['hos_num'];
		$d['procedure'] = 'Phaco + IOL';	// TODO
		$d['allergies'] = $allergyString;
		$d['iol_model'] = 'unknown';	// TODO
		$d['iol_power'] = 'none';		// TODO
		$d['predictedRefractiveOutcome'] = '-0.0 D';	// TODO
		$d['alphaBlockers'] = 'N/A';	// TODO
		$d['anticoagulants'] = 'Anti-N/A';	// TODO
		$d['inr'] = 'None';	// TODO
		return $d;
	}
}
