<?php $annotateToolsIconUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue'), true) . '/svg/oe-annotate-tools.svg'; ?>

<div class="toolbox">
    <button name="manipulate" class="tool-manipulate js-tool-btn">
        <svg viewBox="0 0 57 19" class="tool-icon">
            <use xlink:href="<?= $annotateToolsIconUrl; ?>#manipulate"></use>
        </svg>
    </button><button name="freedraw" class="tool-btn js-tool-btn">
        <svg viewBox="0 0 19 19" class="tool-icon">
            <use xlink:href="<?= $annotateToolsIconUrl; ?>#freedraw"></use>
        </svg>
    </button><button name="circle" class="tool-btn js-tool-btn">
        <svg viewBox="0 0 19 19" class="tool-icon">
            <use xlink:href="<?= $annotateToolsIconUrl; ?>#circle"></use>
        </svg>
    </button><button name="pointer" class="tool-btn js-tool-btn">
        <svg viewBox="0 0 19 19" class="tool-icon">
            <use xlink:href="<?= $annotateToolsIconUrl; ?>#pointer"></use>
        </svg>
    </button>
    <div class="line-width">
        <div class="js-line-width"><small>Line width: 3</small></div>
        <input type="range" min="1" max="5" value="3" class="cols-full js-tool-line-width">
    </div>

    <hr>

    <svg class="colors" viewBox="0 0 150 50" width="100%" height="50px">
        <rect class="selected" x="1" y="2" width="20" height="20" rx="10" fill="#c00"></rect>
        <rect x="26" y="2" width="20" height="20" rx="10" fill="#0c0"></rect>
        <rect x="51" y="2" width="20" height="20" rx="10" fill="#09f"></rect>
        <rect x="76" y="2" width="20" height="20" rx="10" fill="#ff0"></rect>
        <rect x="101" y="2" width="20" height="20" rx="10" fill="#e50"></rect>
        <rect x="126" y="2" width="20" height="20" rx="10" fill="#f5b"></rect>
        <rect x="1" y="27" width="20" height="20" rx="10" fill="#000"></rect>
        <rect x="26" y="27" width="20" height="20" rx="10" fill="#888"></rect>
        <rect x="51" y="27" width="20" height="20" rx="10" fill="#fff"></rect>
        <rect x="76" y="27" width="20" height="20" rx="10" fill="#600"></rect>
        <rect x="101" y="27" width="20" height="20" rx="10" fill="#060"></rect>
        <rect x="126" y="27" width="20" height="20" rx="10" fill="#006"></rect>
    </svg>

    <hr>

    <input type="text" id="js-label-text" class="cols-full" placeholder="Label text...">
    <button name="text" class="js-tool-btn">Add label</button>

    <hr>

    <button name="erase" class="js-tool-btn">Erase selected</button>
    <button name="clear-all" class="js-tool-btn">Clear all</button>
</div>