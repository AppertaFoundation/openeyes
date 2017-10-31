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
<!DOCTYPE html>
<html lang="en">
<head>
	<?php $this->renderPartial('//base/head/_meta'); ?>
	<?php $this->renderPartial('//base/head/_assets'); ?>
	<?php $this->renderPartial('//base/head/_tracking'); ?>
</head>
<body class="open-eyes">

	<?php $this->renderPartial('//base/_banner_watermark'); ?>
	<?php $this->renderPartial('//base/_debug'); ?>

	<div class="container main" role="main">

		<?php $this->renderPartial('//base/_header'); ?>

		<div class="container content">
			<?php echo $content; ?>
		</div><!-- /.content.container -->

		<?php $this->renderPartial('//base/_footer'); ?>

	</div><!-- /.main.container -->
</body>
</html>
