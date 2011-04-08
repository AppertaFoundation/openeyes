<?php

class EyeDrawService
{
	public $identifiers = Array();
	public $active = '';

	/**
	 * Returns HTML for an EyeDraw field
	 *
	 * @param object  $controller	the controller calling this
	 * @param object  $model		the element model
	 * @param integer $side			usually 'left' or 'right' - the eye we're rendering a field for
	 * @param boolean $writeable		whether the field should be writeable
	 *
	 * @return array
	 */
	public static function activeEyeDrawField($controller, $model, $side, $writeable = true)
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
		$html .= $controller->renderPartial('/eyedraw/js',array('model' => $model, 'side' => $side, 'writeable' => $writeable),true);

		// eyedraw html
		$html .= $controller->renderPartial('/eyedraw/ed',array('model' => $model, 'side' => $side, 'writeable' => $writeable),true);

		return $html;
	}

	/**
	 * Initialises EyeDraw.
	 *
	 * @param object $controller
	 */
	public static function activeEyeDrawInit($controller)
	{
		$init = ''; $submit = '';
		global $identifiers;

		if (is_array($identifiers)) {
			foreach ($identifiers as $identifier) {
				$init .= 'init' . $identifier . '();';
				$submit .= 'submit' . $identifier . '();';
			}

			$return = $controller->renderPartial('/eyedraw/init',array('init' => $init, 'submit' => $submit),true);
			return $return;
		} else {
			return '<script type="text/javascript">function eyedraw_init() {return true;}</script>';
		}
	}

	public static function getActive()
	{
		global $active;
		return $active;
	}
}
