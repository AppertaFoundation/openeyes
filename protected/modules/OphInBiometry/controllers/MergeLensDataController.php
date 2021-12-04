<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class MergeLensDataController extends BaseAdminController
{
    public function actionIndex()
    {
        // we need to run only if operation note module is installed
        if (isset(Yii::app()->modules["OphTrOperationnote"])) {
            // get all iol_type_ids from existing cataract elements
            $existing_cataract = $this->dbConnection->createCommand('SELECT distinct iol_type_id FROM et_ophtroperationnote_cataract WHERE iol_type_id IS NOT NULL')->queryAll();
            // we need to drop the old foreign key
            $this->dbConnection->createCommand("ALTER TABLE et_ophtroperationnote_cataract DROP FOREIGN KEY et_ophtroperationnote_cataract_iol_type_id_fk")->query();

            foreach ($existing_cataract as $existing) {
                $IOL_data = OphTrOperationnote_IOLType::model()->findByPk($existing['iol_type_id']);
                if ($IOL_data) {
                    $_id = $IOL_data->id + 10000;
                    $new_IOL = OphInBiometry_LensType_Lens::model()->findByPk($_id);

                    if (!$new_IOL) {
                        $new_IOL = new OphInBiometry_LensType_Lens();
                        $new_IOL->id = $_id;
                        $new_IOL->name = $IOL_data->name;
                        $new_IOL->display_name = $IOL_data->name;
                        $new_IOL->display_order = $IOL_data->display_order;
                        $new_IOL->active = $IOL_data->active;
                        $new_IOL->description = 'Merged from operation note cataract element IOL type values';
                        $new_IOL->save();
                    } else {
                        // we do not modify/re-import them again at this point
                    }

                    // update the existing data
                    $current_operations = Element_OphTrOperationnote_Cataract::model()->findAllByAttributes(array('iol_type_id'=>$existing['iol_type_id']));
                    $complications_none = OphTrOperationnote_CataractComplications::model()->findByAttributes(array('name'=>'None'));

                    foreach ($current_operations as $operation) {
    //                    var_dump($operation);
                        if (!count($operation->complications)) {
                            $operation->updateComplications(array($complications_none->id));
                            $operation->refresh();
                        }

                        $operation->iol_type_id = $new_IOL->id;
                        if (!$operation->save()) {
                            throw new Exception('Error saving cataract element data!');
                        }
                    }
                    foreach (OphInBiometry_LensType_Lens_Institution::model()->findAll('lens_type_id = :id', array(':id', $existing['iol_type_id'])) as $mapping) {
                        $mapping->lens_type_id = $new_IOL->id;
                        $mapping->save();
                    }
                }
            }
            $this->dbConnection->createCommand("ALTER TABLE et_ophtroperationnote_cataract ADD CONSTRAINT et_ophtroperationnote_cataract_iol_type_id_fk FOREIGN KEY (iol_type_id) REFERENCES ophinbiometry_lenstype_lens(id)")->query();
        }
        $setting = SettingInstallation::model()->find("`key`='opnote_lens_migration_link'");
        $setting->value = 'off';
        $setting->save();
        $this->redirect(array('/OphInBiometry/lensTypeAdmin/list'));
    }
}
