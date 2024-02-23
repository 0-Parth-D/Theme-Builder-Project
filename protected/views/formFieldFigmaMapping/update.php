<?php
/* @var $this FormFieldFigmaMappingController */
/* @var $model FormFieldFigmaMapping */

$this->breadcrumbs = array(
    'Form Field Figma Mappings' => array('index'),
    $model->id => array('view', 'id' => $model->id),
    'Update',
);

$this->menu = array(
    array('label' => 'List FormFieldFigmaMapping', 'url' => array('index')),
    array('label' => 'Create FormFieldFigmaMapping', 'url' => array('create')),
    array('label' => 'View FormFieldFigmaMapping', 'url' => array('view', 'id' => $model->id)),
    array('label' => 'Manage FormFieldFigmaMapping', 'url' => array('admin')),
);
?>

<h1>Update FormFieldFigmaMapping <?php echo $model->id; ?></h1>


<?php Yii::app()->clientScript->registerScriptFile("./AjaxFiles/parseHtmlCss.js", CClientScript::POS_END); ?>
<?php // Yii::app()->clientScript->registerScriptFile("./AjaxFiles/formElementFrameMap.js", CClientScript::POS_END); ?>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'form-field-figma-mapping-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <?php
    echo '<h1>Current Mapping</h1>';
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'form-field-figma-mapping-grid',
        'dataProvider' => $model->search(),
//    'filter'=>$model,
        'columns' => array(
            'id',
            array(
                'header' => 'Form',
                'value' => function ($data) use ($formList) {
                    return $formList[$data->form_id];
                },
            ),
            array(
                'header' => 'Field',
                'value' => function ($data) use ($formFieldsList) {
                    if ($data->field_id != 0) {
                        return $formFieldsList[$data->field_id];
                    }
                },
            ),
            'class_name',
            'html_tag',
            'frame_name',
            'flag',
            array(// This is the new button column
                'header' => 'Delete',
                'type' => 'raw',
                'value' => function ($data) {
                    return CHtml::button('Delete', array('onclick' => 'deleteMapping(' . $data->id . ', this);'));
                },
            ),
        ),
    ));
    ?>

    <!--    <div class="row">
            <p>CSS Code</p>
            <textarea type="textField" id="cssCode" name="cssCode"></textarea><br>
        </div>
    
        <div class="row buttons">
    <?php // echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('onclick' => 'js:myFunction();'));  ?>
            <button id="getFramesBtn" type="button" onClick="getFrameData(); submitbtn();">Get Frames</button>
        </div>-->

    <div class="row">
        <?php echo $form->labelEx($model, 'form_id'); ?>
        <?php echo $form->dropDownList($model, 'form_id', $formList, array('id' => 'formName', 'empty' => 'Select Form')) ?>
        <?php echo $form->error($model, 'form_id'); ?>
    </div>


    <div class="fieldFrameMapp" style="display: flex; column-gap: 56px; align-items: flex-start; flex-wrap: wrap">
        <div class="fieldClassSelector" style="display: flex; justify-content: space-between; align-items: center; flex-direction: column">
            <div class="row">
                <?php echo $form->labelEx($model, 'field_id'); ?>
                <?php echo $form->listBox($model, 'field_id', array(), array('id' => 'fieldName', 'onClick' => 'lockMappingList("fieldClass"); lockMappingList("htmlTag");', 'style' => 'height: 120px; width: 250px; font-size: 0.9rem; padding: 0.5rem')); ?>
                <?php echo $form->error($model, 'field_id'); ?>
            </div>

            <div class="row">
                <?php echo $form->labelEx($model, 'class_name'); ?>
                <?php echo $form->listBox($model, 'class_name', $formClassList, array('id' => 'fieldClass', 'onClick' => 'lockMappingList("fieldName"); lockMappingList("htmlTag");', 'style' => 'height: 120px; width: 250px; font-size: 0.9rem; padding: 0.5rem; border-color: orange;')); ?>
                <?php echo $form->error($model, 'class_name'); ?>
            </div>

            <div class="row">
                <?php echo $form->labelEx($model, 'html_tag'); ?>
                <?php echo $form->listBox($model, 'html_tag', $htmlTagList, array('id' => 'htmlTag', 'onClick' => 'lockMappingList("fieldName"); lockMappingList("fieldClass");', 'style' => 'height: 120px; width: 250px; font-size: 0.9rem; padding: 0.5rem; border-color: red;')); ?>
                <?php echo $form->error($model, 'html_tag'); ?>
            </div>
            <button id="clearSelectionBtn" type="button" onClick="clearListBoxSelection();">Clear Selection</button>
            <br>
        </div>

        <div class="row buttons">
            <?php // echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('onclick' => 'js:myFunction();'));  ?>
            <!--            <button id="mapFieldsBtn" type="button" onClick="fetchElementFrameMapping();"><= Map =></button>-->
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'frame_name'); ?>
            <?php // echo $form->listBox($model, 'frame_name', array($model->frame_name), array('id' => 'frameName', 'style' => 'height: 400px; width: 250px; font-size: 0.9rem; padding: 0.5rem')); ?>
            <?php echo '<div id="frameName" style="padding: 10px; margin-top: 10px; border: dashed 1px black; border-radius: 5px">' . $model->frame_name . '</div>'; ?>
            <?php echo $form->error($model, 'frame_name'); ?>
        </div>
    </div>

    <!--    <div id ="mappingContainer1" class="mapDisplayContainer" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <p>(empty)</p>
        </div>-->

    <div class="row buttons">
        <?php
        echo CHtml::submitButton('Save', array('onClick' => 'updateFieldFrameMapping();'));
//            echo '<button type="button" onClick="updateFieldFrameMapping();">Submit btn</button>';
        ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

<script> // Similar to _form
    $(document).ready(function () {
        // Listen for changes in the "Form" dropdown list
        $('#formName').on('change', function () {
            var formId = $(this).val(); // Get the selected form ID
            var fieldIdCurrent = <?php echo $model->field_id; ?>;
            // Send an AJAX request to fetch the field IDs associated with the selected form ID
            $.ajax({
                url: 'index.php?r=formFieldFigmaMapping/fetchFields', // Adjust the URL to point to your controller/action
                type: 'GET',
                data: {formId: formId},
                success: function (response) {
                    console.log(response);
                    var jsonResponse = JSON.parse(response);
                    console.log(jsonResponse);
                    // Update the options of the existing "field_id" dropdown list with the returned field IDs
                    $('#fieldName').empty(); // Clear existing options
                    $.each(jsonResponse.fieldIds, function (index, fieldId) {
                        var fieldName = jsonResponse.fieldNames[index];

                        $('#fieldName').append($('<option>', {
                            value: fieldId,
                            text: fieldName
                        }));
                    });
                    // Preselect the value same as $model->field_id
                    $('#fieldName').val(fieldIdCurrent);
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }).change(); // Trigger the change event
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
        } else if (selectedClass.value && selectedFrame.value) {
            mappingList[formClassList[selectedClass.value]] = selectedFrame.value;
            selectedClass.options[selectedClass.selectedIndex].remove();
            selectedFrame.options[selectedFrame.selectedIndex].remove();
        } else if (selectedHtmlTag.value && selectedFrame.value) {
            htmlMappingList[selectedHtmlTag.value] = selectedFrame.value;
            selectedHtmlTag.options[selectedHtmlTag.selectedIndex].remove();
            selectedFrame.options[selectedFrame.selectedIndex].remove();
        }
        document.getElementById('mappingContainer1').innerHTML = "";
//    console.log(mappingList);
//    console.log(htmlMappingList);
        for (let maps in mappingList) {
            var newDiv = document.createElement("div");
            newDiv.className = "mapDisplayList";
            if (!isNaN(maps)) {
                newDiv.style.cssText = "position: relative; display: flex; justify-content: center; flex-wrap: wrap; gap: 0.5rem; border: solid 1px #a8a8a8; border-radius: 5px; padding: 0.5rem; width: 48%; height: auto;";
            } else {
                newDiv.style.cssText = "position: relative; display: flex; justify-content: center; flex-wrap: wrap; gap: 0.5rem; border: solid 1px orange; border-radius: 5px; padding: 0.5rem; width: 48%; height: auto;";
            }
            var newP1 = document.createElement("p");
            newP1.style.margin = "0px";
            if (!isNaN(maps)) {
                newP1.textContent = formFieldsList[maps];
            } else {
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
            newRemove.onclick = function () {
                if (!isNaN(maps)) {
                    var singleValue = new Option(formFieldsList[maps], maps);
                    selectedField.add(singleValue);
                } else {
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
            newRemove.onclick = function () {
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

    function clearListBoxSelection() {
        const selectedField = document.getElementById('fieldName');
        const selectedClass = document.getElementById('fieldClass');
//        const selectedFrame = document.getElementById('frameName');
        const selectedHtmlTag = document.getElementById('htmlTag');
        selectedField.selectedIndex = -1;
        selectedClass.selectedIndex = -1;
//        selectedFrame.selectedIndex = -1;
        selectedHtmlTag.selectedIndex = -1;
        selectedField.disabled = false;
        selectedClass.disabled = false;
        selectedHtmlTag.disabled = false;
    }

    function updateFieldFrameMapping() {
        var updateModelId = <?php echo $modelId ?>;
        if (updateModelId !== -1) {
            $.ajax({
                url: 'index.php?r=formFieldFigmaMapping/editToMappingList',
                type: 'POST',
                data: {updateModelId: updateModelId, fieldNameUpdate: document.getElementById('fieldName').value, classNameUpdate: document.getElementById('fieldClass').value, htmlTagUpdate: document.getElementById('htmlTag').value, frameNameUpdate: document.getElementById('frameName').value},
                success: function (updateModelId, fieldNameUpdate, classNameUpdate, htmlTagUpdate, frameName) {
                    console.log(updateModelId);
                    console.log(fieldNameUpdate);
                    console.log(classNameUpdate);
                    console.log(htmlTagUpdate);
                    console.log(frameName);
                },

                error: function (jqXHR, textStatus, errorThrown) {
                    // Handle the error case here
                    console.error('AJAX request failed:', textStatus, errorThrown);
                }
            });
        }
    }

    function lockMappingList(listToLock) {
        const selectedList = document.getElementById(listToLock);
        selectedList.value = -1;
        selectedList.disabled = true;
    }

</script>
