<?php $current_uri = '/' . $this->module->id . '/' . $this->id . '/' . $this->action->id; ?>

<?php if ($this->checkAccess('Genetics Admin')) : ?>
<div class="box genetics panel">
    <h2>Admin</h2>
    <ul class="navigation">
    <?php
        $sidebarLinks = array(
            'Amino Acid C. Type' => Yii::app()->createUrl('/Genetics/aminoAcidChangeAdmin/list'),
            'Base Change Type' => Yii::app()->createUrl('/Genetics/baseChangeAdmin/list'),

            'Genetic Results Method' => '/OphInGeneticresults/methodAdmin/list',
            'Genetic Results Effect' => '/OphInGeneticresults/effectAdmin/list',
            'Studies' => Yii::app()->createUrl('/Genetics/study/list'),
            'Genes' => Yii::app()->createUrl('/Genetics/gene/list'),
        );

        foreach ($sidebarLinks as $title => $uri) { ?>
            <li<?php if ($current_uri == $uri) {
                ?> class="selected"<?php
               } ?>>
                <?php if ($current_uri == $uri) { ?>
                    <?=\CHtml::link($title, array($uri), array('class' => 'selected')) ?>
                <?php } else { ?>
                    <?=\CHtml::link($title, array($uri)) ?>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
</div>

<?php endif; ?>

<div class="box genetics panel">
    <h2>Menu</h2>
    <ul class="navigation">
        <?php $sidebarLinks = array(
            'Patients' => Yii::app()->createUrl('/Genetics/subject/list'),
            'Families' => Yii::app()->createUrl('/Genetics/pedigree/list'),
            'Genetic test/result' => Yii::app()->createUrl('/OphInGeneticresults/search/geneticResults'),
            'Samples' => Yii::app()->createUrl('/OphInDnasample/search/DnaSample'),

        );

        $current_uri = '/' . $this->module->id . '/' . $this->id . '/' . $this->action->id;

foreach ($sidebarLinks as $title => $uri) { ?>
            <li<?php if ($current_uri == $uri) {
                ?> class="selected"<?php
               } ?>>
                <?php if ($current_uri == $uri) { ?>
                    <?=\CHtml::link($title, array($uri), array('class' => 'selected')) ?>
                <?php } else { ?>
                    <?=\CHtml::link($title, array($uri)) ?>
                <?php } ?>
            </li>
<?php } ?>
    </ul>
</div>
