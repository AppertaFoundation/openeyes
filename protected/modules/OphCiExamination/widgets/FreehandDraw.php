<?php
/**
 * (C) OpenEyes Foundation, 2019
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
namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\FreehandDraw as FreehandDrawElement;
use OEModule\OphCiExamination\models\FreehandDraw_Entry;

class FreehandDraw extends \BaseEventElementWidget
{
    use \FileHelperTrait;
    public static $moduleName = 'OphCiExamination';

    /**
     * @return FreehandDrawElement
     */
    protected function getNewElement()
    {
        return new FreehandDrawElement();
    }

    /**
     * @param FreehandDrawElement $element
     * @param $data
     * @throws \CException
     * @throws \Exception
     */
    protected function updateElementFromData($element, $data)
    {
        if (!is_a($element, 'OEModule\OphCiExamination\models\FreehandDraw')) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }

        $image_data = \Yii::app()->request->getParam('image', []);

        // pre-cache current entries so any entries that remain in place will use the same db row
        $entries_by_id = [];
        if (!$element->isNewRecord) {
            foreach ($element->entries as $entry) {
                $entries_by_id[$entry->id] = $entry;
            }
        }

        if (array_key_exists('entries', $data)) {
            $entries = [];
            foreach ($data['entries'] as $i => $drawing_entry) {
                $entry = new FreehandDraw_Entry();
                $id = $drawing_entry['id'];
                if ($id && array_key_exists($id, $entries_by_id)) {
                    $entry = $entries_by_id[$id];
                }
                $entry->comments = $drawing_entry['comments'];
                $entry->protected_file_id = $drawing_entry['protected_file_id'];
                $file_content = $image_data[$i]['data'];
                $name = $image_data[$i]['name'];
                $is_edited = $image_data[$i]['is_edited'] ?? 0;

                if ($is_edited) {
                    $protected_file = $this->createProtectedFileFromDataURL($file_content, $name);
                    $entry->protected_file_id = $protected_file->id;

                    if ($drawing_entry['protected_file_id']) {
                        $old_protected_file = \ProtectedFile::model()->findByPk($drawing_entry['protected_file_id']);
                        $old_protected_file->delete();
                    }
                }

                $entries[] = $entry;
            }

            $element->entries = $entries;
        } else {
            $element->entries = [];
        }
    }
}
