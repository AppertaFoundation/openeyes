<?php
if (!Yii::app()->user->isGuest) {
	if (!empty(Yii::app()->session['user'])) {
		$user = Yii::app()->session['user'];
	} else {
		$user = User::model()->findByPk(Yii::app()->user->id);
		Yii::app()->session['user'] = $user;
	} ?>
	<div id="user_panel">
		<div class="clearfix">
			<div id="user_id">
				Hi <strong><?php echo $user->first_name . ' ' . $user->last_name; ?></strong>&nbsp;<a href="#" class="small">(not you?)</a>
			</div>

			<ul id="user_nav">
				<li><a href="/">Home</a></li>
				<li><a href="/theatre">Theatre Management</a></li>
				<li><a href="/site/logout" class="logout">Logout</a></li>
			</ul>
		</div>
		<?php
		if ($this->showForm) {?>
			<div id="user_firm">
				<?php
				$form=$this->beginWidget('CActiveForm', array(
					'id'=>'base-form',
					'enableAjaxValidation'=>false,
					'action' => Yii::app()->createUrl('site')
				));
				?>
				<label>Selected firm: </label>
				<?php
				echo CHtml::dropDownList('selected_firm_id', $this->selectedFirmId, $this->firms);
				$this->endWidget();
				?>
			</div>
		<?php }?>
	</div> <!-- #user_panel -->
<?php
}?>
