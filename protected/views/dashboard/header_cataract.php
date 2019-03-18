<div class="mdl-layout__header-row">
     <span class="mdl-layout-title">
         <img src="<?= Yii::app()->assetManager->createUrl('img/_elements/graphic/OpenEyes_logo_transparent.png')?>" alt="OpenEyes logo" />
         Cataract Personal Audit: <?= $this->user->getFullNameAndTitle() ?>
    </span>
    <div class="mdl-layout-spacer"></div>
    <form id="search-form">
        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
            <input class="mdl-textfield__input" type="text" id="from-date" name="from">
            <label class="mdl-textfield__label" for="from-date">From...</label>
        </div>
        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
            <input class="mdl-textfield__input" type="text" id="to-date" name="to">
            <label class="mdl-textfield__label" for="to-date">To...</label>
        </div>
        <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" type="submit" name="action">Submit
            <i class="material-icons right">send</i>
        </button>
    </form>
</div>
