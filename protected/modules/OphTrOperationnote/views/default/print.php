<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 *  You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 *
 * @var DefaultController $this
 */

?>

<?php $this->renderPartial('//print/patient_overview') ?>

<!-- Operation metadata -->
<?php $this->renderPartial('print_operation_metadata'); ?>

<!-- Operation details -->
<?php $this->renderPartial('print_operation_details'); ?>

<!-- Anaesthetic Details -->
<?php
    $anaesthetic_element = Element_OphTrOperationnote_Anaesthetic::model()->find('event_id = ?', array($this->event->id));
    $this->renderElement($anaesthetic_element, 'print', false);
?>

<!-- Per-operative drugs -->
<?php
    $postdrugs_element = Element_OphTrOperationnote_PostOpDrugs::model()->find('event_id = ?', array($this->event->id));
    $this->renderPartial('view_Element_OphTrOperationnote_PostOpDrugs', array(
            'element' => $postdrugs_element,
            'is_print_view' => true
    ));
    ?>

<!-- Metadata -->
<?php
    $this->renderPartial('//print/event_metadata', array(
        'hide_modified' => @$hide_modified,
    ));
    ?>
