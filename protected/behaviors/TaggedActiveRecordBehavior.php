<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
/**
 * Class TaggedActiveRecordBehavior
 *
 * Attach this behaviour to all Active Records that have
 * MANY_MANY relation to Tags
 */

class TaggedActiveRecordBehavior extends CActiveRecordBehavior
{
    /**
     * Translates tag names from user input
     * into an array of tag ids. If tag name
     * cant be found, creates one.
     *
     * @return bool
     */

    public function beforeSave($event)
    {
        if(is_array($this->owner->tags))
        {
            return parent::beforeSave($event);
        }

        if($this->owner->tags !== '')
        {
            $tags = explode(",", $this->owner->tags);
            $tag_ids=array();
            foreach($tags as $tag)
            {
                // Get tag id...
                if($t = Tag::model()->findByAttributes(['name'=>$tag]))
                {
                    $tag_ids[] = $t->id;
                }
                // ...or create new tag
                else
                {
                    $t = new Tag();
                    $t->name = $tag;
                    $t->save();
                    $tag_ids[] = $t->id;
                }
            }

            $this->owner->tags = $tag_ids;
        }
        else
        {
            $this->owner->tags = array();
        }

        return parent::beforeSave($event);
    }

    /**
     * @return string
     *
     * Returns the list of tag names,
     * separated by comma+space (', ')
     */

    public function getTagNames()
    {
        return implode(', ', array_map(function($e){ return $e->name; }, $this->owner->tags));
    }
}