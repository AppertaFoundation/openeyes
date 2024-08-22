<?php
/**
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\ModelFactory;

class OphTrOperationNote_Generic_Procedure_DataFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'proc_id' => \Procedure::factory(),
            'default_text' => ''
        ];
    }

    /**
     * @param Procedure|ProcedureFactory|string|int $procedure
     * @return OphTrOperationNote_Generic_Procedure_DataFactory
     */
    public function forProcedure($procedure): self
    {
        return $this->state([
            'proc_id' => $procedure
        ]);
    }

    /**
     * @param string $text
     * @return OphTrOperationNote_Generic_Procedure_DataFactory
     */
    public function forDefaultText(string $text): self
    {
        return $this->state([
            'default_text' => $text
        ]);
    }
}
