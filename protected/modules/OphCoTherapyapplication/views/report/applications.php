<h2>Therapy application report</h2>
<div class="row divider">
  <form>
    <table class="standard cols-full">
      <tbody>
      <?php $this->renderPartial('//report/_institution_table_row', ['field_name' => "institution_id"]);?>
      <tr>
        <td><?=\CHtml::label('Consultant', 'firm_id') ?></td>
        <td>
            <?php if (Yii::app()->getAuthManager()->checkAccess('Report', Yii::app()->user->id)) : ?>
                <?=\CHtml::dropDownList('firm_id', null, $firms, array('empty' => 'All consultants')) ?>
            <?php else : ?>
                <?php
                $firm = Firm::model()->findByAttributes(array('consultant_id' => Yii::app()->user->id));

                if ($firm) {
                    echo CHtml::dropDownList(
                        null,
                        '',
                        array($firm->id => $firm->name),
                        array(
                            'disabled' => 'disabled',
                            'readonly' => 'readonly',
                            'style' => 'background-color:#D3D3D3;',
                        ) //for some reason the chrome doesn't gray out
                    );
                    echo CHtml::hiddenField('consultant_id', $firm->id);
                } else {
                    echo CHtml::dropDownList(
                        null,
                        '',
                        array(),
                        array(
                            'disabled' => 'disabled',
                            'readonly' => 'readonly',
                            'style' => 'background-color:#D3D3D3;',
                            'empty' => '- select -',
                        )
                    ); //for some reason the chrome doesn't gray out
                }
                ?>
            <?php endif ?>
        </td>
      </tr>
      <tr>
        <td><?=\CHtml::label('Date From', 'date_from') ?></td>
        <td>
          <input id="date_from"
                 placeholder="dd-mm-yyyy"
                 class="start-date"
                 name="date_from"
                 autocomplete="off"
                 value= <?= date('d-m-Y'); ?>
          >

        </td>
        <td><?=\CHtml::label('Date To', 'date_to') ?></td>
        <td>
          <input id="date_to"
                 placeholder="dd-mm-yyyy"
                 class="end-date"
                 name="date_to"
                 autocomplete="off"
                 value= <?= date('d-m-Y'); ?>
          >
        </td>
      </tr>
      <tr>
        <td>Submission Information</td>
        <td>
            <?=\CHtml::label('Submission Date', 'submission') ?>
            <?=\CHtml::checkBox('submission'); ?>
        </td>
      </tr>
      <tr>
        <td>Injection Information</td>
        <td>
            <?=\CHtml::label('First Injection', 'first_injection') ?>
            <?=\CHtml::checkBox('first_injection'); ?>
        </td>
        <td>
            <?=\CHtml::label('Last Injection', 'last_injection') ?>
            <?=\CHtml::checkBox('last_injection'); ?>
        </td>
      </tr>
      </tbody>
    </table>
    <div class="row flex-layout flex-right">
        <?php
        $htmlOptions = array('class' => 'button green hint');
        if (!$this->canUseTherapyReport()) {
            $htmlOptions = array(
                'disabled' => 'disabled',
                'readonly' => 'readonly',
            );
        } ?>
        <?=\CHtml::submitButton('Generate Report', $htmlOptions) ?>
      &nbsp; &nbsp;
    </div>
  </form>
</div>
