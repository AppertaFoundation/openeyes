<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */


namespace OEModule\Internalreferral\components;


class WinDipIntegration extends \CComponent
{
	protected $yii;
	protected $required_params =  array(
		'launch_uri',
		'application_id',
		'hashing_function'
	);
	protected $launch_uri;
	protected $application_id;
	public $hashing_function;

	public function __construct(\CApplication $yii = null, $params = array())
	{
		if (is_null($yii)) {
			$yii = \Yii::app();
		}

		$this->yii = $yii;

		\OELog::log("inside:" . print_r($params, true));
		foreach ($this->required_params as $p) {
			if (!isset($params[$p]))
				throw new \Exception("Missing required parameter {$p}");

			$this->$p = $params[$p];
		}
	}

	/**
	 * Convenience wrapper to allow template rendering.
	 *
	 * @param $view
	 * @param array $parameters
	 * @return mixed
	 */
	protected function renderPartial($view, $parameters = array())
	{
		return $this->yii->controller->renderPartial($view, $parameters, true);
	}

	protected function constructRequestData(\Event $event, \DateTime $when, $message_id)
	{
		//TODO: better way of handling mysql date to datetime
		$event_date = \DateTime::createFromFormat('Y-m-d H:i:s', $event->event_date);

		$indexes = array();
		$indexes[] = array(
			'id'=>'hosnum',
			'value'=> $event->episode->patient->hos_num
		);

		$user = \User::model()->findByPk(\Yii::app()->user->id);

		return array(
			'timestamp' => $when->format('Y-m-d H:i:s'),
			'message_id' => $message_id,
			'application_id' => $this->application_id,
			'username' => $user->username,
			'user_displayname' => $user->getReversedFullName(),
			'event_id' => $event->id,
			// TODO: make this dynamic
			'windip_type_id' => 1,
			'event_date' => $event_date->format('Y-m-d'),
			'event_time' => $event_date->format('H:i:s'),
			'additional_indexes' => $indexes
		);
	}

	protected function generateMessageId()
	{
		return \Helper::generateUuid();
	}

	public function generateXmlRequest(\Event $event)
	{
		$when = new \DateTime();
		$message_id = $this->generateMessageId();

		$data = $this->constructRequestData($event, $when, $message_id);


		$closure = $this->hashing_function;
		$authentication_hash = call_user_func($closure, $data, 'Internalreferral.views.windipintegration.request_xml');
		$data['authentication_hash'] = $authentication_hash;

		return $this->renderPartial('Internalreferral.views.windipintegration.request_xml', $data, true);
	}
}