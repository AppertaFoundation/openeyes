<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php $this->renderPartial('//base/_head')?>
</head>
<body class="open-eyes">

	<?php $this->renderPartial('//base/_banner_watermark')?>
	<?php $this->renderPartial('//base/_debug')?>

	<div class="container main" role="main">

		<header class="header row">

			<!-- Branding (logo) -->
			<div class="large-2 column">
				<?php $this->renderPartial('//base/_brand'); ?>
			</div>

			<!-- Patient panel -->
			<div class="large-4 medium-5 column">
				<?php if ($this->renderPatientPanel === true) {
					$this->renderPartial('//patient/_patient_id');
				}?>
			</div>

			<!-- User panel (with site navigation) -->
			<div class="large-6 medium-7 column">
				<?php $this->renderPartial('//base/_form'); ?>
			</div>
		</header><!-- /.header -->

		<div class="container content">
			<?php echo $content; ?>
		</div><!-- /.content -->

		<?php $this->renderPartial('//base/_footer')?>

	</div><!-- /.main.container -->
</body>
</html>
