<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php if ($chart->hasData()) : ?>
  <div class="data-group">
    <div class="data-label column cols-9"></div>
    <div class="data-value column cols-3">
      <form action="#OphCiExamination_Episode_MedicalRetinalHistory">
        <label for="mr_history_va_unit_id">Visual Acuity unit</label>
          <?= CHtml::dropDownList('mr_history_va_unit_id', $va_unit->id, CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()->active()->findAll(), 'id', 'name'))?>
      </form>
    </div>
  </div>

  <div class="column cols-12">
    <div id="mr-history-chart" class="chart" style="width: 100%; height: 500px"></div>
  </div>

    <?= $chart->run(); ?>
  <script type="text/javascript">
    $(document).ready(function () {
      $('#mr_history_va_unit_id').change(function () { this.form.submit(); });

      var injections = <?= CJavaScript::encode($this->injections); ?>,
        width = 100,
        height = 20,
        offset = 10;

      var plot = $('#mr-history-chart').data('plot'),
        series = plot.getData(),
        xaxis = plot.getAxes().xaxis;

      var colors = {};
      for (var i = 0; i < series.length; i++) {
        colors[series[i].label] = series[i].color;
      }

      var top = offset;
      for (var t in injections) {
        injections[t].top = top;
        top += (height + offset);
        if (top > 130) top = offset;
      }

        plot.hooks.draw.push(function (plot, ctx) {
          function drawInjectionLabel(ctx, inj, side, x) {
            var drug = inj[side],
              top = inj.top,
              left, l1, l2, text;
            switch (side) {
              case 'right':
                left = x - (width + offset);
                l1 = x - offset;
                l2 = x - offset/2;
                text = drug + " (R)";
                break;
              case 'left':
                left = x + offset;
                l1 = x + offset;
                l2 = x + offset/2;
                text = drug + " (L)";
            }

              ctx.save();

              ctx.fillStyle = "white";
            ctx.fillRect(left, top, width, height);
              ctx.strokeStyle = colors[drug];
            ctx.lineWidth = 2;
            ctx.strokeRect(left, top, width, height);

              ctx.beginPath();
            ctx.moveTo(l1, top + height/2);
            ctx.lineTo(l2, top + height/2);
            ctx.stroke();

              ctx.rect(left, top, width, height);
            ctx.clip();

              ctx.fillStyle = colors[drug];
            ctx.font = "12px sans-serif";
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            ctx.fillText(text, left + width/2, top + height/2);
              ctx.restore();
          }

          ctx.save();
          ctx.translate(plot.getPlotOffset().left, plot.getPlotOffset().top);
          ctx.rect(0, 0, plot.width(), plot.height());
          ctx.clip();

          for (var t in injections) {
            var x = xaxis.p2c(t);

            if (injections[t].right) {
              drawInjectionLabel(ctx, injections[t], 'right', x);
            }
            if (injections[t].left) {
              drawInjectionLabel(ctx, injections[t], 'left', x);
            }
          }
            ctx.restore();
        });
        plot.draw();
    });
  </script>
<?php else : ?>
    <div class="cols-12 column">
      <div class="data-value">(no data)</div>
    </div>
<?php endif; ?>