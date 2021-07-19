<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="element-fields">
    <div class="element-data full-width">
        <div class="alert-box issue">All signatures required to complete consent form. <a href="#" onclick="bluejay.demoSignatureDeviceLink();">Connect your e-Sign device</a></div>

        <table class="last-left">
            <thead>
            <tr>
                <th></th>
                <th>Role</th>
                <th>Signatory</th>
                <th>Date</th>
                <th>Signature</th>
            </tr>
            </thead>
            <tbody>
                <?php
                    $this->widget('application.widgets.EsignPINField', array(
                        'element' => $element,
                        'signatory_label' => 'Consultant 1',
                        'row_id' => '1',
                    ));
                ?>
                <?php
                    $this->widget('application.widgets.EsignPINField', array(
                        'element' => $element,
                        'signatory_label' => 'Consultant 2',
                        'row_id' => 2,
                    ));
                ?>
                <?php
                    $this->widget('application.widgets.EsignUsernamePINField', array(
                        'element' => $element,
                        'signatory_label' => 'Second opinion 1',
                        'row_id' => 3,
                    ));
                ?>
            </tbody>
        </table>

    </div>
</div>