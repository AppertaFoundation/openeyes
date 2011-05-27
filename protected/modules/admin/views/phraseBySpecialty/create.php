<?php
$this->breadcrumbs=array(
        'Phrase By Specialties' => array('/admin/phraseBySpecialty/index'),
        $sectionName => array('specialtyIndex', 'section_id'=>$sectionId),
        $specialtyName => array('phraseIndex', 'section_id'=>$sectionId, 'specialty_id'=>$specialtyId),
	'Create'
);

$this->menu=array(
	array('label'=>'List PhraseBySpecialty', 'url'=>array('index')),
	array('label'=>'Manage PhraseBySpecialty', 'url'=>array('admin')),
);
?>

<h1>Create PhraseBySpecialty</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
