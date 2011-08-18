<?php

Yii::app()->clientScript->registerCssFile(
	'/css/elements/ElementLetterOut/1.css',
	'screen, projection'
);

Yii::app()->clientScript->registerCssFile(
        '/css/elements/ElementLetterOut/1_print.css',
        'print'
);

?>
<div id="ElementLetterOut_layout">
<img src="/img/elements/ElementLetterOut/Letterhead.png" alt="Moorfields logo" border="0" />
<?php

if ($siteId = Yii::app()->request->cookies['site_id']->value) {
        $site = Site::model()->findByPk($siteId);

        if (isset($site)) {
?>
        <div class="ElementLetterOut_siteDetails">
                <?php

		echo $site->name . "<br />\n";
		echo $site->address1 . "<br />\n";
		if (isset($site->address2)) {
			echo $site->address2 . "<br />\n";
		}
                if (isset($site->address3)) {
                        echo $site->address3 . "<br />\n";
                }
		echo $site->postcode . "<br />\n";
		echo "<br />\n";
		echo "Tel: " . $site->telephone . "<br />\n";
		echo "Fax: " . $site->fax . "<br />\n";
?>
        </div>
<?php
        }
}

?>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<p class="ElementLetterOut_to"><?php echo nl2br(CHtml::encode($data->to_address)); ?></p>

	<p class="ElementLetterOut_date"><?php echo CHtml::encode($data->date); ?></p>

	<p class="ElementLetterOut_dear"><?php echo CHtml::encode($data->dear); ?></p>

	<p class="ElementLetterOut_re"><?php echo CHtml::encode($data->re); ?></p>

	<p class="ElementLetterOut_text"><?php echo nl2br(CHtml::encode($data->value)); ?></p>

	<p><?php echo nl2br(CHtml::encode($data->from_address)) ?></p>

	<p class="ElementLetterOut_cc"><?php echo nl2br(CHtml::encode($data->cc)); ?></p>
</div>
