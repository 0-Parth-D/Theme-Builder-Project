<?php

class FormFieldCsspropertyValueMappingController extends Controller {

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
                'actions' => array('create', 'update', 'applyStylesToFormElement', 'passingPostToParser'),
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
        $model = new FormFieldCsspropertyValueMapping;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['FormFieldCsspropertyValueMapping'])) {

            $this->actionPassingPostToParser();
//			$model->attributes=$_POST['FormFieldCsspropertyValueMapping'];
//			if($model->save())
            $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    // Function to map individual css properties to their respective frame mappings
    public function actionPassingPostToParser() {
        if (isset($_POST['objCss'])) {
            $value = json_decode($_POST['objCss']);
        }
//        print_r($value);
        
        foreach ($value as $key => $items) {
            foreach ($items as $item) {
                $propertyValuePair = explode(':', $item); // Extract css property and value pair
                
                if (strpos($propertyValuePair[1], 'url') !== false) { // Add path to /images folder if background is set to image url
                    $propertyValuePair[1] = 'url(images/' . explode('(', $propertyValuePair[1])[1];
                }
                
                // List the existing supported css properties and mappings from their tables
                $cssPropertiesModel = CssProperties::model()->find('property_name=:property_name', array(':property_name' => $propertyValuePair[0]));
                $formFieldFigmaMapping = FormFieldFigmaMapping::model()->find('frame_name=:frame_name', array(':frame_name' => $key));

                // If both exist, create a form entry for each of the css property mapped to the field
                if ($cssPropertiesModel !== null) {
                    $model = new FormFieldCsspropertyValueMapping;
                    if (isset($formFieldFigmaMapping->form_id)) {
                        $model->form_id = $formFieldFigmaMapping->form_id;
                        $model->field_id = $formFieldFigmaMapping->field_id;
                        $model->class_name = $formFieldFigmaMapping->class_name;
                        $model->html_tag = $formFieldFigmaMapping->html_tag;
                        $model->css_property_id = $cssPropertiesModel->id;
                        $model->value = $propertyValuePair[1];
                        if (!$this->combinationValidation($model)) {
                            $model->save();
                        }
                    }
                } else { // If css property does not exist, add to form_field_drop_css_property table
                    $existingEntry = FormFieldDropCssProperty::model()->find('property=:property AND element=:element', array(':property' => $propertyValuePair[0], ':element' => explode('_', $key)[1]));

                    $dropProperty = new FormFieldDropCssProperty;
                    $dropProperty->property = $propertyValuePair[0];
                    $dropProperty->element = explode('_', $key)[1];
                    if ($existingEntry === null) {
                        if (!$dropProperty->save()) {
                            // Handle the error here
                            print_r($dropProperty->getErrors());
                        }
                    }
                }
            }
        }
        Yii::app()->end();
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

    private function combinationValidation($model) {
        // Fetch form_id, field_id, and css_property_id from the POST model
        $formId = $model->form_id;
        $fieldId = $model->field_id;
        $className = $model->class_name;
        $htmlTag = $model->html_tag;
        $cssPropertyId = $model->css_property_id;

        // Check if the combination already exists in the database
        $existingModel = FormFieldCsspropertyValueMapping::model()->findByAttributes(array(
            'form_id' => $formId,
            'field_id' => $fieldId,
            'class_name' => $className,
            'html_tag' => $htmlTag,
            'css_property_id' => $cssPropertyId,
        ));

        // If the combination exists, return true
        if ($existingModel !== null) {
            return true;
            // You can provide options here for update or delete
            // Example: echo "<a href='updateAction'>Update</a> | <a href='deleteAction'>Delete</a>";
        } else {
            return false;
            // Combination doesn't exist, proceed with other actions
            // Maybe save the model or perform further validations
        }
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

        if (isset($_POST['FormFieldCsspropertyValueMapping'])) {
            $model->attributes = $_POST['FormFieldCsspropertyValueMapping'];
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
        $dataProvider = new CActiveDataProvider('FormFieldCsspropertyValueMapping');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new FormFieldCsspropertyValueMapping('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['FormFieldCsspropertyValueMapping']))
            $model->attributes = $_GET['FormFieldCsspropertyValueMapping'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return FormFieldCsspropertyValueMapping the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = FormFieldCsspropertyValueMapping::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param FormFieldCsspropertyValueMapping $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'form-field-cssproperty-value-mapping-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionApplyStylesToFormElement($formID) {

        // Find the form ID
        $formMapping = Forms::model()->findByAttributes(array('FORM_NAME' => $formID));

        $formId = $formMapping->FORM_ID;

        // Find the CSS properties for the given form ID
        $formFieldStyles = FormFieldCsspropertyValueMapping::model()->findAllByAttributes(array('form_id' => $formId));

        // Array to store CSS properties for each form element
        $cssStyles = array();

        // Iterate over each CSS property entry
        foreach ($formFieldStyles as $formFieldStyle) {
            // Store CSS property for each form element
            $fieldId = $formFieldStyle->field_id;
            $className = $formFieldStyle->class_name;
            $htmlTag = $formFieldStyle->html_tag;
            if ($fieldId != 0) {
                $identifier = "#";
                $selector = "field_" . $fieldId;
            } else if ($className != null) {
                $identifier = ".";
                $selector = $className;
            } else if ($htmlTag != null) {
                $identifier = "";
                $selector = $htmlTag;
            }
            $cssPropertyId = $formFieldStyle->css_property_id;
            $cssProperty = CssProperties::model()->findByPk($cssPropertyId)->property_name;
            $value = $formFieldStyle->value;

            // Create CSS rule and add it to the array
            $cssStyles[] = $identifier . $selector . "{" . $cssProperty . ":" . $value . "}";
        }

        // Convert the CSS properties array to a string
        $cssStylesString = implode(' ', $cssStyles);

        // Return the CSS styles in the JSON response
        echo CJSON::encode($cssStylesString);
        print_r($cssStylesString);
        Yii::app()->end();
    }
}
