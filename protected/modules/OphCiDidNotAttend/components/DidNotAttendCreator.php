<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiDidNotAttend\components;

use OEModule\OphCiDidNotAttend\models\Comments;

class DidNotAttendCreator extends \EventCreator
{
    private $comments;

    public function setComments($comment_text)
    {
        $comments = new Comments();
        $comments->comment = $comment_text;
        $this->comments = $comments;
    }

    public function setSource($source) {
        $this->event->automated_source = $source;
    }

    public function setDate($date)
    {
        $this->event->event_date = $date;
    }

    protected function saveElements($event_id)
    {
        // Save Comments
        if ($this->comments) {
            $this->comments->event_id = $event_id;
            if (!$this->comments->save()) {
                return false;
            }
        }

        return $event_id;
    }
}
