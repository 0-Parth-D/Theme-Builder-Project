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
                'actions' => array('create', 'update', 'fetchFields', 'saveToMappingList', 'successPage'),
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
        print_r($elementHtmlMappingList);
        print_r($elementMappingList);
        foreach ($elementMappingList as $key => $value) {
            $model = new FormFieldFigmaMapping;
            $model->form_id = $selectedForm;
            if (is_numeric($key)) {
                $model->field_id = $key;
                $model->class_name = null;
                $model->flag = 0;
            } else {
                $model->field_id = 0;
                $model->class_name = $key;
                $model->flag = 1;
            }
            $model->frame_name = $value;
            if (!$model->save()) {
                // Handle the error here
                print_r($model->getErrors());
            }
        }
        foreach ($elementHtmlMappingList as $key => $value) {
            $model = new FormFieldFigmaMapping;
            $model->form_id = $selectedForm;
            $model->field_id = 0;
            $model->class_name = null;
            $model->html_tag = $key;
            $model->frame_name = $value;
            $model->flag = 2;
            if (!$model->save()) {
                // Handle the error here
                print_r($model->getErrors());
            }
        }
        Yii::app()->end();
        $this->successPage();
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

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['FormFieldFigmaMapping'])) {
            $model->attributes = $_POST['FormFieldFigmaMapping'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('update', array(
            'model' => $model,
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
}
