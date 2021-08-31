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
<div class="row">
    <h4>Two ways to sync your device:</h4>
    <p>1) Enter the following URL directly in the device and use your OE login</p>
    <div class="highlighter large-text"><?= $url ?></div>
</div>
<div class="qr-code">
    <p>2) Or, scan the QR Code and enter your OE PIN</p>
    <!-- make sure QR is always visible -->
    <div style="height:40vh; max-height: 406px;">
        <img src="<?= $qr->getDataUri(); ?>" width="auto" height="100%">
    </div>
</div>
