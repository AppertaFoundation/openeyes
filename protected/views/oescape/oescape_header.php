<?php
$navIconUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue'), true) . '/dist/svg/oe-nav-icons.svg';
?>

<nav class="oescape-header flex-layout">
    <div>
        <!-- resize left chart area and Left & Right Eye buttons -->
        <!-- note: no whitespace (gaps) in HTML, SVG is styled by CSS -->
        <?php

        $selected_size = array_key_exists('oescape_chart_size', $_SESSION) ? $_SESSION['oescape_chart_size'] : 'medium';
        $area_sizes = [
            ['name' => 'small', 'width' => '8'],
            ['name' => 'medium', 'width' => '16'],
            ['name' => 'large', 'width' => '24'],
            ['name' => 'full', 'width' => '30'],
        ];
        foreach ($area_sizes as $size) :
            ?>
            <button class="js-oes-area-resize <?= $selected_size == $size['name'] ? 'selected' : '' ?>"
                    data-area="<?= $size['name'] ?>">
                <svg class="svg-size-icon" width="38" height="19" viewBox="0 0 38 19">
                    <title>oescape-chart-size-small</title>
                    <rect x="1.5" y="1.5" width="34" height="15"></rect>
                    <rect x="3.5" y="3.5" width="<?= $size['width'] ?>" height="11"></rect>
                </svg>
            </button>
        <?php endforeach; ?>
        <button class="js-oes-eyeside selected" data-side="right">Right</button>
        <button class="js-oes-eyeside" data-side="left">Left</button>
        <button class="js-oes-eyeside" data-side="both">Both</button>

        <?php if ($subspecialty->ref_spec == 'GL') : ?>
        <div class="data-awareness">
            <div class="group">
                <span class="type">CCT:</span>
                <span class="data">R:<?= isset($header_data['CCT']['right']) ? $header_data['CCT']['right'] : ' NR'; ?></span>
                <span class="data">L:<?= isset($header_data['CCT']['left']) ? $header_data['CCT']['left'] : ' NR'; ?></span>
                <span class="date"><?= isset($header_data['CCT']['date']) ? $header_data['CCT']['date'] : ''; ?></span>
            </div>
            <div class="group">
                <span class="type">IOP (Base):</span>
                <span class="data">R:<?= isset($header_data['IOP']['right']) ? $header_data['IOP']['right'] : ' NR'; ?></span>
                <span class="data">L:<?= isset($header_data['IOP']['left']) ? $header_data['IOP']['left'] : ' NR'; ?></span>
                <span class="date"><?= isset($header_data['IOP']['date']) ? $header_data['IOP']['date'] : ''; ?></span>
            </div>
            <div class="group">
                <span class="type">IOP (Max):</span>
                <span class="data">R:<?= isset($header_data['IOP_MAX']['right']) ? $header_data['IOP_MAX']['right'] : ' NR'; ?></span>
                <span class="data">L:<?= isset($header_data['IOP_MAX']['left']) ? $header_data['IOP_MAX']['left'] : ' NR'; ?></span>
            </div>
        </div>

        <?php endif; ?>
    </div>
    <div class="nav-title">
        <div class="title"><?= $subspecialty->name ?></div>
        <ul class="oescape-icon-btns" style="font-size: 0;">
            <?php
            $subspecialties = Subspecialty::model()->findAllByAttributes(array(
                'name' => array(
                    'Cataract',
                    'Glaucoma',
                    'Medical Retina',
                    'General Ophthalmology',
                ),
            ));
            ?>
            <?php foreach ($subspecialties as $subspecialty) : ?>
                <li class="icon-btn"
                    data-subspecialty-id="<?= $subspecialty->id ?>">
                    <a class="active"
                       href="<?= Yii::app()->createUrl(
                           '/patient/oescape/',
                           array(
                               'subspecialty_id' => $subspecialty->id,
                               'patient_id' => $this->patient->id)
                             ) ?>"
                    >
                        <?= $subspecialty->ref_spec ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <li class="icon-btn">
                <a href="<?= Yii::app()->createUrl('/patient/lightningViewer', array('id' => $this->patient->id)) ?>"
                   class="lightning-viewer-icon active">
                    <svg viewBox="0 0 30 30" width="15" height="15">
                        <use xlink:href="<?= $navIconUrl ?>#lightning-viewer-icon"></use>
                    </svg>
                </a>
            </li>
        </ul>
        <!-- icon-btns -->
    </div>

    <!-- exit oes and go back to previous page -->
    <div id="js-exit-oescape"
         data-link="<?php $core_api = new CoreAPI();
            echo $core_api->generatePatientLandingPageLink($this->patient) ?>">
        <i class="oe-i remove-circle"></i>
    </div>
</nav>
