<?php

namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\PrismReflex_Entry;

class PrismReflex extends \BaseEventElementWidget
{
    public function renderEntriesForElement($entries)
    {
        foreach ($entries as $i => $entry) {
            $this->render($this->getViewForEntry(), $this->getViewDataForEntry($entry, (string) $i));
        }
    }

    public function renderEntryTemplate()
    {
        $this->render($this->getViewForEntry(), $this->getViewDataForEntry(new PrismReflex_Entry()));
    }

    protected function ensureRequiredDataKeysSet(&$data)
    {
        if (!isset($data['entries'])) {
            $data['entries'] = [];
        }
    }

    /**
     * @param $attr
     * @return mixed|string
     */
    public function getReadingAttributeLabel($attr)
    {
        return PrismReflex_Entry::model()->getAttributeLabel($attr);
    }

    protected function getViewForEntry()
    {
        return $this->getViewNameForPrefix(\Helper::getNSShortname(PrismReflex_Entry::class));
    }

    protected function getViewDataForEntry(PrismReflex_Entry $entry, $index = '{{row_count}}')
    {
        return [
            'row_count' => $index,
            'field_prefix' => \CHtml::modelName($this->element) . "[entries][{$index}]",
            'entry' => $entry
        ];
    }
}
