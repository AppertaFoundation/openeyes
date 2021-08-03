<?php
/**
 * @var $legacyepisodes
 * @var $active_episodes
 * @var $ordered_episodes
 * @var $active_episodes
 * @var Episode[] $specialty_episodes
 **/

$navIconUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue'), true) . '/svg/oe-nav-icons.svg';

// Note, we are ignoring the possibility of additional specialties here and only supporting the first,
// which is expected to be opthalmology.
$active_episodes = array();
if (is_array($ordered_episodes)) {
    foreach ($ordered_episodes as $specialty) {
        $active_episodes = array_merge($active_episodes, $specialty['episodes']);
    }
    //$active_episodes = $ordered_episodes[0]['episodes'];
}

// flatten the data structure to include legacy events into the core navigation. Note here we are
// simply assuming that the first entry will be Ophthalmology specialty (for the purposes of this PoC
// we don't anticipate events from any other specialty)
if (count($legacyepisodes)) {
    if (!is_array($ordered_episodes) || empty($ordered_episodes)) {
        $ordered_episodes = array(
            array(
                'specialty' => 'Ophthalmology',
                'episodes' => array(),
            ),
        );
    }
    foreach ($legacyepisodes as $le) {
        $ordered_episodes[0]['episodes'][] = $le;
    }
}
?>

<?php
$subspecialty_labels = array();
$current_subspecialty = null;
$episodes_list = array();

if (is_array($ordered_episodes)) { ?>
    <ul class="oescape-icon-btns" style="font-size: 0;">
        <?php foreach ($ordered_episodes as $specialty_episodes) {
            foreach ($specialty_episodes['episodes'] as $i => $episode) {
                $id = $episode->getSubspecialtyID();
                $subspecialty_name = $episode->getSubspecialtyText();
                if ($id) {
                    $tag = $episode->subspecialty->ref_spec;
                }

                if (!array_key_exists($id, $subspecialty_labels)) {
                    $subspecialty_labels[$id] = $subspecialty_name;
                }
                if (!array_key_exists($id, $episodes_list)) {
                    $episodes_list[$id] = $episode;
                }
            }
        }

        $subspecialties = Subspecialty::model()->findAllByAttributes(
            [
            'name'=> [
              'Cataract',
              'Glaucoma',
              'Medical Retina',
              'General Ophthalmology'
            ]
            ]
        );

        foreach ($subspecialties as $subspecialty) { ?>
            <li class="icon-btn"
                data-subspecialty-id="<?= $subspecialty->id ?>">
                <a class="<?= in_array($subspecialty->id, array_keys($episodes_list))?'active':'inactive' ?>"
                   href="<?= Yii::app()->createUrl(
                       '/patient/oescape/',
                       [
                       'subspecialty_id' => $subspecialty->id,
                       'patient_id' => $this->patient->id
                       ]
                         ) ?>" >
                  <?= $subspecialty->ref_spec ?>
                </a>
            </li>
        <?php } ?>

        <li class="icon-btn">
            <a href="<?= Yii::app()->createUrl('/patient/lightningViewer', array('id' => $this->patient->id)) ?>"
               class="lightning-viewer-icon active <?= $this->action->id === 'lightningViewer' ? 'selected' : '' ?>">
                <svg viewBox="0 0 30 30" width="15" height="15">
                    <use xlink:href="<?= $navIconUrl ?>#lightning-viewer-icon"></use>
                </svg>
            </a>
        </li>
        <li class="icon-btn">
            <a href="<?= Yii::app()->createUrl('/patient/summary', array('id' => $this->patient->id)) ?>"
               class="patient-overview-icon active <?= $this->action->id === "summary" ? 'selected' : ''?>">
                <svg viewBox="0 0 30 30" width="15" height="15">
                    <use xlink:href="<?= $navIconUrl ?>#patient-icon"></use>
                </svg>
            </a>
        </li>
    </ul>
<?php } ?>