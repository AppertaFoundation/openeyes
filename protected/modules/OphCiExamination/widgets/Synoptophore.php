<?php

namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\Synoptophore_ReadingForGaze;

class Synoptophore extends \BaseEventElementWidget
{
    public function getAllReadingGazeTypes()
    {
        return [
            [Synoptophore_ReadingForGaze::RIGHT_UP, Synoptophore_ReadingForGaze::CENTER_UP, Synoptophore_ReadingForGaze::LEFT_UP],
            [Synoptophore_ReadingForGaze::RIGHT_MID, Synoptophore_ReadingForGaze::CENTER_MID, Synoptophore_ReadingForGaze::LEFT_MID],
            [Synoptophore_ReadingForGaze::RIGHT_DOWN, Synoptophore_ReadingForGaze::CENTER_DOWN, Synoptophore_ReadingForGaze::LEFT_DOWN],
        ];
    }

    /**
     * @param $attr
     * @return mixed|string
     */
    public function getReadingAttributeLabel($attr)
    {
        return Synoptophore_ReadingForGaze::model()->getAttributeLabel($attr);
    }

    public function getJsonDirectionOptions()
    {
        return \CJSON::encode(
            array_map(function ($direction) {
                return [
                    'id' => $direction->id,
                    'label' => $direction->name
                ];
            }, Synoptophore_ReadingForGaze::model()
                    ->direction_options)
        );
    }

    public function getJsonDeviationOptions()
    {
        return \CJSON::encode(
            array_map(function ($deviation) {
                return [
                    'id' => $deviation->id,
                    'label' => $deviation->name,
                    'abbreviation' => $deviation->abbreviation,
                ];
            }, Synoptophore_ReadingForGaze::model()
                ->deviation_options)
        );
    }

    public function getJsonHeaders()
    {
        return \CJSON::encode(
            Synoptophore_ReadingForGaze::model()
                    ->attributeLabels()
        );
    }


    protected function ensureRequiredDataKeysSet(&$data)
    {
        foreach (['right', 'left'] as $side) {
            $data["{$side}_readings"] = $data["{$side}_readings"] ?? [];
        }
    }

    protected function getViewForEntry()
    {
        return $this->getViewNameForPrefix('Synoptophore_ReadingForGaze');
    }

    protected function getViewDataForEntry(Synoptophore_ReadingForGaze $entry, $index = '{{row_count}}')
    {
        return [
            'row_count' => $index,
            'field_prefix' => \CHtml::modelName($this->element) . "[entries][{$index}]",
            'entry' => $entry
        ];
    }
}
