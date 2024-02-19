<?php
/* @var $this FormFieldCsspropertyValueMappingController */
/* @var $model FormFieldCsspropertyValueMapping */
/* @var $form CActiveForm */

$forms = Forms::model()->findAll(array('order' => 'FORM_NAME'));
$formsList = CHtml::listData($forms, 'FORM_ID', 'FORM_NAME');

$cssProperty = CssProperties::model()->findAll(array('order' => 'property_name'));
$cssPropertyList = CHtml::listData($cssProperty, 'id', 'property_name');

$fields = FormFields::model()->findAll(array('order' => 'TITLE'));
$fieldsList = CHtml::listData($fields, 'FIELD_ID', 'TITLE');
?>

<?php Yii::app()->clientScript->registerScriptFile("./AjaxFiles/parseHtmlCss.js", CClientScript::POS_END); ?>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'form-field-cssproperty-value-mapping-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

<!--    <div class="row">
        <p>HTML Code</p>
        <textarea type="textField" id="htmlCode" name="htmlCode"></textarea>
    </div>-->

    <div class="row">
        <p>CSS Code</p>
        <textarea type="textField" id="cssCode" name="cssCode"></textarea><br>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('onclick' => 'js:myFunction();')); ?>
        <!--<button type="button" onClick="myFunction();">submit</button>-->
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->