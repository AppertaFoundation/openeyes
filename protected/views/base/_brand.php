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
$logoUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue.svg') . '/oe-logo.svg');
?>
<div class="oe-logo" id="js-openeyes-btn">
    <svg viewBox="0 0 300.06 55.35" class="oe-openeyes">
        <use xlink:href="<?= $logoUrl . '#openeyes-logo'; ?>"></use>
    </svg>
</div>
<div class="oe-product-info" id="js-openeyes-info">
  <h3>OpenEyes</h3>
  <p class="gap">Release Date: 22nd Jan 2018</p>
  <p class="gap">
    <a href="#activate-pro-theme" id="js-theme-pro" style="display: inline-block; margin-bottom: 4px;">PRO theme (recommended)</a>
    <br/>
    <a href="#activate-classic-theme" id="js-theme-classic">Classic theme (default)</a>
  </p>
  <p class="gap">OpenEyes is released under the AGPL3 license and is free to download and use.</p>
  <p class="gap">
    OpenEyes is maintained by the <a href="https://openeyes.org.uk/" target="_blank">OpenEyes Foundation</a>.
  </p>
  <p class="gap">
    Technical support is provided by <a href="https://abehr.com/" target="_blank">ABEHRdigital</a>.
  </p>
  <p class="gap">
    Send <a href="#">feedback or suggestions.</a>
  </p>
</div>
<script>
    (function(){
        /* IDG demo only */
        // use localStorage for CSS Themes Switching
        var pro = document.getElementById("js-theme-pro");
        var classic = document.getElementById("js-theme-classic");

        pro.onclick = function( e ) {
            e.preventDefault();
            localStorage.setItem( "oeTheme",'pro' );
            location.reload();
        };

        classic.onclick = function( e ) {
            e.preventDefault();
            localStorage.setItem( "oeTheme",'classic' );
            location.reload();
        };
    })();
</script>
