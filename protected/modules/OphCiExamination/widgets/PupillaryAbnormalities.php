<?php


namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\PupillaryAbnormalities as PupillaryAbnormalitiesElement;
use OEModule\OphCiExamination\models\PupillaryAbnormalityEntry;

class PupillaryAbnormalities extends \BaseEventElementWidget
{
    public static $moduleName = 'OphCiExamination';
    protected $print_view = 'PupillaryAbnormalities_event_print';

    /**
     * @return FamilyHistoryElement
     */
    protected function getNewElement()
    {
        return new PupillaryAbnormalitiesElement();
    }

    /**
     * @param PupillaryAbnormalityElement $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
        $sides = array(strtolower($data['eye_id']));
        if ($sides[0] == '3') {
            $sides = array('left', 'right');
        }elseif($sides[0] == '2'){
            $sides = array('right');
        }else{
            $sides = array('left');
        }

        if (!is_a($element, 'OEModule\OphCiExamination\models\PupillaryAbnormalities')) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }

        if(array_key_exists('eye_id', $data)){
            $element->eye_id = $data['eye_id'];
        }

        $entries_by_id = array();
        $entries = array();
        foreach ($sides as $side) {

            if (array_key_exists($side  .'_no_pupillaryabnormalities', $data)  && $data[$side . '_no_pupillaryabnormalities'] == 1) {
                if(!$element->{'no_pupillaryabnormalities_date_' . $side}){
                    $element->{'no_pupillaryabnormalities_date_' . $side} = date('Y-m-d H:i:s');
                }elseif($element->{'no_pupillaryabnormalities_date_' . $side}){
                    $element->{'no_pupillaryabnormalities_date_' . $side} = null;
                }
            }

            // pre-cache current entries so any entries that remain in place will use the same db row
            foreach ($element->{'entries_' . $side} as $entry) {
                $entries_by_id[$entry->id] = $entry;
            }

            if (array_key_exists('entries_' . $side, $data)) {
                foreach ($data['entries_' . $side] as $i => $entry) {
                    $abnormality_entry = new PupillaryAbnormalityEntry();
                    $id = $entry['id'];
                    if ($id && array_key_exists($id, $entries_by_id)) {
                        $abnormality_entry = $entries_by_id[$id];
                    }
                    $abnormality_entry->abnormality_id = $entry['abnormality_id'];
                    $abnormality_entry->has_abnormality = array_key_exists('has_abnormality', $entry) ? $entry['has_abnormality'] : null;
                    $abnormality_entry->comments = $entry['comments'];
                    $abnormality_entry->eye_id = $entry['eye_id'];
                    $entries[] = $abnormality_entry;
                }
            }

            $element->entries = $entries;
        }
    }

    /**
     * Gets all required pupillary abnormalities
     * @return mixed
     */
    public function getRequiredAbnormalities()
    {
        $exam_api = \Yii::app()->moduleAPI->get('OphCiExamination');
        return $exam_api->getRequiredAbnormalities($this->patient);
    }

    /**
     * Gets all required missing pupillary abnormalities
     * @return array
     */
    public function getMissingRequiredAbnormalities()
    {
        $current_ids = array_map(function ($e) {
            return $e->abnormality_id;
        },
            $this->element->entries_left);

        $missing = array();
        foreach ($this->getRequiredAbnormalities() as $required) {
            if (!in_array($required->id, $current_ids)) {
                $entry = new PupillaryAbnormalityEntry();
                $entry->abnormality_id = $required->id;
                $missing[] = $entry;
            }
        }

        return $missing;
    }

    /**
     * @param $row
     * @return bool
     */
    public function postedNotChecked($row)
    {
        return \Helper::elementFinder(\CHtml::modelName($this->element) . ".entries.$row.has_abnormality", $_POST)
            == PupillaryAbnormalityEntry::$NOT_CHECKED;
    }
}