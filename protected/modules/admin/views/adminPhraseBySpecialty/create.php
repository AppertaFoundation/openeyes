<?php
$this->breadcrumbs=array(
        'Phrase By Specialties' => array('/admin/adminPhraseBySpecialty/index'),
        $sectionName => array('specialtyIndex', 'section_id'=>$sectionId),
        $specialtyName => array('phraseIndex', 'section_id'=>$sectionId, 'specialty_id'=>$specialtyId),
	'Create'
);

$this->menu=array(
	array('label'=>'List phrases by specialty', 'url'=>array('index')),
	array('label'=>'Manage phrases by specialty', 'url'=>array('admin')),
);
?>

<h1>Create PhraseBySpecialty</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
