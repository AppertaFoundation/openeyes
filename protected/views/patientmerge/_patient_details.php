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

<section class="<?php echo $type; ?> box patient-info js-toggle-container">
	<h3 class="box-title">Personal Details:</h3>
	
	<div class="js-toggle-body">
            
            <div class="row data-row">
                <div class="large-4 column">
                        <div class="data-label">Hospital No.</div>
                </div>
                <div class="large-8 column">
                    <div class="data-value">
                        <div class="hospital-number">
                            <span class="hos_num"></span>
                            <input type="hidden" class="hos_num-input" name="Patients[<?php echo $type; ?>][hos_num]" value="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row data-row">
                <div class="large-4 column">
                        <div class="data-label">NHS Number:</div>
                </div>
                <div class="large-8 column">
                    <div class="data-value">
                        <!-- NHS number -->
                        <div class="nhs-number">
                                <span class="hide-text print-only">
                                        NHS number:
                                </span>
                                <span class="nhsnum">000 000 000</span>
                                <input type="hidden" class="nhsnum-input" name="Patients[<?php echo $type; ?>][nhsnum]" value="000 000 000">
                        </div>
                    </div>
                </div>
            </div>
            
            
            
            <div class="row data-row">
                    <div class="large-4 column">
                            <div class="data-label">First name(s):</div>
                    </div>
                    <div class="large-8 column">
                            <div class="data-value first_name"></div>
                    </div>
            </div>

            <div class="row data-row">
                    <div class="large-4 column">
                            <div class="data-label">Last name:</div>
                    </div>
                    <div class="large-8 column">
                            <div class="data-value last_name"></div>
                    </div>
            </div>
           
            <div class="row data-row">
                    <div class="large-4 column">
                            <div class="data-label">Date of Birth:</div>
                    </div>
                    <div class="large-8 column">
                            <div class="data-value dob"></div>
                            <input type="hidden" class="dob-input" name="Patients[<?php echo $type; ?>][dob]" value="">
                    </div>
            </div>

		<div class="row data-row">
			<div class="large-4 column">
				<div class="data-label">Gender:</div>
			</div>
			<div class="large-8 column">
				<div class="data-value gender"></div>
                                <input type="hidden" class="gender-input" name="Patients[<?php echo $type; ?>][gender]" value="">
			</div>
		</div>
	</div>
</section>




