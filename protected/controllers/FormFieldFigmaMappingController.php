<?php

class FormFieldFigmaMappingController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'view'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update', 'fetchFields', 'saveToMappingList', 'successPage', 'editAllMapping', 'deleteAllMapping', 'editToMappingList'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete'),
                'users' => array('admin'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $model = new FormFieldFigmaMapping;
        $formList = CHtml::listData(ApplicationForms::model()->findAll(), 'id', 'menu_form');
        $formFieldsList = CHtml::listData(FormFields::model()->findAll(), 'FIELD_ID', 'TITLE');
        $formClassList = array('Buttons' => 'Buttons', 'Radio-Button-List' => 'Radio-Button-List', 'Dropdown-List' => 'Dropdown-List', 'Checkbox-List' => 'Checkbox-List', 'Input-Labels' => 'Input-Labels', 'Text-Input-Box' => 'Text-Input-Box');
        $htmlTagList = array('button' => 'button', 'div' => 'div', 'input' => 'input', 'label' => 'label');
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);    

        $this->render('create', array(
            'model' => $model,
            'formList' => $formList,
            'formFieldsList' => $formFieldsList,
            'formClassList' => $formClassList,
            'htmlTagList' => $htmlTagList,
        ));
    }

    public function actionSaveToMappingList() {
        if (isset($_POST['htmlMappingList']) && $_POST['selectedForm']) {
            $elementHtmlMappingList = json_decode($_POST['htmlMappingList']);
            $selectedForm = $_POST['selectedForm'];
        }
        if (isset($_POST['mappingList']) && $_POST['selectedForm']) {
            $elementMappingList = json_decode($_POST['mappingList']);
            $selectedForm = $_POST['selectedForm'];
        }
        foreach ($elementMappingList as $key => $value) {
            $model = new FormFieldFigmaMapping;
            $model->form_id = $selectedForm;
            if (is_numeric($key)) {
                $model->field_id = $key;
                $model->class_name = '';
                $model->html_tag = '';
                $model->flag = 0;
            } else {
                $model->field_id = 0;
                $model->class_name = $key;
                $model->html_tag = '';
                $model->flag = 1;
            }
            if (!$this->combinationValidation($model)) {
                $model->frame_name = $value;
                if (!$model->save()) {
                    // Handle the error here    
                    print_r($model->getErrors());
                }
            } else {
                echo "<script type='text/javascript'>alert('The specified mapping already exists.');</script>";
                throw new CHttpException('The specified mapping already exists.');
            }
        }
        foreach ($elementHtmlMappingList as $key => $value) {
            $model = new FormFieldFigmaMapping;
            $model->form_id = $selectedForm;
            $model->field_id = 0;
            $model->class_name = '';
            $model->html_tag = $key;
            $model->flag = 2;
            if (!$this->combinationValidation($model)) {
                print_r($model);
                $model->frame_name = $value;
                if (!$model->save()) {
                    // Handle the error here
                    print_r($model->getErrors());
                }
            }
        }
//        $this->successPage();
        Yii::app()->end();
    }

    public function actionEditToMappingList() {
        if (isset($_POST['fieldNameUpdate']) || isset($_POST['classNameUpdate']) || isset($_POST['htmlTagUpdate'])) {
            $fieldNameUpdate = $_POST['fieldNameUpdate'];
            $classNameUpdate = $_POST['classNameUpdate'];
            $htmlTagUpdate = $_POST['htmlTagUpdate'];
            $updateModelId = $_POST['updateModelId'];
//            $frameNameUpdate = $_POST['frameNameUpdate'];
        }
        $selectedMapping = FormFieldFigmaMapping::model()->findByPk($updateModelId);
        $selectedCssMappings = FormFieldCsspropertyValueMapping::model()->findAllByAttributes(array('form_id' => $selectedMapping->form_id, 'field_id' => $selectedMapping->field_id, 'class_name' => $selectedMapping->class_name, 'html_tag' => $selectedMapping->html_tag));
        foreach ($selectedCssMappings as $cssMapping) {
            $cssMapping->field_id = $fieldNameUpdate;
            $cssMapping->class_name = $classNameUpdate;
            $cssMapping->html_tag = $htmlTagUpdate;
            if (!$cssMapping->save()) {
                // Handle the error here
                print_r($cssMapping->getErrors());
            }
        }
        $selectedMapping->field_id = $fieldNameUpdate;
        $selectedMapping->class_name = $classNameUpdate;
        $selectedMapping->html_tag = $htmlTagUpdate;
        if ($selectedMapping->html_tag !== ''){
            $selectedMapping->flag = 2;
        }
        else if($selectedMapping->class_name !== ''){
            $selectedMapping->flag = 1;
        }        
        else if($selectedMapping->field_id !== 0){
            $selectedMapping->flag = 0;
        }        
        if (!$selectedMapping->save()) {
            // Handle the error here
            print_r($selectedMapping->getErrors());
        }
        $this->successPage();
        Yii::app()->end();
    }

    public function actionEditAllMapping() {
        if (isset($_POST['editValue'])) {
            $editId = $_POST['editValue'];
        }

        $this->redirect(array('update', 'id' => $editId), array());
    }

    public function actionDeleteAllMapping() {
        if (isset($_POST['deleteValue'])) {
            $modifyId = $_POST['deleteValue'];
        }
        $selectedMapping = FormFieldFigmaMapping::model()->findByPk($modifyId);
        print_r($selectedMapping);
        $selectedCssMappings = FormFieldCsspropertyValueMapping::model()->findAllByAttributes(array('form_id' => $selectedMapping->form_id, 'field_id' => $selectedMapping->field_id, 'class_name' => $selectedMapping->class_name, 'html_tag' => $selectedMapping->html_tag));
        foreach ($selectedCssMappings as $cssMapping) {
            $cssMapping->delete();
        }
        $selectedMapping->delete();
//        $this->actionSuccessPage();
//        Yii::app()->end();
    }

    public function actionSuccessPage() {
        $this->render('successPage');
    }

    public function actionFetchFields($formId) {
        // Fetch all field IDs for the given form ID
        $fieldsForForm = FormFields::model()->findAllByAttributes(array('FORM_ID' => $formId));

        // Extract field IDs from the fetched records
        $fieldIds = array();
        foreach ($fieldsForForm as $field) {
            $fieldIds[] = $field->FIELD_ID; // Assuming FIELD_ID is the attribute representing the field ID
            $fieldNames[] = $field->TITLE;
        }

        // Combine field IDs and field names into a single associative array
        $responseData = array(
            'fieldIds' => $fieldIds,
            'fieldNames' => $fieldNames
        );

        // Return the combined data as JSON
        echo json_encode($responseData);

        // Terminate the application
        Yii::app()->end();
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $formList = CHtml::listData(ApplicationForms::model()->findAll(), 'id', 'menu_form');
        $formFieldsList = CHtml::listData(FormFields::model()->findAll(), 'FIELD_ID', 'TITLE');
        $formClassList = array('Buttons' => 'Buttons', 'Radio-Button-List' => 'Radio-Button-List', 'Dropdown-List' => 'Dropdown-List', 'Checkbox-List' => 'Checkbox-List', 'Input-Labels' => 'Input-Labels', 'Text-Input-Box' => 'Text-Input-Box');
        $htmlTagList = array('button' => 'button', 'div' => 'div', 'input' => 'input', 'label' => 'label');

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['FormFieldFigmaMapping'])) {
            $model->id = $id;
//            $model->form_id = ;
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('update', array(
            'model' => $model,
            'formList' => $formList,
            'formFieldsList' => $formFieldsList,
            'formClassList' => $formClassList,
            'htmlTagList' => $htmlTagList,
            'modelId' => $id,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $dataProvider = new CActiveDataProvider('FormFieldFigmaMapping');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new FormFieldFigmaMapping('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['FormFieldFigmaMapping']))
            $model->attributes = $_GET['FormFieldFigmaMapping'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return FormFieldFigmaMapping the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = FormFieldFigmaMapping::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param FormFieldFigmaMapping $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'form-field-figma-mapping-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    private function combinationValidation($model) {
        // Fetch form_id, field_id, and css_property_id from the POST model
        $formId = $model->form_id;
        $fieldId = $model->field_id;
        $className = $model->class_name;
        $htmlTag = $model->html_tag;

        // Check if the combination already exists in the database
        $existingModel = FormFieldFigmaMapping::model()->findByAttributes(array(
            'form_id' => $formId,
            'field_id' => $fieldId,
            'class_name' => $className,
            'html_tag' => $htmlTag,
        ));

        // If the combination exists, return true
        if ($existingModel !== null) {
            print_r('Exists');
            return true;
            // You can provide options here for update or delete
            // Example: echo "<a href='updateAction'>Update</a> | <a href='deleteAction'>Delete</a>";
        } else {
            print_r('Not Exists');
            return false;
            // Combination doesn't exist, proceed with other actions
            // Maybe save the model or perform further validations
        }
    }
}
