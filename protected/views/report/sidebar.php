<div class="box reports">
	<h2>Core</h2>
	<ul class="navigation reports">
		<?php foreach (array(
            'Diagnoses' => '/report/diagnoses',
        ) as $title => $uri) {?>
			<li<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/report\//', '', $uri)) {?> class="selected"<?php }?>>
				<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/report\//', '', $uri)) {?>
					<?php echo CHtml::link($title, array($uri), array('class' => 'selected'))?>
				<?php } else {?>
					<?php echo CHtml::link($title, array($uri))?>
				<?php }?>
			</li>
		<?php }?>
	</ul>
</div>
<?php foreach (ModuleReports::getAll() as $module => $items) {?>
	<div class="reports box">
		<h2><?php echo $module?></h2>
		<ul class="navigation reports">
			<?php foreach ($items as $item => $uri) {
                $e = explode('/', $uri);
                $action = array_pop($e)?>
				<li<?php if (Yii::app()->getController()->action->id == $action) {?> class="selected"<?php }?>>
					<?php if (Yii::app()->getController()->action->id == $action) {?>
						<?php echo CHtml::link($item, array($uri), array('class' => 'selected'))?>
					<?php } else {?>
						<?php echo CHtml::link($item, array($uri))?>
					<?php }?>
				</li>
			<?php }?>
		</ul>
	</div>
<?php }?>
