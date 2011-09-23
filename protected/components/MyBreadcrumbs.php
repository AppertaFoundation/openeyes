<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

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
