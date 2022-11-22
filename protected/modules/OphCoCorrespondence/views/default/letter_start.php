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

$logo_helper = new LogoHelper();
?>
<header class="print-header">
    <div class="logo">
        <?= $logo_helper->render('//base/_logo', 70, $element->site->id, true); ?>
    </div>
</header>

<main class="print-main">
    <div class="flex-layout normal-font">
        <div>
            <p class="nowrap" <?= (!$clinicDate || strtotime('today') === $clinicDate ? 'style="visibility: hidden;"' : '') ?>>
                Date of visit: <b><?php echo date(Helper::NHS_DATE_FORMAT, $clinicDate) ?></b>
            </p>
            <?php
            $add_x_pad = SettingMetadata::model()->getSetting('correspondence_to_address_x_padding');
            $add_y_pad = SettingMetadata::model()->getSetting('correspondence_to_address_y_padding');
            $x_padding = '';

            for ($y = 0; $y < $add_x_pad; $y++) {
                $x_padding .= '&nbsp;';
            }

            for ($y = 0; $y < $add_y_pad; $y++) {?>
                <div class="spacer"></div>
            <?php }
            echo $x_padding;?>            
            <br/>
            <div class="address-to"
                <?= // OE-11074 This inline css is to solve the word-wrapping issue for Australia Clients
                    SettingMetadata::model()->getSetting('default_country') === 'Australia' ? "style='white-space: nowrap'" : null;
                ?>>
                <?php echo $x_padding . str_replace("\n", '<br/>' . $x_padding, CHtml::encode($toAddress)) ?>
            </div>
        </div>

        <div class="address-from change-font large-font" style="text-align:right;">
            <?php if ($element->site) { ?>
                <h5 <?= // OE-11074 This inline css is to solve the word-wrapping issue for Australia Clients
                    SettingMetadata::model()->getSetting('default_country') === 'Australia' ? "style='white-space: nowrap'" : null;
                ?> >
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
                </h5>
            <?php } ?>
            <div class="date">
                <br/><?php echo date(Helper::NHS_DATE_FORMAT, strtotime($date)) ?>
            </div>
        </div>
    </div>
    <br/><br/>
</main>
