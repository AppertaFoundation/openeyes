<div class="box admin">
	<h2>Menu</h2>
	<ul class="navigation admin">
		<?php foreach (array(
			'Pedigrees' => Yii::app()->createUrl('/Genetics/default/pedigrees'),
			'Inheritance' => Yii::app()->createUrl('/Genetics/default/inheritance'),
			'Genes' => Yii::app()->createUrl('/Genetics/default/genes'),
		) as $title => $uri) {?>
			<li<?php if (Yii::app()->getController()->action->id == $uri) {?> class="selected"<?php }?>>
				<?php if (Yii::app()->getController()->action->id == $uri) {?>
					<?php echo CHtml::link($title,array($uri),array('class' => 'selected'))?>
				<?php } else {?>
					<?php echo CHtml::link($title,array($uri))?>
				<?php }?>
			</li>
		<?php }?>
	</ul>
</div>
