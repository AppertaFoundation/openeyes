<?php

class EyeDrawService
{
	/**
	 * Returns HTML for an EyeDraw field
	 *
	 * @param object  $model		the element model
	 * @param integer $side			usually 'left' or 'right' - the eye we're rendering a field for
	 * @param boolean $writeable		whether the field should be writeable
	 *
	 * @return array
	 */
	public $identifiers = Array();
	public $active = '';

	public function activeEyeDrawField($model, $side, $writeable = true)
	{
		global $active;
		global $identifiers;
		$active = true;
		$identifier = get_class($model) . '_' . $side;
		$identifiers[]= $identifier;

		$html = '';

		// create hidden form field to contain the EyeDraw data string
		// $html .= CHtml::activeHiddenField($model, $side);
		// eyedraw javascript
		$html .= CController::renderPartial('/eyedraw/js',array('model' => $model, 'side' => $side, 'writeable' => $writeable),true);

		// eyedraw html
		$html .= CController::renderPartial('/eyedraw/ed',array('model' => $model, 'side' => $side, 'writeable' => $writeable),true);

		return $html;
	}
	public function activeEyeDrawInit()
	{
		$init = ''; $submit = '';
		global $identifiers;

		if (is_array($identifiers)) {
			foreach ($identifiers as $identifier) {
				$init .= 'init' . $identifier . '();';
				$submit .= 'submit' . $identifier . '();';
			}
			$return = CController::renderPartial('/eyedraw/init',array('init' => $init, 'submit' => $submit),true);
			return $return;
		} else {
			return '<script type="text/javascript">function eyedraw_init() {return true;}</script>';
		}
	}
	public function getActive()
	{
		global $active;
		return $active;
	}
}
