<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoMessaging\factories\models;

use OE\factories\ModelFactory;
use OE\factories\models\UserFactory;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;

class OphCoMessaging_Message_CommentFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'element_id' => ModelFactory::factoryFor(Element_OphCoMessaging_Message::class),
            'comment_text' => $this->faker->sentence(128),
            'marked_as_read' => false,
            'created_user_id' => \User::factory()->useExisting()
        ];
    }

    /**
     * @param Element_OphCoMessaging_Message|Element_OphCoMessaging_MessageFactory|int|string $element
     * @return OphCoMessaging_Message_CommentFactory
     */
    public function withElement($element): self
    {
        return $this->state([
            'element_id' => $element->id
        ]);
    }

    public function withCommentText(string $text): self
    {
        return $this->state([
            'comment_text' => $text
        ]);
    }

    /**
     * @param \User|\OEWebUser|UserFactory|int|string $user
     * @return OphCoMessaging_Message_CommentFactory
     */
    public function withUser($user): self
    {
        return $this->state([
            'created_user_id' => $user
        ]);
    }

    /**
     * Override persistInstance to call save with its third parameter, $allow_overriding, set to true.
     * Setting that parameter to true ensures that the value of created_user_id is not overwritten by the
     * save function.
     *
     * @param mixed $instance
     * @return bool
     */
    protected function persistInstance($instance): bool
    {
        return $instance->save(false, null, true);
    }
}
