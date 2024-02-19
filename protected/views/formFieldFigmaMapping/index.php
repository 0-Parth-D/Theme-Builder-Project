<?php
/* @var $this FormFieldFigmaMappingController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Form Field Figma Mappings',
);

$this->menu=array(
	array('label'=>'Create FormFieldFigmaMapping', 'url'=>array('create')),
	array('label'=>'Manage FormFieldFigmaMapping', 'url'=>array('admin')),
);
?>

<h1>Form Field Figma Mappings</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
