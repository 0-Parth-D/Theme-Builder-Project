<?php
/* @var $this FormFieldFigmaMappingController */
/* @var $model FormFieldFigmaMapping */
/* @var $form CActiveForm */
?>

<?php Yii::app()->clientScript->registerScriptFile("./AjaxFiles/parseHtmlCss.js", CClientScript::POS_END); ?>
<?php Yii::app()->clientScript->registerScriptFile("./AjaxFiles/formElementFrameMap.js", CClientScript::POS_END); ?>

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
            <p>CSS Code</p>
            <textarea type="textField" id="cssCode" name="cssCode"></textarea><br>
        </div>

        <div class="row buttons">
            <?php // echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('onclick' => 'js:myFunction();')); ?>
            <button id="getFramesBtn" type="button" onClick="getFrameData(); submitbtn();">Get Frames</button>
        </div>
        
	<div class="row">
		<?php echo $form->labelEx($model,'form_id'); ?>
		<?php echo $form->dropDownList($model,'form_id', $formList, array('id' => 'formName', 'empty' => 'Select Form')) ?>
		<?php echo $form->error($model,'form_id'); ?>
	</div>

        
        <div class="fieldFrameMapp" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="row">
                    <?php echo $form->labelEx($model,'field_id'); ?>
                    <?php echo $form->listBox($model,'field_id', array(), array('id' => 'fieldName', 'style'=>'height: 250px; width: 250px; font-size: 0.9rem; padding: 0.5rem')); ?>
                    <?php echo $form->error($model,'field_id'); ?>
            </div>
            
            <div class="row buttons">
                <?php // echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('onclick' => 'js:myFunction();')); ?>
                <button id="mapFieldsBtn" type="button" onClick="fetchElementFrameMapping();"><= Map =></button>
            </div>

            <div class="row">
                    <?php echo $form->labelEx($model,'frame_name'); ?>
                    <?php echo $form->listBox($model,'frame_name', array() ,array('id' => 'frameName', 'style'=>'height: 250px; width: 250px; font-size: 0.9rem; padding: 0.5rem')); ?>
                    <?php echo $form->error($model,'frame_name'); ?>
            </div>
        </div>
        
        <?php echo $form->labelEx($model, 'Mapping List'); ?><br>
        
        <div id ="mappingContainer1" class="mapDisplayContainer" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <p>(empty)</p>
        </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('onClick' => 'saveFieldFrameMapping();')); ?>
                <!--<button type="button" onClick="saveFieldFrameMapping();">Submit</button>-->
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script>
    $(document).ready(function() {
        // Listen for changes in the "Form" dropdown list
        $('#formName').on('change', function() {
            var formId = $(this).val(); // Get the selected form ID

            // Send an AJAX request to fetch the field IDs associated with the selected form ID
            $.ajax({
                url: 'index.php?r=formFieldFigmaMapping/fetchFields', // Adjust the URL to point to your controller/action
                type: 'GET',
                data: { formId: formId },
                success: function(response) {
                    console.log(response);
                    var jsonResponse = JSON.parse(response);
                   console.log(jsonResponse)
                    // Update the options of the existing "field_id" dropdown list with the returned field IDs
                    $('#fieldName').empty(); // Clear existing options
                    $.each(jsonResponse.fieldIds, function(index, fieldId) {
                                var fieldName = jsonResponse.fieldNames[index];

                        $('#fieldName').append($('<option>', {
                            value: fieldId,
                            text: fieldName
                        }));
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });
    });
$(document).ready(function () {
    // Event handler for page load
    $(window).on('load', function () {
        // Get the controller and action names
//            // Call the function to fetch CSS properties
    });
});

var mappingList = {};

function fetchElementFrameMapping() {
    const selectedField = document.getElementById('fieldName');
    const selectedFrame = document.getElementById('frameName');
    var formFieldsList = <?php echo json_encode($formFieldsList); ?>;
    if (selectedField.value && selectedFrame.value) {
        mappingList[selectedField.value] = selectedFrame.value;
        selectedField.options[selectedField.selectedIndex].remove();
        selectedFrame.options[selectedFrame.selectedIndex].remove();
    }
    selectedField.selectedIndex = -1;
    selectedFrame.selectedIndex = -1;
    document.getElementById('mappingContainer1').innerHTML = "";
    for (let maps in mappingList) {
        var newDiv = document.createElement("div");
        newDiv.className = "mapDisplayList";
        newDiv.style.cssText = "position: relative; display: flex; justify-content: center; flex-wrap: wrap; gap: 0.5rem; border: solid 1px #a8a8a8; border-radius: 5px; padding: 0.5rem; width: 48%; height: auto;";
        var newP1 = document.createElement("p");
        newP1.style.margin = "0px";
        newP1.textContent = formFieldsList[maps];
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
        newRemove.onclick = function() {
            $(this).parent().remove();
            delete mappingList[maps];
        };
        newDiv.appendChild(newRemove);
        $('.mapDisplayContainer').append(newDiv);
    }
}

function saveFieldFrameMapping() {
    $.ajax({
        url: 'index.php?r=formFieldFigmaMapping/saveToMappingList',
        type: 'POST',
        data: {mappingList: JSON.stringify(mappingList), selectedForm: document.getElementById('formName').value},
        success: function (mappingList, selectedForm) {
            console.log(mappingList);
            console.log(selectedForm);
        },

        error: function (jqXHR, textStatus, errorThrown) {
            // Handle the error case here
            console.error('AJAX request failed:', textStatus, errorThrown);
        }
    });
    saveCssProperties();
}

</script>