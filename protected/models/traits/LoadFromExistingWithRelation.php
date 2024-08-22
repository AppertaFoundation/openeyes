<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Trait LoadFromExistingWithRelation
 *
 * This trait addresses an issue in the BaseElement's loadFromExisting method,
 * specifically concerning the handling of HAS_MANY relationships, as described in OE-15087.
 * The problem manifests when copying data from a source model to a new model, resulting in
 * unintended movement rather than copying of child models. This issue was particularly notable
 * in scenarios involving 'Refraction,' 'Cover Test,' 'Nine Positions,' and 'Strabismus management.'
 *
 * Problem Description:
 * The current implementation of loadFromExisting in BaseElement lacks the necessary granularity
 * when copying attributes, leading to inadvertent movement of child models instead of copying
 * in cases of HAS_MANY relationships. Furthermore, the auto_save_relations behavior compounds
 * this problem by automatically reassigning child models without appropriate checks.
 *
 * Proposed Solution (Refactoring and Enhancements):
 * To resolve OE-15087 and related issues, the specific code addressing this problem will be
 * refactored into the BaseElement. Moreover, the auto_save_relations behavior will be updated
 * to prevent automatic reassignment of child models unless explicitly intended by developers.
 *
 * Refactoring Scope:
 * - Refactor the code addressing OE-15087 into BaseElement, with necessary test adjustments.
 * - Enhance auto_save_relations to prevent unintended reassignment of child models to new parents.
 *
 * Proposed auto_save_relations Behavior Update:
 * The updated behavior will include a check to ensure that child models are not automatically
 * reassigned to new parents. If a reassignment is attempted, an exception will be thrown,
 * requiring developers to handle such scenarios explicitly outside of the auto_save behavior.
 *
 * Removal Path:
 * These changes aim to provide more consistent and robust behavior for handling relationships.
 * Once integrated, the targeted changes from OE-15087 and related enhancements will be deprecated
 * in favor of the standardized, improved behavior. Developers should plan for the removal of
 * LoadFromExistingWithRelation trait as part of the deprecation process.
 *
 * Tickets:
 * https://openeyes.atlassian.net/browse/OE-15108
 * https://openeyes.atlassian.net/browse/OE-15087
 */
trait LoadFromExistingWithRelation
{
    public function loadFromExisting($element)
    {
        foreach ($this->copiedFields() as $attribute) {
            if ($element->getMetaData()->hasRelation($attribute)) {
                $relation = $this->getActiveRelation($attribute);

                if (!is_a($relation, \CActiveRecord::HAS_MANY)) {
                    throw new \RuntimeException("Can only copy HAS_MANY relations ... use the fk attribute for single relationships that should be copied");
                }

                $this->$attribute = array_map(function ($entry) {
                    $new_entry = clone $entry;
                    $new_entry->setIsNewRecord(true);

                    return $new_entry;
                }, $element->$attribute);
            } else {
                $this->$attribute = $element->$attribute;
            }
        }
    }
}
