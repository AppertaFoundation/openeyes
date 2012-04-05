<?php
/**
 * Contains a Yii widget for EyeDraw
 * @package EyeDraw
 * @author Bill Aylward <bill.aylward@openeyes.org>
 * @version 0.9
 * @link http://www.openeyes.org.uk/
 * @copyright Copyright (c) 2012 OpenEyes Foundation
 * @license http://www.yiiframework.com/license/
 * 
 * This file is part of OpenEyes.
 * 
 * OpenEyes is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * OpenEyes is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with OpenEyes.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once(dirname(__FILE__)."/OEEyeDrawWidget.php");

class OEEyeDrawWidgetCataract extends OEEyeDrawWidget {
	public $doodleToolBarArray = array('PhakoIncision','SidePort','IrisHook','PCIOL','ACIOL','PI','MattressSuture');

	public $onLoadedCommandArray = array(
		array('addDoodle', array('AntSeg')),
		array('addDoodle', array('PhakoIncision')),
		array('addDoodle', array('SidePort')),
		array('addDoodle', array('PCIOL')),
		array('deselectDoodles', array()),
	);

	public $identifier = 'Cataract';

	public function init() {
		if ($this->mode == 'view') {
			$this->doodleToolBarArray = array();
			$this->onLoadedCommandArray = array(array('addDoodle', array('AntSeg')),array('deselectDoodles', array()));
		}

		parent::init();
	}
}
