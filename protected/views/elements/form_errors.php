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
<?php if (isset($errors) && !empty($errors)) {
    ?>
	<div class="alert-box error with-icon<?php if(isset($bottom) && $bottom) {
    echo ' bottom';
}
    ?>">
		<p>Please fix the following input errors:</p>
		<?php foreach ($errors as $field => $errs) {
    ?>
			<?php foreach ($errs as $err) {
    ?>
				<ul>
					<li>
						<?php echo $field.': '.$err?>
					</li>
				</ul>
			<?php
}
    ?>
		<?php
}
    ?>
	</div>
<?php
}?>
<script type="text/javascript">
	$(document).ready(function () {
		<?php
            if (isset($elements) && is_array($elements)) {
                foreach ($elements as $element) {
                    ?>
		var errorObject = <?php $element->getFrontEndErrors();
                    ?>;
		for (k = 0; k < errorObject.length; k++) {
			//$('[id*=' + errorObject[k] + ']').addClass('highlighted-error');
      var $field = $('#' + errorObject[k]);
			if ($field.length) {
			  if ($field.is('tr')) {
			    $field.addClass('highlighted-error');
        } else {
          if(! $field.parent().hasClass('highlighted-error')) {
            $field.wrap("<div class='highlighted-error'></div>");
          }
        }
			} else {
				if(! $('[id*="' + errorObject[k] + '"]').parent().hasClass('highlighted-error')) {
					$('[id*="' + errorObject[k] + '"]:not(:hidden)').wrap("<div class='highlighted-error'></div>");
				}
			}
		}
		<?php

                }
            }?>
	});

</script>
