<?php

class m210810_115137_migrate_risks_from_benfitrisk extends OEMigration
{
    public function up()
    {
        if (!$this->verifyTableExists('et_ophtrconsent_benefitrisk_risk')) {
            return true;
        }

        $this->alterOEColumn('et_ophtrconsent_benfitrisk','risks','mediumtext',true);

        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where(
            'class_name = :class_name',
            array(':class_name' => 'OphTrConsent')
        )->queryScalar();

        $consent_events = \Event::model()->findAll('event_type_id=:event_type_id', [':event_type_id'=>$event_type_id]);

        foreach ($consent_events as $event) {
            $append_string = '';
            $benefits_and_risks = $event->getElementByClass(Element_OphTrConsent_BenefitsAndRisks::class);

            if ($benefits_and_risks) {
                $old_risks = $this->dbConnection->createCommand()
                    ->select('b.name as name')
                    ->from('et_ophtrconsent_benefitrisk_risk br')
                    ->leftJoin('benefit b', 'b.id = br.risk_id')
                    ->where('element_id='.$benefits_and_risks->id)
                    ->queryAll();

                if (count($old_risks) > 0) {
                    foreach ($old_risks as $old_risk) {
                        $append_string .= "<li>".$old_risk['name']."</li>";
                    }
                }

                $benefits_and_risks->risks .= '<ul>'.$append_string.'</ul>';
                $benefits_and_risks->save();
            }
        }

        $this->dropOETable('et_ophtrconsent_benefitrisk_risk', true);
    }

    public function safeDown()
    {
        return false;
    }
}
