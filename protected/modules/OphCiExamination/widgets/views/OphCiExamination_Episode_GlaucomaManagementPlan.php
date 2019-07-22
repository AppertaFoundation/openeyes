<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>


<?php if ($plan) : ?>
  <div class="flex-layout">
    <div class="cols-6 data-group column">
      <table>
        <tbody>
        <tr>
          <th scope="col">Clinic interval</th>
          <td><?= $plan->clinic_interval ?: 'None' ?></td>
        </tr>
        <tr>
          <th scope="col">Photo</th>
          <td><?= $plan->photo->name ?></td>
        </tr>
        <tr>
          <th scope="col">OCT</th>
          <td><?= $plan->oct->name ?></td>
        </tr>
        <tr>
          <th scope="col">Visual Fields</th>
          <td><?= $plan->hfa->name ?></td>
        </tr>
        <tr>
          <th scope="col">Gonio</th>
          <td><?= $plan->gonio->name ?></td>
        </tr>
        <?php if (isset($plan->hrt)) {
            ?>
          <tr>
            <th scope="col">HRT</th>
            <td><?= $plan->hrt->name ?></td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
    <div class="cols-6 data-group column">
        <?php if ($plan->comments) { ?>
          <div class="data-label">Comments:</div>
          <div class="data-value panel comments"><?= Yii::app()->format->nText($plan->comments) ?></div>
        <?php } ?>
    </div>
  </div>

  <div class="flex-layout">
    <div class="cols-6 data-group column">
        <?php if ($plan->hasRight()) {
            $this->render('OphCiExamination_Episode_GlaucomaManagementPlan_side',
                array('plan' => $plan, 'side' => 'right'));
        } ?>
    </div>
    <div class="cols-6 data-group column">
        <?php if ($plan->hasLeft()) {
            $this->render('OphCiExamination_Episode_GlaucomaManagementPlan_side',
                array('plan' => $plan, 'side' => 'left'));
        } ?>
    </div>
  </div>
<?php else : ?>
  <div class="cols-12 column">
    <div class="data-value not-recorded">(not recorded)</div>
  </div>
<?php endif; ?>
