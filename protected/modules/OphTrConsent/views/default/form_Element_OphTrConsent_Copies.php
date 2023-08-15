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
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 * @author OpenEyes <info@openeyes.org.uk>
 * @package default
 */
?>
<div class="element-fields full-width">
    <div class="cols-10">
        <table class="cols-full last-left">
            <colgroup>
                <col class="cols-6">
            </colgroup>
            <tbody>
                <tr>
                    <td>
                        Paper copy accepted by patient?
                    </td>
                    <td>
                        <fieldset data-test='consent-paper-copies'>
                            <?= $form->radioButtons(
                                $element,
                                'copies_id',
                                'OphTrConsent_Paper_Copies',
                                null,
                                false,
                                false,
                                false,
                                false,
                                array('nowrapper' => true, ),
                                null
                            );
?>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>