<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
  <div class="cols-3 column right" id="external-referral-help">
    <a href="javascript:void(0)" class="windip-help">Problems reaching WinDIP ?</a>
  </div>
<div class="element">
    <div class="element-data">
      <div class="cols-6 column hidden end" id="external-referral-popup-blocked">
        Unable to automatically open WinDip. Please click the button below.
      </div>
        <?php if( !$is_new_referral ): ?>
          <div class="cols-6 column" id="external-referral-button">
            <a href="<?=$external_link?>" class="button primary small">click to view</a>
          </div>
        <?php endif; ?>
      <div class="cols-12 column <?php echo $is_new_referral ? 'hidden' : '' ?>" id="external-referral-status">
        placeholder for displaying the status information and/or link for the referral in windip.
      </div>
    </div>
</div>
<?php if( $is_new_referral ): ?>
  <script type="text/javascript">
    $(document).on('ready', function() {
      createNewWindow('<?= $external_link?>');
    });
  </script>
<?php $this->getApp()->user->setState("new_referral", false); ?>
<?php endif; ?>