<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en" rel="stylesheet">
    <link href="<?= Yii::app()->assetManager->createUrl('fonts/material-design/material-icons.css')?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= Yii::app()->assetManager->createUrl('components/material-design-lite/material.min.css')?>">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="A front-end template that helps you build fast, modern mobile web apps.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cataract WHO summary</title>

    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
    <meta name="msapplication-TileColor" content="#3372DF">

</head>

<body>
<div>
    <header class="mdl-layout__header mdl-color--grey-100 mdl-color-text--grey-600">
        <div class="mdl-layout__header-row">
            <span class="mdl-layout-title">
                Cataract WHO summary
            </span>
        </div>
        <div class="mdl-layout-spacer"></div>
        <div>
            <?php if($data->booking->isEditable()):?>
                <a class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored"
                   id="refresh-button"
                   href="/OphTrOperationbooking/whiteboard/reload/<?=$data->event_id?>"
                   title="Valid as at <?=date_create_from_format('Y-m-d H:i:s', $data->last_modified_date)->format('j M Y H:i:s')?>">
                    Refresh
                    <i class="material-icons right">refresh</i>
                </a>
            <?php endif;?>
        </div>
    <header>
</div>
<div class="mdl-grid">
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand patient">
                <h2 class="mdl-card__title-text">Patient Details</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <dl>
                    <dt>Name</dt>
                    <dd><?=$data->patient_name?></dd>
                </dl>
                <dl>
                    <dt>Date of Birth</dt>
                    <dd><?=date_create_from_format('Y-m-d', $data->date_of_birth)->format('j M Y')?></dd>
                </dl>
                <dl>
                    <dt>Hospital Number</dt>
                    <dd><?=$data->hos_num?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand patient">
                <h2 class="mdl-card__title-text">Operation Side</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?= $data->eye->name ?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand patient">
                <h2 class="mdl-card__title-text">Operation Type</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?=$data->procedure?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand biometry">
                <h2 class="mdl-card__title-text">IOL Model</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?=$data->iol_model?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand biometry">
                <h2 class="mdl-card__title-text">IOL Power</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?=$data->iol_power?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand biometry">
                <h2 class="mdl-card__title-text">Predicted refractive outcome</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?=$data->predicted_refractive_outcome?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand risk">
                <h2 class="mdl-card__title-text">Allergies</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?=nl2br($data->allergies)?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand risk">
                <h2 class="mdl-card__title-text">Alpha-blockers</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?php if($data->alpha_blockers):?>
                    Yes
                <?php else: ?>
                    No
                <?php endif;?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand comment">
                <h2 class="mdl-card__title-text">Predicted additional equipment</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?php if($data->predicted_additional_equipment): ?>
                    <?=nl2br($data->predicted_additional_equipment)?>
                <?php else:?>
                    None
                <?php endif;?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand risk">
                <h2 class="mdl-card__title-text">Anticoagulants</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?php if($data->anticoagulants):?>
                Yes
                <?php else: ?>
                No
                <?php endif;?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--8-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand comment">
                <h2 class="mdl-card__title-text">Comments</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?=nl2br($data->comments)?>
            </div>
        </div>
    </div>
</div>
<div id="dialog-container"></div>
</body>
</html>