<?php

namespace OEModule\OphCiExamination\seeders;

use OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications;
use Subspecialty;

class PostOpComplicationsAdminSeeder
{
    protected \DataContext $data_context;

    public function __construct(\DataContext $data_context)
    {
        $this->data_context = $data_context;
    }

    public function __invoke()
    {
        // Create complications and assign to default subspecialty
        $post_op_complications = OphCiExamination_PostOpComplications::factory()
            ->count(rand(2, 5))
            ->create();

        OphCiExamination_PostOpComplications::model()->assign(
            array_map(function ($complication) {
                return $complication->id;
            }, $post_op_complications),
            \Yii::app()->session->getSelectedInstitution()->id,
            \Yii::app()->session->getSelectedFirm()->getSubspecialtyID()
        );

        return [
            'post_op_complications' => [
                'defaultSubspecialty' => array_map(
                    function ($complication) {
                        return [
                            'id' => $complication->id,
                            'name' => $complication->name
                        ];
                    },
                    $post_op_complications
                )
            ]
        ];
    }
}
