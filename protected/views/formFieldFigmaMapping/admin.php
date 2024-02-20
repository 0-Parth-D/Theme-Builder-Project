<?php
/* @var $this FormFieldFigmaMappingController */
/* @var $model FormFieldFigmaMapping */

$this->breadcrumbs=array(
	'Form Field Figma Mappings'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List FormFieldFigmaMapping', 'url'=>array('index')),
	array('label'=>'Create FormFieldFigmaMapping', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#form-field-figma-mapping-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Form Field Figma Mappings</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php 
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'form-field-figma-mapping-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        'id',
        'form_id',
        'field_id',
        'class_name',
        'html_tag',
        'frame_name',
        'flag',
        array( // This is the new button column
            'header' => 'Edit',
            'type' => 'raw',
            'value' => function($data) {
                return CHtml::button('Edit', array('onclick' => 'editMapping('.$data->id.');'));
            },
        ),
        array( // This is the new button column
            'header' => 'Delete',
            'type' => 'raw',
            'value' => function($data) {
                return CHtml::button('Delete', array('onclick' => 'deleteMapping('.$data->id.', this);'));
            },
        ),
    ),
));
//print_r(FormFieldFigmaMapping::model()->findAll());
?>
<script>
function editMapping(editValue){
    $.ajax({
            url: 'index.php?r=formFieldFigmaMapping/editAllMapping',
            type: 'POST',
            data: {editValue: editValue},
            success: function (editValue) {
                    $("body").html(editValue);  
            },

            error: function (jqXHR, textStatus, errorThrown) {
                // Handle the error case here
                console.error('AJAX request failed:', textStatus, errorThrown);
            }
        });
}

function deleteMapping(deleteValue, element){
    $.ajax({
            url: 'index.php?r=formFieldFigmaMapping/deleteAllMapping',
            type: 'POST',
            data: {deleteValue: deleteValue},
            success: function (deleteValue) {
                console.log(deleteValue);
                $(element).parent().parent().remove();
            },

            error: function (jqXHR, textStatus, errorThrown) {
                // Handle the error case here
                console.error('AJAX request failed:', textStatus, errorThrown);
            }
        });
}
</script>
