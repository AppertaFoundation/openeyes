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
?>
<?php /** @var EsignElementWidget $this */ ?>
<?php $signatures = $this->element->getSignatures(); ?>
<?php if (count($signatures) > 0): ?>
    <p><b>E-signature<?= count($signatures) > 1 ? "s" :""?>:</b></p>
    <?php foreach ($signatures as $signature): ?>
        <?php if($this->isBeingSigned($signature)) {
            $this->widget(SignatureCapture::class, [
                "submit_url" => Yii::app()->createUrl(
                        $this->controller->module->id."/".$this->controller->id."/saveCapturedSignature",
                        [
                            "element_id" => $this->element->id,
                            "element_type_id" => $this->element->getElementType()->id,
                            "signature_type" => $signature->type,
                        ]
                ),
                "after_submit_js" => "function(response, widget){window.parent.location.reload();}",
            ]);
        }
        else if($signature->isSigned()){
            echo $signature->getPrintout();
        }
        ?>
    <?php endforeach; ?>
<?php endif; ?>
