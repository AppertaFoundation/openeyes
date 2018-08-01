<?php
/**
 * @var $legacyepisodes
 * @var $active_episodes
 * @var $ordered_episodes
 * @var $active_episodes
 * @var Episode[] $specialty_episodes
 **/

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

if (is_array($ordered_episodes)):
    foreach ($ordered_episodes as $specialty_episodes): ?>
      <ul class="oescape-icon-btns" style="font-size: 0;">
          <?php foreach ($specialty_episodes['episodes'] as $i => $episode) {
              // TODO deal with support services possibly?
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
          $subspecialties = array_map(function ($v) {
              return array($v->id, $v->name, $v->ref_spec);
          }, Subspecialty::model()->findAllByAttributes(array('name'=> array('Cataract', 'Glaucoma', 'Medical Retina','General Ophthalmology'))));
          foreach ($subspecialties as $subspecialty) { ?>
              <li class="icon-btn"
                  data-subspecialty-id="<?= $subspecialty[0] ?>">
                <a class="<?= in_array($subspecialty[0], array_keys($episodes_list))?'active':'inactive' ?>"
                   href=" <?= in_array($subspecialty[0], array_keys($episodes_list))?Yii::app()->createUrl('/patient/oescape/' . $episodes_list[$subspecialty[0]]->id):'' ?>">
                    <?= $subspecialty[2] ?>
                </a>
              </li>
          <?php
          } ?>
      </ul>
    <?php endforeach;
endif; ?>
