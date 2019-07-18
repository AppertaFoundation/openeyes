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
<?php if ($elements): ?>
    <div id="vf-slider-container">
    <?php foreach ($elements as $element): ?>
        <div id="OphInVisualfields_Episode_VisualFieldsHistory_element_<?= $element->id ?>" class="OphInVisualfields_Episode_VisualFieldsHistory_element element-fields element-eyes hidden">
            <?php
                $this->render(get_class($this).'_side', array('element' => $element, 'side' => 'right'));
                $this->render(get_class($this).'_side', array('element' => $element, 'side' => 'left'));
            ?>
        </div>
    <?php endforeach ?>
    </div>
    <div id="OphInVisualfields_Episode_VisualFieldsHistory_slider"></div>
    <script>
        $(document).ready(function () {

            var elementIds = window.OphInVisualfields_Episode_VisualFieldsHistory_element_ids;
            var sliderContainer = $('#vf-slider-container');
            var slider = $('#OphInVisualfields_Episode_VisualFieldsHistory_slider');
            var elements = $('.OphInVisualfields_Episode_VisualFieldsHistory_element');
            var max = elementIds.length - 1;
            var throttle = false;
            var timer = 0;
            var diff = -1;

            function showElement(elementId) {
                $('#OphInVisualfields_Episode_VisualFieldsHistory_element_' + elementId).show();
            }

            function updateElements(val) {
                    elements.hide();
                    showElement(elementIds[val]);
            }

            // This is a POC, written in haste.
            // We throttle the execution of this handler because trackpads (and possibly
            // other touch devices) will emit the mousewheel event continuesly while scrolling.
            function onMouseWheel(e, delta) {

                e.preventDefault();

                if (throttle) return;

                // This helps to prevent the slider from jittering from a sudden change of
                // direction.
                if (delta > 0) {
                    if (diff < 0) {
                        diff = delta;
                        return;
                    }
                }
                if (delta < 0) {
                    if (diff > 0) {
                        diff = delta;
                        return;
                    }
                }

                diff = delta;

                var sliderVal = slider.slider('value');

                // Left or right scrolling?
                if (delta >= 0 && sliderVal > 0) {
                    sliderVal--;
                } else if (delta < 0 && sliderVal <= max) {
                    sliderVal++;
                }

                slider.slider('value', sliderVal);

                throttle = true;
                clearTimeout(timer);

                timer = setTimeout(function() {
                    throttle = false;
                }, 160);
            }

            slider.slider({
                'min': 0,
                'max': max,
                'value': max,
                'change': function (e, ui) {
                    updateElements(ui.value);
                },
                'slide': function(e, ui) {
                    updateElements(ui.value);
                }
            });

            sliderContainer.on('mousewheel', onMouseWheel);
            showElement(elementIds[elementIds.length - 1]);
        });
    </script>
<?php else: ?>
    <div class="data-value">No visual field images recorded for this patient.</div>
<?php endif ?>
