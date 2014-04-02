<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace Service;

class ValidationFailure extends ServiceException
{
	public $httpStatus = 422;

	protected $errors;

	/**
	 * @param string $message
	 * @param array $errors
	 */
	public function __construct($message, array $errors)
	{
		$this->errors = $errors;
		parent::__construct($message);
	}

	public function toFhirOutcome()
	{
		$issues = array();
		foreach ($this->errors as $attr => $errors) {
			foreach ($errors as $error) {
				$issues[] = new FhirOutcomeIssue(
					array(
						'severity' => \FhirValueSet::ISSUESEVERITY_ERROR,
						'type' => \FhirValueSet::ISSUETYPE_INVALID_VALUE,  // Ideally we'd also support INVALID_REQUIRED, but Yii doesn't make that easy for us
						'details' => $error,
					)
				);
			}
		}
		return new FhirOutcome(array('issues' => $issues));
	}
}
