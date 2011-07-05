<?php
Yii::import('zii.widgets.CBreadcrumbs');

class MyBreadcrumbs extends CBreadcrumbs
{
	public $prefixText='';
	
	public function run()
	{
		if (empty($this->links))
			return;

		echo CHtml::openTag($this->tagName,$this->htmlOptions)."\n";
		if (!empty($this->prefixText)) {
			echo $this->prefixText;
		}
		$links=array();
		if ($this->homeLink === null) {
			$links[] = CHtml::link(Yii::t('zii','Home'),Yii::app()->homeUrl);
		} else if($this->homeLink !== false) {
			$links[] = $this->homeLink;
		}
		foreach($this->links as $label=>$url)
		{
			if (is_string($label) || is_array($url)) {
				$links[]=CHtml::link($this->encodeLabel ? CHtml::encode($label) : $label, $url);
			} else {
				$links[]='<span>'.($this->encodeLabel ? CHtml::encode($url) : $url).'</span>';
			}
		}
		echo implode($this->separator,$links);
		echo CHtml::closeTag($this->tagName);
	}
}