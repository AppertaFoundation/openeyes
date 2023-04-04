<?php

namespace OEModule\OphCiExamination\seeders;

use OE\seeders\BaseSeeder;
use OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications;

class PostOpComplicationsAdminSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        // Create complications and assign to default subspecialty
        $post_op_complications = OphCiExamination_PostOpComplications::factory()
            ->count(rand(2, 5))
            ->create();

        OphCiExamination_PostOpComplications::model()->assign(
            array_map(function ($complication) {
                return $complication->id;
            }, $post_op_complications),
            $this->app_context->getSelectedInstitution()->id,
            $this->app_context->getSelectedFirm()->getSubspecialtyID()
        );

        $unassigned_complications = OphCiExamination_PostOpComplications::factory()->count(2)->create();
        $subspecialty = \Subspecialty::factory()->create([
            'name' => 'subspecialty ' . time()
        ]);

        return [
            'post_op_complications' => [
                'for_default_subspecialty' => $this->mapModels($post_op_complications),
                'unassigned' => $this->mapModels($unassigned_complications)
            ],
            'unused_subspecialty' => $this->mapModels([$subspecialty])[0]
        ];
    }

    protected function mapModels(array $models): array
    {
        return array_map(
            function ($model) {
                return [
                    'id' => $model->getPrimaryKey(),
                    'name' => $model->name
                ];
            },
            $models
        );
    }
}
