<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class OpnoteDataCommand extends CConsoleCommand {
	
	public function getName() {
		return '';
	}
	
	public function getHelp() {
		return "";
	}

	public function run($args) {
		Yii::import('application.modules.OphTrOperationnote.models.*');

		$drugs = array(
			'Cataract' => array(
				'Intracameral Cefuroxime 1mg',
				'S/C Cefuroxime',
				'S/C Gentamicin 10/20mg',
				'S/C Dexamethasone',
				'S/C Betnosol',
			),
		);

		foreach ($drugs as $subspecialty_name => $s_drugs) {
			$subspecialty = Subspecialty::model()->find('name=?',array($subspecialty_name));

			foreach ($s_drugs as $drug) {
				if (!$d = Drug::model()->find('name=?',array($drug))) {
					echo "Adding drug: $drug\n";

					$d = new Drug;
					$d->name = $drug;
					$d->save();
				}

				foreach (Site::model()->findAll() as $site) {
					if (!$ssd = SiteSubspecialtyDrug::model()->find('site_id = ? and subspecialty_id = ? and drug_id = ?',array($site->id,$subspecialty->id,$d->id))) {
						echo "Creating association: [$site->id][$subspecialty->id][$d->id]\n";
						$ssd = new SiteSubspecialtyDrug;
						$ssd->site_id = $site->id;
						$ssd->subspecialty_id = $subspecialty->id;
						$ssd->drug_id = $d->id;
						$ssd->save();
					}
				}
			}
		}
	}
}
?>
