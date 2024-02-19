<?php
/* @var $this FormFieldFigmaMappingController */
/* @var $model FormFieldFigmaMapping */

$this->breadcrumbs=array(
	'Form Field Figma Mappings'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List FormFieldFigmaMapping', 'url'=>array('index')),
	array('label'=>'Create FormFieldFigmaMapping', 'url'=>array('create')),
	array('label'=>'Update FormFieldFigmaMapping', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete FormFieldFigmaMapping', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage FormFieldFigmaMapping', 'url'=>array('admin')),
);
?>

<h1>View FormFieldFigmaMapping #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'form_id',
		'field_id',
		'frame_name',
	),
)); ?>
