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
$logoHelper = new LogoHelper();
?>

<div class="logo">
    <?= $logoHelper->render() ?>
</div>
<div style="position: relative; top: 30px;">
    <div style="float: left;">
        <div class="to-address">
            <div class="to-address-header">
                To:
            </div>
            <div class="to-address-address" style="font-weight: bold;">
                <?php echo str_replace("\n", '<br/>', CHtml::encode($toAddress))?>
            </div>
        </div>
    </div>
    <div>
    <?php if ($element->site) {?>
        <div class="right-align">
                <?php
                echo $element->site->getLetterAddress(array(
                    'include_name' => true,
                    'delimiter' => '<br />',
                    'include_telephone' => true,
                    'include_fax' => true,
                )) ?>
                <?php if ($element->direct_line || $element->fax) { ?>
                    <br/>
                <?php } ?>
                <?php if ($element->direct_line) { ?>
                    <br/><?php echo $element->getAttributeLabel('direct_line') ?>: <?php echo $element->direct_line ?>
                <?php } ?>
                <?php if ($element->fax) { ?>
                    <br/><?php echo $element->getAttributeLabel('fax') ?>: <?php echo $element->fax ?>
                <?php } ?>
                <div class="date"><br/><?php echo date(Helper::NHS_DATE_FORMAT, strtotime($date)) ?><?php if ($clinicDate) { ?> (clinic date <?php echo date(Helper::NHS_DATE_FORMAT, strtotime($clinicDate)) ?>)<?php } ?></div>
        </div>
    <?php }?>
    </div>
</div>
<br/><br/>
