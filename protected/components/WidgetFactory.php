<?php

class WidgetFactory extends CWidgetFactory
{
	/*
	 * We're overridding this method so we can set the jquery ui paths to point to
	 * the components directory for all CJui* widgets.
	 */
	public function createWidget($owner,$className,$properties=array())
	{
		$widgetName=Yii::import($className);
		if (strpos($widgetName, 'CJui') === 0) {
			$assetsPublishedPath = Yii::app()->assetManager->getPublishedPathOfAlias('application.assets.components');
			$properties = CMap::mergeArray(array(
				'scriptUrl' => $assetsPublishedPath,
				'scriptFile' => 'jquery-ui/ui/minified/jquery-ui.min.js',
				'themeUrl' => $assetsPublishedPath.'/jquery-ui/themes'
			), $properties);
		}
		return parent::createWidget($owner,$className,$properties);
	}
}