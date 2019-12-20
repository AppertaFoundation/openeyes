<?php
/**
 * OpenEyes.
 *
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
namespace OEModule\OphCiExamination\models\traits;

trait CustomOrdering {
    private function getCustomOrder($set_id) {
        if ($set_id) {
            $set_item = \OEModule\OphCiExamination\models\OphCiExamination_ElementSetItem::model()->find(
                'set_id = :set_id AND element_type_id = :element_type_id',
                [':set_id' => $set_id, ':element_type_id' => $this->getElementType()->id]
            );
            if (isset($set_item->display_order)) {
                return -1 * $set_item->display_order;
            }
        }
        return parent::getDisplayOrder($set_id);
    }

    public function getDisplayOrder($set_id = null) {
        return $set_id == 'view' && isset($this->default_view_order) ? $this->default_view_order :
            $this->getCustomOrder($set_id);
    }

    /**
     * Added to satisfy condition where the display_order is attempted
     * to be retrieved as attribute `display_order`.
     */
    public function getdisplay_order($set_id = null) {
        return $this->getDisplayOrder($set_id);
    }
}
