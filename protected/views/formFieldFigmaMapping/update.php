<?php
/* @var $this FormFieldFigmaMappingController */
/* @var $model FormFieldFigmaMapping */

$this->breadcrumbs=array(
	'Form Field Figma Mappings'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List FormFieldFigmaMapping', 'url'=>array('index')),
	array('label'=>'Create FormFieldFigmaMapping', 'url'=>array('create')),
	array('label'=>'View FormFieldFigmaMapping', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage FormFieldFigmaMapping', 'url'=>array('admin')),
);
?>

<h1>Update FormFieldFigmaMapping <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>