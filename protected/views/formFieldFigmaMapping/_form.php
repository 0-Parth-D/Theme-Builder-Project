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

        
        <div class="fieldFrameMapp" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap">
            <div class="fieldClassSelector" style="display: flex; justify-content: space-between; align-items: center; flex-direction: column">
                <div class="row">
                        <?php echo $form->labelEx($model,'field_id'); ?>
                        <?php echo $form->listBox($model,'field_id', array(), array('id' => 'fieldName', 'onClick'=>'lockMappingList("fieldClass"); lockMappingList("htmlTag");', 'style'=>'height: 120px; width: 250px; font-size: 0.9rem; padding: 0.5rem')); ?>
                        <?php echo $form->error($model,'field_id'); ?>
                </div>

                <div class="row">
                        <?php echo $form->labelEx($model,'class_name'); ?>
                        <?php echo $form->listBox($model,'class_name', $formClassList, array('id' => 'fieldClass', 'onClick'=>'lockMappingList("fieldName"); lockMappingList("htmlTag");', 'style'=>'height: 120px; width: 250px; font-size: 0.9rem; padding: 0.5rem; border-color: orange;')); ?>
                        <?php echo $form->error($model,'class_name'); ?>
                </div>
                
                <div class="row">
                        <?php echo $form->labelEx($model,'flag'); ?>
                        <?php echo $form->listBox($model,'flag', $htmlTagList, array('id' => 'htmlTag', 'onClick'=>'lockMappingList("fieldName"); lockMappingList("fieldClass");', 'style'=>'height: 120px; width: 250px; font-size: 0.9rem; padding: 0.5rem; border-color: red;')); ?>
                        <?php echo $form->error($model,'flag'); ?>
                </div>
            <button id="clearSelectionBtn" type="button" onClick="clearListBoxSelection();">Clear Seletion</button>
            <br>
            </div>
            
            <div class="row buttons">
                <?php // echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('onclick' => 'js:myFunction();')); ?>
                <button id="mapFieldsBtn" type="button" onClick="fetchElementFrameMapping();"><= Map =></button>
            </div>

            <div class="row">
                    <?php echo $form->labelEx($model,'frame_name'); ?>
                    <?php echo $form->listBox($model,'frame_name', array() ,array('id' => 'frameName', 'style'=>'height: 400px; width: 250px; font-size: 0.9rem; padding: 0.5rem')); ?>
                    <?php echo $form->error($model,'frame_name'); ?>
            </div>
        </div>
        
        <?php echo $form->labelEx($model, 'Mapping List'); ?><br>
        
        <div id ="mappingContainer1" class="mapDisplayContainer" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <p>(empty)</p>
        </div>

	<div class="row buttons">
		<?php // echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('onClick' => 'saveFieldFrameMapping();')); ?>
                <button type="button" onClick="saveFieldFrameMapping();">Submit</button>
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
                   console.log(jsonResponse);   
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
var htmlMappingList = {};

function fetchElementFrameMapping() {
    const selectedField = document.getElementById('fieldName');
    const selectedClass = document.getElementById('fieldClass');
    const selectedFrame = document.getElementById('frameName');
    const selectedHtmlTag = document.getElementById('htmlTag');
    var formFieldsList = <?php echo json_encode($formFieldsList); ?>;
    var formClassList = <?php echo json_encode($formClassList); ?>;
    var htmlTagList = <?php echo json_encode($htmlTagList); ?>;
  
    if (selectedField.value && selectedFrame.value) {
        mappingList[selectedField.value] = selectedFrame.value;
        selectedField.options[selectedField.selectedIndex].remove();
        selectedFrame.options[selectedFrame.selectedIndex].remove();
    }
    else if (selectedClass.value && selectedFrame.value) {
        mappingList[formClassList[selectedClass.value]] = selectedFrame.value;
        selectedClass.options[selectedClass.selectedIndex].remove();
        selectedFrame.options[selectedFrame.selectedIndex].remove();
    }
    else if (selectedHtmlTag.value && selectedFrame.value) {
        htmlMappingList[selectedHtmlTag.value] = selectedFrame.value;
        selectedHtmlTag.options[selectedHtmlTag.selectedIndex].remove();
        selectedFrame.options[selectedFrame.selectedIndex].remove();
    }
    document.getElementById('mappingContainer1').innerHTML = "";
    console.log(mappingList);
    console.log(htmlMappingList);
    for (let maps in mappingList) {
        var newDiv = document.createElement("div");
        newDiv.className = "mapDisplayList";
        if (!isNaN(maps)){
            newDiv.style.cssText = "position: relative; display: flex; justify-content: center; flex-wrap: wrap; gap: 0.5rem; border: solid 1px #a8a8a8; border-radius: 5px; padding: 0.5rem; width: 48%; height: auto;";
        }
        else{
            newDiv.style.cssText = "position: relative; display: flex; justify-content: center; flex-wrap: wrap; gap: 0.5rem; border: solid 1px orange; border-radius: 5px; padding: 0.5rem; width: 48%; height: auto;";
        }
        var newP1 = document.createElement("p");
        newP1.style.margin = "0px";
        if (!isNaN(maps)){
            newP1.textContent = formFieldsList[maps];
        }
        else{
            newP1.textContent = maps;
        }
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
            if (!isNaN(maps)){
                var singleValue = new Option(formFieldsList[maps], maps);
                selectedField.add(singleValue);
            }
            else{
                var singleClass = new Option(formClassList[maps]);
                selectedClass.add(singleClass);
            }
            var frameValue = new Option(mappingList[maps]);
            selectedFrame.add(frameValue);
            $(this).parent().remove();
            delete mappingList[maps];
        };
        newDiv.appendChild(newRemove);
        $('.mapDisplayContainer').append(newDiv);
    }
    for (let htmlMaps in htmlMappingList) {
        var newDiv = document.createElement("div");
        newDiv.className = "mapDisplayList";
        newDiv.style.cssText = "position: relative; display: flex; justify-content: center; flex-wrap: wrap; gap: 0.5rem; border: solid 1px red; border-radius: 5px; padding: 0.5rem; width: 48%; height: auto;";

        var newP1 = document.createElement("p");
        newP1.style.margin = "0px";
        newP1.textContent = htmlMaps;
        newDiv.appendChild(newP1);
        var newP2 = document.createElement("p");
        newP2.style.margin = "0px";
        newP2.textContent = '=>';
        newDiv.appendChild(newP2);
        var newP3 = document.createElement("p");
        newP3.style.margin = "0px";
        newP3.textContent = htmlMappingList[htmlMaps];
        newDiv.appendChild(newP3);
        var newRemove = document.createElement("button");
        newRemove.style.cssText = "position: absolute; bottom: 0; right: 0;";
        newRemove.textContent = '-';
        newRemove.onclick = function() {
            var singleHtmlTag = new Option(htmlTagList[htmlMaps]);
            selectedHtmlTag.add(singleHtmlTag);
            var frameValue = new Option(htmlMappingList[htmlMaps]);
            selectedFrame.add(frameValue);
            $(this).parent().remove();
            delete htmlMappingList[htmlMaps];
        };
        newDiv.appendChild(newRemove);
        $('.mapDisplayContainer').append(newDiv);
    }
    clearListBoxSelection();
}

function clearListBoxSelection(){
    const selectedField = document.getElementById('fieldName');
    const selectedClass = document.getElementById('fieldClass');
    const selectedFrame = document.getElementById('frameName');
    const selectedHtmlTag = document.getElementById('htmlTag');
    selectedField.selectedIndex = -1;
    selectedClass.selectedIndex = -1;
    selectedFrame.selectedIndex = -1;
    selectedHtmlTag.selectedIndex = -1;
    selectedField.disabled = false;
    selectedClass.disabled = false;
    selectedHtmlTag.disabled = false;
}

function saveFieldFrameMapping() {
    $.ajax({
        url: 'index.php?r=formFieldFigmaMapping/saveToMappingList',
        type: 'POST',
        data: {htmlMappingList: JSON.stringify(htmlMappingList), mappingList: JSON.stringify(mappingList), selectedForm: document.getElementById('formName').value},
        success: function (htmlMappingList, mappingList, selectedForm) {
            console.log(htmlMappingList);
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

function lockMappingList(listToLock){
    const selectedList = document.getElementById(listToLock);
    selectedList.disabled = true;
}

</script>