<?php

class OescapeSummaryItem extends BaseActiveRecord
{
    public function tableName()
    {
        return 'oescape_summary_item';
    }

    public function relations()
    {
        return array(
            'event_type' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
        );
    }

    /**
     * Named scope to fetch enabled items for the given subspecialty.
     *
     * @param int|null $subspecialty_id Null for default episode summary
     *
     * @return oescapeSummaryItem
     */
    public function enabled($subspecialty_id = null)
    {
        $criteria = array(
            'join' => 'inner join oescape_summary on oescape_summary.item_id = t.id',
            'order' => 'oescape_summary.display_order',
        );

        if ($subspecialty_id) {
            $criteria['condition'] = 'oescape_summary.subspecialty_id = :subspecialty_id';
            $criteria['params'] = array('subspecialty_id' => $subspecialty_id);
        } else {
            $criteria['condition'] = 'oescape_summary.subspecialty_id is null';
        }

        $this->getDbCriteria()->mergeWith($criteria);

        return $this;
    }

    /**
     * Named scope to fetch available (non-enabled) items for the given subspecialty.
     *
     * @param int|null $subspecialty_id Null for default oescape summary
     *
     * @return oescapeSummaryItem
     */
    public function available($subspecialty_id = null)
    {
        $criteria = array(
            'join' => 'left join oescape_summary on oescape_summary.item_id = t.id and oescape_summary.subspecialty_id ',
            'condition' => 'oescape_summary.id is null',
            'order' => 't.event_type_id, t.name',
        );

        if ($subspecialty_id) {
            $criteria['join'] .= '= :subspecialty_id';
            $criteria['params'] = array('subspecialty_id' => $subspecialty_id);
        } else {
            $criteria['join'] .= 'is null';
        }

        $this->getDbCriteria()->mergeWith($criteria);

        return $this;
    }

    /**
     * Assign the given items to the given oescape summary.
     *
     * @param array    $item_ids
     * @param int|null $subspecialty_id
     */
    public function assign(array $item_ids, $subspecialty_id = null)
    {
        $this->dbConnection->createCommand()->delete(
            'oescape_summary',
            $subspecialty_id ? 'subspecialty_id = :subspecialty_id' : 'subspecialty_id is null',
            array('subspecialty_id' => $subspecialty_id)
        );

        if ($item_ids) {
            $rows = array();
            foreach ($item_ids as $display_order => $item_id) {
                $rows[] = array(
                    'item_id' => $item_id,
                    'subspecialty_id' => $subspecialty_id,
                    'display_order' => $display_order,
                );
            }

            $this->dbConnection->getCommandBuilder()->createMultipleInsertCommand('oescape_summary', $rows)->execute();
        }
    }

    public function getClassName()
    {
        return $this->event_type->class_name.'_Episode_'.str_replace(' ', '', $this->name);
    }
}
