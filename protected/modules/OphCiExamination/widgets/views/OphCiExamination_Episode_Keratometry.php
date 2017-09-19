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
<?php
if ($keratometry) { ?>
    <div class="row large-12">
        <div class="row">
            <div class="large-3 column">
                <h3 class="element-title">RIGHT</h3>
            </div>
            <div class="large-8 column">
            </div>
        </div>
        <div class="row">
            <div class="large-3 column">
                <h3 class="data-title"></h3>
            </div>
            <div class="large-2 column">
                <h3 class="data-title">Front</h3>
            </div>
            <div class="large-2 column">
                <h3 class="data-title">Back</h3>
            </div>
            <div class="large-5 column">
                <h3 class="data-title">Other</h3>
            </div>
        </div>
        <div class="row">
            <div class="large-3 column">
                <h4 class="data-title">Date</h4>
            </div>
            <div class="large-1 column">
                <h4 class="data-title">K1</h4>
            </div>
            <div class="large-1 column">
                <h4 class="data-title">K2</h4>
            </div>
            <div class="large-1 column">
                <h4 class="data-title">K1</h4>
            </div>
            <div class="large-1 column">
                <h4 class="data-title">K2</h4>
            </div>
            <div class="large-1 column">
                <h4 class="data-title">Kmax</h4>
            </div>
            <div class="large-1 column">
                <h4 class="data-title">TPP</h4>
            </div>
            <div class="large-3 column"></div>
        </div>
        <div class="row">
            <?php
                foreach ($keratometry as $kera) {
                    ?>
                    <div class="large-3 column">
                    <?php
                    $keraDate = new DateTime($kera['created_date']);
                    echo $keraDate->format('d-m-Y');
                        ?>
                    </div>
            <div class="large-1 column">
                <?php
                echo $kera['right_anterior_k1_value'];
                ?>
            </div>
            <div class="large-1 column">
                <?php
                echo $kera['right_axis_anterior_k1_value'];
                ?>
            </div>
            <div class="large-1 column">
                <?php
                echo $kera['right_anterior_k2_value'];
                ?>
            </div>
            <div class="large-1 column">
                <?php
                echo $kera['right_axis_anterior_k2_value'];
                ?>
            </div>
            <div class="large-1 column">
                <?php
                echo $kera['right_kmax_value'];
                ?>
            </div>
            <div class="large-1 column">
                <?php
                echo $kera['right_thinnest_point_pachymetry_value'];
                ?>
            </div>
            <div class="large-3 column"></div><br/>
                <?php
                }
             ?>
        </div>
</div>
    <br/>
    <div class="row large-12">
        <div class="row">
            <div class="large-3 column">
                <h3 class="element-title">LEFT</h3>
            </div>
            <div class="large-8 column">
            </div>
        </div>
        <div class="row">
            <div class="large-3 column">
                <h3 class="data-title"></h3>
            </div>
            <div class="large-2 column">
                <h3 class="data-title">Front</h3>
            </div>
            <div class="large-2 column">
                <h3 class="data-title">Back</h3>
            </div>
            <div class="large-5 column">
                <h3 class="data-title">Other</h3>
            </div>
        </div>
        <div class="row">
            <div class="large-3 column">
                <h4 class="data-title">Date</h4>
            </div>
            <div class="large-1 column">
                <h4 class="data-title">K1</h4>
            </div>
            <div class="large-1 column">
                <h4 class="data-title">K2</h4>
            </div>
            <div class="large-1 column">
                <h4 class="data-title">K1</h4>
            </div>
            <div class="large-1 column">
                <h4 class="data-title">K2</h4>
            </div>
            <div class="large-1 column">
                <h4 class="data-title">Kmax</h4>
            </div>
            <div class="large-1 column">
                <h4 class="data-title">TPP</h4>
            </div>
            <div class="large-3 column"></div>
        </div>
        <div class="row">
            <?php
                foreach ($keratometry as $kera){
                    ?>
                    <div class="large-3 column">
                        <?php
                        $keraDate = new DateTime($kera['created_date']);
                        echo $keraDate->format('d-m-Y');
                        ?>
                    </div>
                    <div class="large-1 column">
                        <?php
                        echo $kera['left_anterior_k1_value'];
                        ?>
                    </div>
                    <div class="large-1 column">
                        <?php
                        echo $kera['left_axis_anterior_k1_value'];
                        ?>
                    </div>
                    <div class="large-1 column">
                        <?php
                        echo $kera['left_anterior_k2_value'];
                        ?>
                    </div>
                    <div class="large-1 column">
                        <?php
                        echo $kera['left_axis_anterior_k2_value'];
                        ?>
                    </div>
                    <div class="large-1 column">
                        <?php
                        echo $kera['left_kmax_value'];
                        ?>
                    </div>
                    <div class="large-1 column">
                        <?php
                        echo $kera['left_thinnest_point_pachymetry_value'];
                        ?>
                    </div>
                    <div class="large-3 column"></div><br/>
                    <?php
                }
 ?>
        </div>
    </div>
    <br/>
        <?php
    }
    ?>
