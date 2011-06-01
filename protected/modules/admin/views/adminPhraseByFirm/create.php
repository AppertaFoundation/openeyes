<?php
$this->breadcrumbs=array(
        'Phrase By Specialties' => array('/admin/adminPhraseBySpecialty/index'),
        $sectionName => array('specialtyIndex', 'section_id'=>$sectionId),
        $firmName => array('phraseIndex', 'section_id'=>$sectionId, 'firm_id'=>$_GET['firm_id']),
	'Create'
);

$this->menu=array(
	array('label'=>'List phrasees by firm', 'url'=>array('index')),
	array('label'=>'Manage phrases by firm', 'url'=>array('admin')),
);
?>

<h1>Create PhraseByFirm</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
