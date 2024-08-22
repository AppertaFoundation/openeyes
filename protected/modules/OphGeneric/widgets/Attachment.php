<?php
/**
 * OpenEyes.
 *
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class Attachment extends BaseModuleWidget
{
    public $field_name = 'oe-attachment';

    const LEFT = 8966001;
    const RIGHT = 18944008;
    const BOTH = 40638003;

    // required
    public $event_ids;
    public $form;
    public $element;

    public $allow_attach = false;
    public $show_titles;
    public $image_size = '80';

    public $event_attachments = [];
    public $group_titles = [];
    public $is_examination = false;


    public function init()
    {
        $assetManager = Yii::app()->getAssetManager();
        $widgetPath = $assetManager->publish('protected/modules/OphGeneric/widgets/js');
        Yii::app()->clientScript->registerScriptFile($widgetPath . '/Attachment.js');
        parent::init();

        // get the grouped attachments for each event
        foreach ($this->event_ids as $event_id) {
            $this->getAttachments($event_id);
        }
    }

    /**
     * @param $event_id
     * @param $short_code
     * @param string $condition
     */
    public function getAttachments($event_id)
    {
        $api = Yii::app()->moduleAPI->get('OphGeneric');
        $criteria = new CDbCriteria();
        // join tables to get attachments for given event id
        $criteria->with = [
            'eventAttachmentItems',
            'eventAttachmentItems.attachmentData',
            'eventAttachmentItems.attachmentData.attachmentType',
            'eventAttachmentItems.attachmentData.mimeType',
        ];


        $criteria->together = true;
        $criteria->addCondition('t.event_id = :event_id');
        $criteria->params[':event_id'] = $event_id;
        $model = EventAttachmentGroup::model()->findAll($criteria);

        // save each group of attachments into its own array
        $is_single_attachment_event = false;
        if (sizeof($model) == 1 && sizeof($model[0]->eventAttachmentItems) == 1) {
            $is_single_attachment_event = true;
        }

        foreach ($model as $group_items) {
            if ($group_items && $group_items->eventAttachmentItems) {
                $group = [];
                foreach ($group_items->eventAttachmentItems as $event_item) {
                    $group[] = [
                        'attachmentData' => $event_item->attachmentData,
                        'preSelected' => $event_item->event_document_view_set === null ? ($is_single_attachment_event ? "selected" : "") : $event_item->event_document_view_set,
                        'attachmentType' => AttachmentType::model()->findByPk($event_item->attachmentData->attachment_type),
                        'event_id' => $event_id,
                        'group_id' => $group_items->id,
                    ];
                }

                $group_side = $this->getSide($group_items);
                if (!isset($this->event_attachments[$group_side])) {
                    $this->event_attachments[$group_side] = [];
                }

                $group_already_present = false;
                foreach ($this->event_attachments[$group_side] as $group_side_) {
                    if ($group_side_[0]['group_id'] == $group_items->id) {
                        $group_already_present = true;
                        break;
                    }
                }
                if (!$group_already_present) {
                    $event = Event::model()->findByPk($event_id);
                        $event_subtype = isset($event->firstEventSubtypeItem) ? $event->firstEventSubtypeItem->event_subtype : "";
                        $this->group_titles[$group_side] = "{$api->getLaterality($event_id)->getAdjective()} {$event_subtype} ({$event->event_date})";
                    array_push($this->event_attachments[$group_side], $group);
                }
            }
        }
    }

    /**
     * Get side of a group based on following rules:
     * left+right   => both
     * both+...     => both
     * left         => left
     * right        => right
     *
     * @param $group_items
     * @return null|string
     */
    private function getSide($group_items)
    {
        $left = 0;
        $right = 0;

        foreach ($group_items->eventAttachmentItems as $event_item) {
            if ($event_item->attachmentData->body_site_snomed_type == Attachment::BOTH) {
                return "BOTH";
            } elseif ($event_item->attachmentData->body_site_snomed_type == Attachment::LEFT) {
                $left++;
            } elseif ($event_item->attachmentData->body_site_snomed_type == Attachment::RIGHT) {
                $right++;
            }
        }

        if ($left && $right) {
            return "BOTH";
        } elseif ($left) {
            return "LEFT";
        } elseif ($right) {
            return "RIGHT";
        } else {
            return null;
        }
    }
}
