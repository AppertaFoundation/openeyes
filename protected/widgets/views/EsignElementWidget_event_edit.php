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
<div class="element-fields">
    <div class="element-data full-width">
        <?php foreach ($this->element->getInfoMessages() as $msg): ?>
            <div class="alert-box info"><?=CHtml::encode($msg)?></div>
        <?php endforeach; ?>
        <?php if(!$this->isSigningAllowed()): ?>
            <div class="alert-box warning">E-signing of this event will be available at a later stage.</div>
        <?php else: ?>
            <?php if(!$this->element->isSigned()): ?>
                <div class="alert-box issue"><?= $this->element->getUnsignedMessage() ?>
                    <?php if($this->element->usesEsignDevice()): ?>
                        <a href="#" onclick="bluejay.demoSignatureDeviceLink();">Connect your e-Sign device</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
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
                    $row = 0;
                    foreach ($this->element->getSignatures() as $signature) {
                        $this->widget(
                            $this->getWidgetClassByType($signature->type),
                            [
                                "row_id" => $row++,
                                "element" => $this->element,
                                "signature" => $signature,
                            ]
                        );
                    }
                ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        new OpenEyes.UI.EsignElementWidget(
            $(".<?= \CHtml::modelName($this->element) ?>"),
            {
                mode : "<?= $this->mode === $this::$EVENT_VIEW_MODE ? 'view' : 'edit' ?>"
            }
        );
    });
</script>