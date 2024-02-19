<?php
/* @var $this FormFieldFigmaMappingController */
/* @var $model FormFieldFigmaMapping */

$this->breadcrumbs=array(
	'Form Field Figma Mappings'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List FormFieldFigmaMapping', 'url'=>array('index')),
	array('label'=>'Manage FormFieldFigmaMapping', 'url'=>array('admin')),
);
?>

<h1>Create FormFieldFigmaMapping</h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'formList'=>$formList, 'formFieldsList'=>$formFieldsList,  'formClassList'=>$formClassList, 'htmlTagList'=>$htmlTagList,)); ?>