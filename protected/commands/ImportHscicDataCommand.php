<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class ImportHscicDataCommand extends CConsoleCommand
{
	static private $files = array(
		'Gp' => array(
			'url' => 'http://systems.hscic.gov.uk/data/ods/datadownloads/data-files/egpcur.zip',
			'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode', '', '', 'status', '', '', '', '', 'phone'),
		 ),
		'Practice' => array(  // http://systems.hscic.gov.uk/data/ods/supportinginfo/filedescriptions#_Toc350757591
			'url' => 'http://systems.hscic.gov.uk/data/ods/datadownloads/data-files/epraccur.zip',
			'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode', '', '', 'status', '', '', '', '', 'phone'),
		),
		'Ccg' => array(
			'url' => 'http://systems.hscic.gov.uk/data/ods/datadownloads/data-files/eccg.zip',
			'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode'),
		),
		'CcgAssignment' => array(
			'url' => 'http://systems.hscic.gov.uk/data/ods/datadownloads/data-files/epcmem.zip',
			'fields' => array('practice_code', 'ccg_code'),
		),
	);

	private $pcu;
	private $country_id;
	private $cbt_id;

	public function run($args)
	{
		$this->pcu = new PostCodeUtility;
		$this->country_id = Country::model()->findByAttributes(array('code' => 'GB'))->id;
		$this->cbt_id = CommissioningBodyType::model()->findByAttributes(array('shortname' => 'CCG'))->id;

		foreach (self::$files as $type => $params) {
			$f = $this->downloadAndOpen($params['url']);

			while (($row = fgetcsv($f))) {
				print "{$type} {$row[0]}\n";
				$data = array_combine(array_pad($params['fields'], count($row), ""), $row);
				$tx = Yii::app()->db->beginTransaction();
				try {
					$this->{"import{$type}"}($data);
					$tx->commit();
				} catch(Exception $e) {
					$message = "Error processing {$type} row:\n" . CVarDumper::dumpAsString($row) . "\n$e";
					Yii::log($message, CLogger::LEVEL_ERROR);
					print "$message\n";
					$tx->rollback();
				}
			}

			fclose($f);
		}
	}

	/**
	 * @param string $url
	 * @return resource File handle
	 */
	private function downloadAndOpen($url)
	{
		$zip_path = Yii::app()->runtimePath . '/hscic_data.zip';

		$f = fopen($zip_path, 'x');
		$c = curl_init($url);
		curl_setopt($c, CURLOPT_FILE, $f);
		curl_exec($c);
		curl_close($c);
		fclose($f);

		$z = new ZipArchive;
		if (($res = $z->open($zip_path)) !== true) {
			throw new Exception("Failed to open zip file at '{$zip_path}': " . $res);
		}

		$filename = str_replace('.zip', '.csv', basename($url));
		if (!($stream = $z->getStream($filename))) {
			throw new Exception("Failed to extract '{$filename}' from zip file at '{$zip_path}'");
		}

		unlink($zip_path);

		return $stream;
	}

	private function importGp(array $data)
	{
		if (!($gp = Gp::model()->findbyAttributes(array('nat_id' => $data['code'])))) {
			if ($data['status'] != 'A') return;
			$gp = new Gp;
			$gp->nat_id = $data['code'];
			$gp->obj_prof = $data['code'];
		}

		if (!$gp->save()) throw new Exception("Failed to save GP: " . print_r($gp->errors, true));

		$contact = $gp->contact;
		$contact->primary_phone = $data['phone'];

		if (preg_match("/^([\S]+)\s+([A-Z]{1,4})$/i", trim($data['name']), $m)) {
			$contact->title = 'Dr';
			$contact->first_name = $m[2];
			$contact->last_name = $this->tidy($m[1]);
		} else {
			$contact->last_name = $data['name'];
		}

		if (!$contact->save()) throw new Exception("Failed to save contact: " . print_r($contact->errors, true));

		if (!($address = $contact->address)) {
			$address = new Address;
			$address->contact_id = $contact->id;
		}
		$this->importAddress($address, array($data['addr1'], $data['addr2'], $data['addr3'], $data['addr4'], $data['addr5']));
		$address->postcode = $data['postcode'];
		$address->country_id = $this->country_id;

		if (!$address->save()) throw new Exception("Failed to save address: " . print_r($address->errors, true));
	}

	private function importPractice(array $data)
	{
		if (!($practice = Practice::model()->findByAttributes(array('code' => $data['code'])))) {
			if ($data['status'] != 'A') return;
			$practice = new Practice;
			$practice->code = $data['code'];
		}
		$practice->phone = $data['phone'];
		if (!$practice->save()) throw new Exception("Failed to save practice: " . print_r($practice->errors, true));

		$contact = $practice->contact;
		$contact->primary_phone = $practice->phone;
		if (!$contact->save()) throw new Exception("Failed to save contact: " . print_r($contact->errors, true));

		if (!($address = $contact->address)) {
			$address = new Address;
			$address->contact_id = $contact->id;
		}
		$this->importAddress($address, array($data['name'], $data['addr1'], $data['addr2'], $data['addr3'], $data['addr4'], $data['addr5']));
		$address->postcode = $data['postcode'];
		$address->country_id = $this->country_id;

		if (!$address->save()) throw new Exception("Failed to save address: " . print_r($address->errors, true));
	}

	private function importCcg(array $data)
	{
		if (!($ccg = CommissioningBody::model()->findByAttributes(array('code' => $data['code'], 'commissioning_body_type_id' => $this->cbt_id)))) {
			$ccg = new CommissioningBody;
			$ccg->code = $data['code'];
			$ccg->commissioning_body_type_id = $this->cbt_id;
		}
		$ccg->name = $data['name'];
		if (!$ccg->save()) throw new Exception("Failed to save CCG: " . print_r($ccg->errors, true));

		$contact = $ccg->contact;

		if (!($address = $contact->address)) {
			$address = new Address;
			$address->contact_id = $contact->id;
		}
		$this->importAddress($address, array($data['addr1'], $data['addr2'], $data['addr3'], $data['addr4'], $data['addr5']));
		$address->postcode = $data['postcode'];
		$address->country_id = $this->country_id;

		if (!$address->save()) throw new Exception("Failed to save address: " . print_r($address->errors, true));
	}

	private function importCcgAssignment(array $data)
	{
		$practice = Practice::model()->findByAttributes(array('code' => $data['practice_code']));
		$ccg = CommissioningBody::model()->findByAttributes(array('code' => $data['ccg_code'], 'commissioning_body_type_id' => $this->cbt_id));

		if (!$practice || !$ccg) return;

		$found = false;
		foreach ($practice->commissioningbodyassigments as $assignment) {
			if ($assignment->commissioning_body_id == $ccg->id) {
				$found = true;
			} else {
				if ($assignment->commissioning_body->commissioning_body_type_id == $this->cbt_id)  $assignment->delete();
			}
		}

		if (!$found) {
			$assignment = new CommissioningBodyPracticeAssignment;
			$assignment->commissioning_body_id = $ccg->id;
			$assignment->practice_id = $practice->id;
			if (!$assignment->save()) throw new Exception("Failed to save commissioning body assignment: " . print_r($assignment->errors, true));
		}
	}

	private function importAddress(Address $address, array $lines)
	{
		$lines = array_unique(array_filter(array_map(array($this, 'tidy'), $lines)));
		if ($lines) $address->address1 = array_shift($lines);
		if ($lines) $address->county = array_pop($lines);
		if ($lines) $address->city = array_pop($lines);
		if ($lines) $address->address2 = implode("\n", $lines);
	}

	private function tidy($string)
	{
		$string = ucwords(strtolower(trim($string)));

		foreach (array('-', '\'', '.') as $delimiter) {
			if (strpos($string, $delimiter) !== false) {
				$string = implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
			}
		}

		$string = str_replace('\'S ', '\'s ', $string);

		return $string;
	}
}
