		<h3>Describe your event type:</h3>
		<label>Specialty:</label>
		<?php echo CHtml::dropDownList('Specialty[id]',empty($_REQUEST) ? Specialty::model()->find('code=?',array(130))->id : @$_REQUEST['Specialty']['id'], CHtml::listData(Specialty::model()->findAll(array('order' => 'name')), 'id', 'name'))?><br />
		<label>Event group:</label>
		<?php echo CHtml::dropDownList('EventGroup[id]', empty($_REQUEST) ? EventGroup::model()->find('code=?',array('Mi'))->id : @$_REQUEST['EventGroup']['id'], CHtml::listData(EventGroup::model()->findAll(array('order' => 'name')), 'id', 'name'))?><br />
		<label>Name of event type:</label>
		<input type="text" name="EventTypeModuleCode[moduleSuffix]" value="<?php echo @$_POST['EventTypeModuleCode']['moduleSuffix']?>" size="65" id="moduleSuffix" /><br/>
		<?php if (isset($this->form_errors['EventTypeModuleCode[moduleSuffix]'])) {?>
			<span style="color: #f00; margin-bottom: 10px; display: inline-block;"><?php echo $this->form_errors['EventTypeModuleCode[moduleSuffix]']?></span>
		<?php }?>

		<label>Event type short name:</label>
		<input type="text" name="EventTypeModuleCode[moduleShortSuffix]" value="<?php echo @$_POST['EventTypeModuleCode']['moduleShortSuffix']?>" size="65" id="moduleShortSuffix" /><br/>

		<?php if (isset($this->form_errors['EventTypeModuleCode[moduleShortSuffix]'])) {?>
			<span style="color: #f00; margin-bottom: 10px; display: inline-block;"><?php echo $this->form_errors['EventTypeModuleCode[moduleShortSuffix]']?></span>
		<?php }?>

		<h3>Describe your element types:</h3>

		<div id="elementsGenerateNew">
			<?php foreach ($_POST as $key => $value) {
				if (preg_match('/^elementName([0-9]+)$/',$key,$m)) {
					echo $this->renderPartial('element',array('element_num'=>$m[1]));
				}
			}
			?>
		</div>

		<input type="submit" class="add_element" name="add" value="add element" /><br />
		<br/>

		<div class="tooltip">
			The name should only contain word characters and spaces.	The generated module class will be named based on the specialty, event group, and name of the event type.  EG: 'Ophthalmology', 'Treatment', and 'Operation note' will take the short codes for the specialty and event group to create <code>OphTrOperationnote</code>.
		</div>
