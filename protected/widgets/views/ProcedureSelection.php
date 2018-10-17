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
<div class="flex-layout procedure-selection eventDetail<?= $last ? 'eventDetailLast':''?>"
     id="typeProcedure"
     style="<?= $hidden ? 'display: none;':''?>"
>
    <?php if ($label && sizeof($label)) { ?>
  <div class="cols-2">
    <label for="select_procedure_id_<?php echo $identifier; ?>">
        <?php echo $label ?>
    </label>
  </div>
      <?php }?>
    <?php $totalDuration = 0; ?>

        <table class="cols-10" id="procedureList_<?php echo $identifier ?>" style="<?= empty($selected_procedures) ? 'visibility: hidden;' : '' ?>">
          <thead>
          <tr>
            <th>Procedure</th>
              <?php if ($durations) { ?>
                <th>Duration</th>
              <?php } ?>
            <th></th>
          </tr>
          </thead>
          <tbody class="body">
          <?php
          if (!empty($selected_procedures)) {
              foreach ($selected_procedures as $procedure) {
                  $totalDuration += $procedure['default_duration']; ?>
                <tr class="item">
                  <td class="procedure large-text">
                  <span class="field"><?= CHtml::hiddenField('Procedures_' . $identifier . '[]',
                          $procedure['id']); ?></span>
                    <span class="value"><?= $procedure['term']; ?></span>
                  </td>
                    <?php if ($durations) { ?>
                      <td class="duration">
                          <?php echo $procedure['default_duration'] ?> mins
                      </td>
                    <?php } ?>
                  <td>
                  <span class="removeProcedure">
                      <i class="oe-i trash"></i>
                  </span>
                  </td>
                </tr>
              <?php }
              if (isset($_POST[$class]['total_duration_' . $identifier])) {
                  $total_duration = $_POST[$class]['total_duration_' . $identifier];
              }
          } ?>
          </tbody>

          <?php if ($durations) { ?>
              <tfoot>
              <tr>
                <td></td>
                <td>
                    <span id="projected_duration_<?php echo $identifier ?>">
                        <?=\CHtml::encode($totalDuration) ?> mins
                    </span>
                    <span class="fade">(calculated)</span>
                </td>
                <td class="align-left">
                  <input
                      type="text"
                      value="<?php echo $total_duration ?>"
                      id="<?php echo $class ?>_total_duration_<?php echo $identifier ?>"
                      name="<?php echo $class ?>[total_duration_<?php echo $identifier ?>]"
                      style="width:60px"
                  />
                    <span class="fade">mins (estimated)</span>
                </td>
              </tr>
              </tfoot>

          <?php } ?>
        </table>

    <div class="add-data-actions flex-item-bottom">
      <button class="button hint green add-entry" type="button" id="add-procedure-list-btn-<?= $identifier ?>">
        <i class="oe-i plus pro-theme"></i>
      </button>
    </div>

</div>

  <script type="text/javascript">
    // Note: Removed_stack is probably not the best name for this. Selected procedures is more accurate.
    // It is used to suppress procedures from the add a procedure inputs
    var removed_stack_<?php echo $identifier?> = [<?php echo implode(',', $removed_stack); ?>];

    function updateTotalDuration(identifier) {

      // update total duration
      var totalDuration = 0;
      $('#procedureList_' + identifier).find('.item').map(function () {
        $(this).find('.duration').map(function () {
          totalDuration += parseInt($(this).html().match(/[0-9]+/));
        });
      });
      if ($('input[name=\"<?php echo $class?>[eye_id]\"]:checked').val() == 3) {
        $('#projected_duration_' + identifier).text(totalDuration + ' * 2');
        totalDuration *= 2;
      }
      $('#projected_duration_' + identifier).text(totalDuration + " mins");
      $('#<?php echo $class?>_total_duration_' + identifier).val(totalDuration);
    }

    $('.removeProcedure').die('click').live('click', function () {
      let $table = $(this).closest("[id^='procedureList_']");
      let identifier;
       if($table) {
           identifier = $table.attr('id').match(/^procedureList_(.*?)$/);
           removeProcedure($(this).closest('tr'), identifier[1]);
        }

      return false;
    });

    function removeProcedure($table_row, identifier) {
        var length = $table_row.siblings('tr').length;
        var procedure_id = $table_row.find('input[type="hidden"]:first').val();

        $table_row.remove();

        <?php if ($durations) {?>
      updateTotalDuration(identifier);
        <?php }?>

      if (length < 1) {
        $('#procedureList_' + identifier).css('visibility' , 'hidden');
          <?php if ($durations) {?>
        $('#procedureList_' + identifier).find('.durations').hide();
          <?php }?>
      }

      if (typeof(window.callbackRemoveProcedure) == 'function') {
        callbackRemoveProcedure(procedure_id);
      }

      // Remove removed procedure from the removed_stack
      var stack = [];
      var popped = null;
      $.each(window["removed_stack_" + identifier], function (key, value) {
        if (value["id"] != procedure_id) {
          stack.push(value);
        } else {
          popped = value;
        }
      });
      window["removed_stack_" + identifier] = stack;

      // Refresh the current procedure select box in case the removed procedure came from there
      if ($('#subsection_id_' + identifier).length) {
        // Procedures are in subsections, so fetch a clean list via ajax (easier than trying to work out if it's the right list)
        updateProcedureSelect(identifier);
      } else if (popped) {
        // No subsections, so we should be safe to just push it back into the list
        $('#select_procedure_id_' + identifier).append('<option value="' + popped["id"] + '">' + popped["name"] + '</option>').removeAttr('disabled');
        sort_selectbox($('#select_procedure_id_' + identifier));
      }

      return false;
    }

    function selectSort(a, b) {
      if (a.innerHTML == rootItem) {
        return -1;
      } else if (b.innerHTML == rootItem) {
        return 1;
      }
      return (a.innerHTML > b.innerHTML) ? 1 : -1;
    };

    $('select[id^=subsection_id]').unbind('change').change(function () {
      var m = $(this).attr('id').match(/^subsection_id_(.*)$/);
      updateProcedureSelect(m[1]);
    });

    function initialiseProcedureAdder() {
        $('.add-options[data-id="subsections"]').on('click' , 'li' , function(){
                updateProcedureDialog($(this).data('id'));
        });
        if ($('.add-options[data-id="subsections"] > li').length === 0) {
            $('.add-options[data-id="subsections"]').hide();
        }

        if ($('.add-options[data-id="select"] > li').length === 0) {
            $('.add-options[data-id="select"]').hide();
        }
    }

    function updateProcedureDialog(subsection) {
        if (subsection !== '') {
            $.ajax({
                'url': '<?php echo Yii::app()->createUrl('procedure/list')?>',
                'type': 'POST',
                'data': {'subsection': subsection, 'dialog': true, 'YII_CSRF_TOKEN': YII_CSRF_TOKEN},
                'success': function (data) {
                    $('.add-options[data-id="select"]').each(function () {
                        $(this).html(data);
                        $(this).show();
                    });
                }
            });
        }
    }

    function updateProcedureSelect(identifier) {
        let subsection_field = $('select[id=subsection_id_' + identifier + ']');
        let subsection = subsection_field.val();
        if (subsection !== '') {
            $.ajax({
                'url': '<?php echo Yii::app()->createUrl('procedure/list')?>',
                'type': 'POST',
                'data': {'subsection': subsection, 'YII_CSRF_TOKEN': YII_CSRF_TOKEN},
                'success': function (data) {
                    $('select[name=select_procedure_id_' + identifier + ']').attr('disabled', false);
                    $('select[name=select_procedure_id_' + identifier + ']').html(data);

                    // remove any items in the removed_stack
                    $('select[name=select_procedure_id_' + identifier + '] option').map(function () {
                        var obj = $(this);

                        $.each(window["removed_stack_" + identifier], function (key, value) {
                            if (value["id"] == obj.val()) {
                                obj.remove();
                            }
                        });
                    });

                    $('select[name=select_procedure_id_' + identifier + ']').parent().css('visibility' , 'visible');
                }
            });
        } else {
            $('select[name=select_procedure_id_' + identifier + ']').parent().hide();
        }
    }

    $('select[id^="select_procedure_id"]').unbind('change').change(function () {
      var m = $(this).attr('id').match(/^select_procedure_id_(.*)$/);
      var identifier = m[1];
      var select = $(this);
      var procedure = $('select[name=select_procedure_id_' + m[1] + '] option:selected').text();
      if (procedure != 'Select a commonly used procedure') {

        if (typeof(window.callbackVerifyAddProcedure) == 'function') {
          window.callbackVerifyAddProcedure(procedure, "<?php echo $durations ? '1' : '0';?>", function (result) {
            if (result != true) {
              select.val('');
              return;
            }

            if (typeof(window.callbackAddProcedure) == 'function') {
              var procedure_id = $('select[name=select_procedure_id_' + identifier + '] option:selected').val();
              callbackAddProcedure(procedure_id);
            }

            ProcedureSelectionSelectByName(procedure, false, m[1]);
          });
        } else {
          if (typeof(window.callbackAddProcedure) == 'function') {
            var procedure_id = $('select[name=select_procedure_id_' + identifier + '] option:selected').val();
            callbackAddProcedure(procedure_id);
          }

          ProcedureSelectionSelectByName(procedure, false, m[1]);
        }
      }
      return false;
    });

    <?php if ($durations): ?>
    $(document).ready(function () {
      if ($('input[name="<?php echo $class?>[eye_id]"]:checked').val() == 3) {
        $('#projected_duration_<?php echo $identifier?>').html((parseInt($('#projected_duration_<?php echo $identifier?>').html().match(/[0-9]+/)) * 2) + " mins");
      }
      $('input[name="<?php echo $class?>[eye_id]"]').click(function () {
        updateTotalDuration('<?php echo $identifier?>');
      });
    });
    <?php endif ?>

    function ProcedureSelectionSelectByName(name, callback, identifier) {
      $.ajax({
        'url': baseUrl + '/procedure/details?durations=<?php echo $durations ? '1' : '0'?>&identifier=' + identifier,
        'type': 'GET',
        'data': {'name': name},
        'success': function (data) {
          var enableDurations = <?php echo $durations ? 'true' : 'false'?>;

          // append selection onto procedure list
          $('#procedureList_' + identifier).find('.body').append(data);
          $('#procedureList_' + identifier).css('visibility' , 'visible');

          if (enableDurations) {
            updateTotalDuration(identifier);
            $('#procedureList_' + identifier).find('.durations').show();
          }

          // clear out text field
          $('#autocomplete_procedure_id_' + identifier).val('');

          // remove selection from the filter box
          if ($('#select_procedure_id_' + identifier).children().length > 0) {
            m = data.match(/<span class="value">(.*?)<\/span>/);

            $('#select_procedure_id_' + identifier).children().each(function () {
              if ($(this).text() == m[1]) {
                var id = $(this).val();
                var name = $(this).text();

                window["removed_stack_" + identifier].push({name: name, id: id});

                $(this).remove();
              }
            });
          }

          if (callback && typeof(window.callbackAddProcedure) == 'function') {
            m = data.match(/<input type=\"hidden\" value=\"([0-9]+)\"/);
            var procedure_id = m[1];
            callbackAddProcedure(procedure_id);
          }
        }
      });
  }

  $(document).ready(function () {
    new OpenEyes.UI.AdderDialog({
      id:'procedure_popup_<?= $identifier ?:''; ?>',
      openButton: $('#add-procedure-list-btn-<?= $identifier ?>'),
      itemSets: [
          new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
              array_map(function ($key, $item) {
                  return ['label' => $item, 'id' => $key];
              }, array_keys($subsections), $subsections))?>, {'id':'subsections'}),
          new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
          array_map(function ($key, $item) {
              return ['label' =>$item, 'id' => $key];
          },array_keys($procedures), $procedures)
      ) ?>, {'id':'select','multiSelect': true})
      ],

      onReturn: function (adderDialog, selectedItems) {
        var $selector = $('#select_procedure_id_<?php echo $identifier; ?>');
        for (i in selectedItems) {
            ProcedureSelectionSelectByName(selectedItems[i]['label'], true, '<?= $identifier ?>');
        }
        return true;
      },
    searchOptions: {
        searchSource: '/procedure/autocomplete',
    }
    });

    initialiseProcedureAdder();
  });
</script>

