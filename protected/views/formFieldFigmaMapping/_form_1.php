<?php
/* @var $this FormFieldFigmaMappingController */
/* @var $model FormFieldFigmaMapping */
/* @var $form CActiveForm */
?>

<?php Yii::app()->clientScript->registerScriptFile("./AjaxFiles/parseHtmlCss.js", CClientScript::POS_END); ?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'form-field-figma-mapping-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
        
	<div class="row">
		<?php echo $form->labelEx($model,'form_id'); ?>
		<?php echo $form->dropDownList($model,'form_id', $formList) ?>
		<?php echo $form->error($model,'form_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'field_id'); ?>
		<?php echo $form->listBox($model,'field_id', $formFieldsList, array('style'=>'height: 250px; width: 250px; font-size: 0.9rem; padding: 0.5rem')); ?>
		<?php echo $form->error($model,'field_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'frame_name'); ?>
		<?php echo $form->listBox($model,'frame_name', $framesList, array('style'=>'height: 250px; width: 250px; font-size: 0.9rem; padding: 0.5rem')); ?>
		<?php echo $form->error($model,'frame_name'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->