<?php

namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\ContrastSensitivity_Result;

class ContrastSensitivity extends \BaseEventElementWidget
{
    public function renderEntriesForElement($results)
    {
        foreach ($results as $i => $result) {
            $this->render($this->getViewForEntry(), $this->getViewDataForEntry($result, (string) $i));
        }
    }

    public function renderEntryTemplate()
    {
        $this->render($this->getViewForEntry(), $this->getViewDataForEntry(new ContrastSensitivity_Result()));
    }

    protected function ensureRequiredDataKeysSet(&$data)
    {
        if (!isset($data['results'])) {
            $data['results'] = [];
        }
    }

    /**
     * @param $attr
     * @return mixed|string
     */
    public function getResultAttributeLabel($attr)
    {
        return ContrastSensitivity_Result::model()->getAttributeLabel($attr);
    }

    protected function getViewForEntry()
    {
        return $this->getViewNameForPrefix('ContrastSensitivity_Result');
    }

    protected function getViewDataForEntry(ContrastSensitivity_Result $result, $index = '{{row_count}}')
    {
        return [
            'row_count' => $index,
            'field_prefix' => \CHtml::modelName($this->element) . "[results][{$index}]",
            'entry' => $result
        ];
    }
}
