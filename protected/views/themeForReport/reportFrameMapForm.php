<?php
/* @var $this ThemeForReportController */
/* @var $model ThemeForReport */
/* @var $form CActiveForm */
?>

<?php Yii::app()->clientScript->registerScriptFile("./AjaxFiles/reportParseCss.js", CClientScript::POS_END); ?>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'theme-for-report-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'theme_name'); ?>
        <?php echo $form->textField($model, 'theme_name', array('id'=> 'themeName', 'size' => 60, 'maxlength' => 255)); ?>
        <?php echo $form->error($model, 'theme_name'); ?>
    </div>
    <span> Theme name should be unique with first letter capital</span>

    <div class="row">
        <label style="margin: 0;">CSS Code</label>
        <textarea type="textField" id="cssCode" name="cssCode" style="width: 100%; height: 10rem"></textarea><br>
    </div>

    <div class="row buttons">
        <?php // echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('onclick' => 'js:myFunction();'));   ?>
        <button id="getFramesBtn" type="button" onClick="getFrameDataReport(); submitbtn();">Get Frames</button>
    </div>

    <div class="fieldFrameMapp" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap">
        <div class="fieldClassSelector" style="display: flex; justify-content: space-between; align-items: center; flex-direction: column">
            <div class="row">
                <?php echo $form->labelEx($model, 'element_id'); ?>
                <?php echo $form->listBox($model, 'element_id', $elementCssList, array('id' => 'elementName', 'style' => 'height: 250px; width: 250px; font-size: 0.9rem; padding: 0.5rem')); ?>
                <?php echo $form->error($model, 'element_id'); ?>
            </div>

            <br>
        </div>

        <div class="row buttons">
            <?php // echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('onclick' => 'js:myFunction();'));   ?>
            <button id="mapFieldsBtn" type="button" onClick="fetchReportFrameMapping();"><= Map =></button>
        </div>

        <div class="row">
            <label>Frame Name</label>
            <select id="frameNameReport" size = 2 style="height: 250px; width: 250px; font-size: 0.9rem; padding: 0.5rem">
                <!-- Options would go here -->
            </select>
        </div>
    </div>
    <div id ="mappingContainer1" class="mapDisplayContainer" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <p>(empty)</p>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('onclick' => 'saveCssProperties();'));   ?>
        <!--<button id="mapFieldsBtn" type="button" onClick="saveCssProperties();">Create</button>-->
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

<script>

    // Global Variables
    var mappingList = {};

    // Function to Get the selected field and frame mapping
    function fetchReportFrameMapping() {
        const selectedReportElement = document.getElementById('elementName');
        const selectedFrame = document.getElementById('frameNameReport');
        var elementCssList = <?php echo json_encode($elementCssList); ?>;

        // Save the mapping according to the selected class_name, html_tag or field_id
        if (selectedReportElement.value && selectedFrame.value) {
            mappingList[selectedReportElement.value] = selectedFrame.value;
            selectedReportElement.options[selectedReportElement.selectedIndex].remove();
            selectedFrame.options[selectedFrame.selectedIndex].remove();
        }

        document.getElementById('mappingContainer1').innerHTML = "";

        // Create dynamic <div> to display the mappings for field_id and class_name
        for (let maps in mappingList) {
            var newDiv = document.createElement("div");
            newDiv.className = "mapDisplayList";
            newDiv.style.cssText = "position: relative; display: flex; justify-content: center; flex-wrap: wrap; gap: 0.5rem; border: solid 1px #a8a8a8; border-radius: 5px; padding: 0.5rem; width: 48%; height: auto;";
            var newP1 = document.createElement("p");
            newP1.style.margin = "0px";
            newP1.textContent = elementCssList[maps];
            newDiv.appendChild(newP1);
            var newP2 = document.createElement("p");
            newP2.style.margin = "0px";
            newP2.textContent = '=>';
            newDiv.appendChild(newP2);
            var newP3 = document.createElement("p");
            newP3.style.margin = "0px";
            newP3.textContent = mappingList[maps];
            newDiv.appendChild(newP3);
            var newRemove = document.createElement("button");
            newRemove.style.cssText = "position: absolute; bottom: 0; right: 0;";
            newRemove.textContent = '-';
            newRemove.onclick = function () {
                var singleValue = new Option(elementCssList[maps], maps);
                selectedReportElement.add(singleValue);
                var frameValue = new Option(mappingList[maps]);
                selectedFrame.add(frameValue);
                $(this).parent().remove();
                delete mappingList[maps];
            };
            newDiv.appendChild(newRemove);
            $('.mapDisplayContainer').append(newDiv);
        }

        clearListBoxSelection(); // Clear selections
    }

    // Function to clear the selections after mapping and removing the disabled status
    function clearListBoxSelection() {
        const selectedReportElement = document.getElementById('elementName');
        const selectedFrame = document.getElementById('frameNameReport');
        selectedReportElement.selectedIndex = -1;
        selectedFrame.selectedIndex = -1;
        selectedReportElement.disabled = false;
    }
</script>