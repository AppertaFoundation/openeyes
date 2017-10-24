<?php

class m160217_103151_nod_export extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophtroperationnote_cataract', 'pupil_size', 'VARCHAR(10)');
        $this->addColumn('et_ophtroperationnote_cataract_version', 'pupil_size', 'VARCHAR(10)');

        $cataracts = $this->getDbConnection()->createCommand()->select('id, eyedraw')->from('et_ophtroperationnote_cataract')->queryAll();

        foreach ($cataracts as $cataract) {
            $eyedraw = json_decode($cataract['eyedraw']);
            $pupilSize = null;
            if (is_array($eyedraw)) {
                foreach ($eyedraw as $eyedrawEl) {
                    if (property_exists($eyedrawEl, 'pupilSize')) {
                        $pupilSize = $eyedrawEl->pupilSize;
                        break;
                    }
                }
            }
            if ($pupilSize) {
                $this->update('et_ophtroperationnote_cataract', array('pupil_size' => $pupilSize), 'id='.$cataract['id']);
            }
        }

        return true;
    }

    public function down()
    {
        $this->dropColumn('et_ophtroperationnote_cataract', 'pupil_size');
        $this->dropColumn('et_ophtroperationnote_cataract_version', 'pupil_size');

        return true;
    }

}
