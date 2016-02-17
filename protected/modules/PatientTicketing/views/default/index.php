<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>


<?php
$qs_svc = Yii::app()->service->getService($this::$QUEUESET_SERVICE);
?>
	<h1 class="badge"><?= $queueset ? $queueset->name : $category->name ?></h1>

	<div class="box content">
		<?php
		if($queueset) {
			if ($flash_message = Yii::app()->user->getFlash('patient-ticketing-' . $queueset->getId())) {
				?>
				<br />
				<div class="large-12 column">
					<div class="panel">
						<div class="alert-box with-icon success">
							<?php echo $flash_message; ?>
						</div>
					</div>
				</div>
			<?php
			}
		}
		?>
<?php
	$this->renderPartial('form_queueset_select', array(
		'qs_svc' => $qs_svc,
		'category' => $category,
		'queueset' => $queueset
	));

	if ($queueset) {
		$this->renderPartial('ticketlist', array(
			'qs_svc' => $qs_svc,
			'category' => $category,
			'queueset' => $queueset,
			'tickets' => $tickets,
			'patient_filter' => $patient_filter,
			'pages' => $pages
		));

	}
?>
</div>