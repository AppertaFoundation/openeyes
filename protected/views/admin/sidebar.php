<div class="box admin">
	<div class="box_admin_header box_admin_header_all">
		<h2>Admin</h2>
	</div>
</div>
<?php foreach (Yii::app()->params['admin_structure'] as $adminBoxTitle => $adminBoxElements) { ?>
	<div class="box admin">
		<div class="box_admin_header">
			<h2><?php echo $adminBoxTitle ?></h2>
		</div>
		<div class="box_admin_elements">
			<ul class="navigation admin">
				<?php foreach ($adminBoxElements as $title => $uri) {
                    // need to check if function depends on a module
                    if (is_array($uri)) {
                        foreach ($uri as $moduleName => $adminUri) {
                            if (array_key_exists($moduleName, Yii::app()->modules)) {
                                $uri = $adminUri;
                            } else {
                                $uri = null;
                            }
                        }
                    }
                    if ($uri) { ?>
						<li<?php
                        $requestUriArray = explode('?', Yii::app()->getController()->request->requestUri);
                        $requestUri = $requestUriArray[0];
                        if ($requestUri == $uri) { ?> class="selected"<?php } ?>>
							<?php if ($requestUri == $uri) { ?>
								<script type="text/javascript">
									$(document).ready(function () {
											$('#<?php echo str_replace(' ', '_', $title) ?>').closest('.box_admin_elements').show();
										}
									);
								</script>
								<?php echo CHtml::link($title, array($uri),
                                    array('class' => 'selected', 'id' => str_replace(' ', '_', $title))); ?>
							<?php } else { ?>
								<?php echo CHtml::link($title, array($uri)) ?>
							<?php } ?>
						</li>
					<?php }
                } ?>
			</ul>
		</div>
	</div>
<?php } ?>

<?php foreach (ModuleAdmin::getAll() as $module => $items) {?>
	<div class="admin box">
		<div class="box_admin_header">
			<h2><?php echo $module ?></h2>
		</div>
		<div class="box_admin_elements">
			<ul class="navigation admin">
				<?php foreach ($items as $item => $uri) {
                    $e = explode('/', $uri);
                    $action = array_pop($e) ?>
					<li<?php if ($requestUri == $uri) { ?> class="selected"<?php } ?>>
						<?php if ($requestUri == $uri) { ?>
							<script type="text/javascript">
								$(document).ready(function () {
										$('#<?php echo str_replace(' ', '_', $title) ?>').closest('.box_admin_elements').show();
									}
								);
							</script>
							<?php echo CHtml::link($item, array($uri),
                                array('class' => 'selected', 'id' => str_replace(' ', '_', $title))) ?>
						<?php } else { ?>
							<?php echo CHtml::link($item, array($uri)) ?>
						<?php } ?>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
<?php }?>
