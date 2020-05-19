<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * Conwenience class for all glaucoma elements.
 *
 * The purpose of this class is to enable views to determine if the image
 * string has been set for a given view.
 *
 * Subclasses should extend this class and set the DoodleInfo titles
 * (obtained from the OEEyeDrawWidget class) upon construction.
 */
class OphCiExamination_EyeDrawBase extends BaseEventTypeElement
{
    /** This is the array of doodle names that will be used to detect
     * if the mage string has been set or not. The names are modelled
     * directly on the OEEyeDrawWidget DoodleInfo class titles.
     */
    private $doodleInfo = array();

    /**
     * Set the titles of the eyedraw component. This is done by callers.
     *
     *
     * @param type $titles an array of strings containing the DoodleInfo
     *                     titles to check to see if the eyedraw string has been set or not.
     */
    public function setDoodleInfo($titles)
    {
        $this->doodleInfo = $titles;
    }

    /**
     * Gets the doodle info that has been set on this class.
     *
     * @return array the doodle info; if no doodle info has been set,
     *               the empty array is returned.
     */
    public function getDoodleInfo()
    {
        return $this->doodleInfo;
    }

    /**
     * Does the image string for this EyeDraw object contain any of
     * the EyeDraw doodles?
     *
     * @param string $side a string with it's first character an 'l' (for left) or 'r' (for right).
     *
     * @return bool true of the image string contains
     */
    public function isImageStringSet($side)
    {
        $image_string = false;
        if ($side && strtolower($side{0}) == 'l') {
            $image_string = $this->checkItems(
                $this->doodleInfo,
                $this->image_string_left
            );
        } elseif ($side && strtolower($side{0}) == 'r') {
            $image_string = $this->checkItems(
                $this->doodleInfo,
                $this->image_string_right
            );
        }

        return $image_string;
    }

    /**
     * Checks to see if the specified image string contains any of the strings
     * given in the items array.
     *
     * @param string[] $contents     an array of string to look for in the image string.
     * @param string   $image_string the non-null image string in the form of a JSON EyeDraw string.
     *
     * @return bool true if any of the items exist; false otherwise.
     */
    private function checkItems($contents, $image_string)
    {
        $hasValues = false;
        /* Dirty hack - see if any of the angle grades have values different
         * to -176 - if they have, it's been set; else move on: */
        $json = json_decode($image_string);
        if ($json) {
            foreach ($json as $element) {
                if ($element->{'subclass'} == 'AngleGrade') {
                    if ($element->{'apexY'} != -176) {
                        $hasValues = true;
                    }
                }
            }
        }
        foreach ($contents as $item) {
            if (strpos($image_string, $item) !== false) {
                $hasValues = true;
            }
        }

        return $hasValues;
    }
}
