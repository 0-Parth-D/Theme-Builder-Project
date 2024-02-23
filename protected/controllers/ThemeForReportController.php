<?php

class ThemeForReportController extends Controller {

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
                'actions' => array('create', 'update', 'reportTheme', 'tabThemeReport', 'saveThemeValues', 'reportTestTheme', 'reportFrameMap'),
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
        $model = new ThemeForReport;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['ThemeForReport'])) {
            $model->attributes = $_POST['ThemeForReport'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionReportFrameMap() {
        $model = new ThemeForReport;

        $elementCssList = CHtml::listData(ReportElementCssSet::model()->findAll(), 'element_id', 'element_id');
        $elementList = CHtml::listData(Elements::model()->findAll(), 'id', 'element_name');
        foreach ($elementCssList as $key => &$value) {
            $value = $elementList[$key];
        }
        unset($value);

        if (isset($_POST['objCss']) && isset($_POST['mappingList'])) {
            $objCss = json_decode($_POST['objCss']);
            $mappingList = (array) json_decode($_POST['mappingList']);
            $mappingList = array_flip($mappingList);
            $themeName = $_POST['themeName'];
//                    print_r($objCss);
//                    print_r($mappingList);
//            print_r($mappingList);

            $model = new ThemeForReport;
            $model->theme_name = $themeName;
            $model->save();

            $referenceId = ThemeForReport::model()->findByAttributes(array('theme_name' => $themeName))->id+1;

            foreach ($objCss as $key => $items) {
                foreach ($items as $item) {
                    $propertyValuePair = explode(':', $item); // Extract css property and value pair

                    if (strpos($propertyValuePair[1], 'url') !== false) { // Add path to /images folder if background is set to image url
                        $propertyValuePair[1] = 'url(images/' . explode('(', $propertyValuePair[1])[1];
                    }
                    if ($propertyValuePair[0] === 'background') { // Add path to /images folder if background is set to image url
                        $propertyValuePair[0] = 'background-color';
                    }

                    $cssPropertiesModel = CssProperties::model()->findByAttributes(array('property_name' => $propertyValuePair[0]));
                    $reportElementModel = -1;
                    
                    if ($cssPropertiesModel !== null && in_array($key, array_keys($mappingList))) {
                        $reportElementModel = ReportElementCssSet::model()->findByAttributes(array('css_property_id' => $cssPropertiesModel->id, 'element_id' => $mappingList[$key]));
                    print_r($cssPropertiesModel->id.' '. $cssPropertiesModel->property_name);
                    print_r($mappingList[$key]. ' </br>');
                    }
                    if ($reportElementModel !== -1 && $reportElementModel !== null) {
                        $model = new ThemeForReport;
                        $model->reference_id = $referenceId;
                        $model->theme_name = $themeName;
                        $model->element_id = $mappingList[$key];
                        $model->css_property_id = $cssPropertiesModel->id;
                        $model->value = $propertyValuePair[1];
                        $model->save();
                    }
                }
            }
        }

        $this->render('reportFrameMapForm', array(
            'model' => $model,
            'elementCssList' => $elementCssList,
        ));
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

        if (isset($_POST['ThemeForReport'])) {
            $model->attributes = $_POST['ThemeForReport'];
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
        $dataProvider = new CActiveDataProvider('ThemeForReport');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new ThemeForReport('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['ThemeForReport']))
            $model->attributes = $_GET['ThemeForReport'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return ThemeForReport the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = ThemeForReport::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param ThemeForReport $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'theme-for-report-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

//        public function actionReportTheme(){
//            
//             $model = new ThemeForReport;
//
//            if (isset($_POST['ThemeForReport'])) {
//                $model->attributes = $_POST['ThemeForReport'];
//
//                if ($model->validate()) {
//                    // Save only the theme_name to the database
//                    if ($this->saveThemeName($model->theme_name)) {
////                       
//                       $reportElementCssSets = ReportElementCssSet::model()->findAll();
//                          foreach ($reportElementCssSets as $reportElementCssSet) {
//                            $themeReportModel = new ThemeForReport;
//                            $themeReportModel->attributes = array(
//                                'theme_name' => $model->theme_name,  // Use $model->theme_name here instead of $themeName
//                                'element_id' => $reportElementCssSet->element_id,
//                                'css_property_id' => $reportElementCssSet->css_property_id,
//                                // You may need to set other attributes or modify as per your needs
//                            );
//                            
//
//            // Save the ThemeForReport model for each pair
//            $themeReportModel->save();
//        }
//                     
//                    }
//                }
//            }
//
//            $this->render('createTheme', array(
//                'model' => $model,
//            ));
//            
//        }
//        protected function saveThemeName($themeName)
//{
//    $themeModel = new ThemeForReport;
//    $themeModel->theme_name = $themeName;
//
//    return $themeModel->save();
//}

    public function actionReportTheme() {
        $model = new ThemeForReport;

        if (isset($_POST['ThemeForReport'])) {
            $model->attributes = $_POST['ThemeForReport'];

            if ($model->validate()) {
                // Save only the theme_name to the database
                if ($this->saveThemeName($model->theme_name)) {
                    $reportElementCssSets = ReportElementCssSet::model()->findAll();

                    $firstInstanceId = null; // Variable to store the id of the first instance

                    foreach ($reportElementCssSets as $reportElementCssSet) {
                        $themeReportModel = new ThemeForReport;
                        $themeReportModel->attributes = array(
                            'theme_name' => $model->theme_name,
                            'element_id' => $reportElementCssSet->element_id,
                            'css_property_id' => $reportElementCssSet->css_property_id,
                                // You may need to set other attributes or modify as per your needs
                        );

                        // If it's the first iteration, save the first instance and store its id
                        if ($firstInstanceId === null) {
                            $themeReportModel->save();
                            $firstInstanceId = $themeReportModel->id;

                            // Set reference_id to the id of the first instance
                            $themeReportModel->reference_id = $firstInstanceId;
                            $themeReportModel->save();
                        } else {
                            // Set reference_id to the id of the first instance
                            $themeReportModel->reference_id = $firstInstanceId;
                            $themeReportModel->save();
                        }
                    }
                    $this->redirect(array('tabThemeReport', 'themeReportId' => $firstInstanceId));
                }
            }
        }

        $this->render('createTheme', array(
            'model' => $model,
        ));
    }

    protected function saveThemeName($themeName) {
        $themeModel = new ThemeForReport;
        $themeModel->theme_name = $themeName;

        return $themeModel->save();
    }

    public function actionTabThemeReport($themeReportId) {
        // Fetch the ThemeForReport model based on the themeReportId
        $themeReportModel = ThemeForReport::model()->findByPk($themeReportId);
        $rerferenceId = $themeReportModel->reference_id;
// Fetch associated element and property sets based on the reference_id
        $associatedSets = ThemeForReport::model()->findAllByAttributes(array('reference_id' => $themeReportId));

        $this->render('reportThemeTab', array(
            'themeReportModel' => $themeReportModel,
            'associatedSets' => $associatedSets,
        ));
    }

    public function actionSaveThemeValues() {
        // Fetch all records for the given reference_id
        $referenceId = $_POST['themeReportId'];
        $themeForReports = ThemeForReport::model()->findAllByAttributes(array('reference_id' => $referenceId));

        // Iterate through the POST data
        foreach ($_POST as $key => $value) {
            // Check if the key corresponds to an element_id_css_property_id pair
            if (preg_match('/^(\d+)_(\d+)$/', $key, $matches)) {
                $elementId = $matches[1];
                $cssPropertyId = $matches[2];

                // Find the corresponding ThemeForReport model
                $themeForReport = $this->findThemeForReport($themeForReports, $elementId, $cssPropertyId);

                // Update the value and save the model
                if ($themeForReport !== null) {
                    $themeForReport->value = $value;
                    $themeForReport->save();
                }
            }
        }
//            $this->redirect(array('tabThemeReport', 'themeReportId' => $_POST['themeReportId']));
        $this->render("successPage");
        // Redirect or do any other necessary actions
        // ...
//     Optionally, you can print or return something
//    print_r($_POST);
//    die();
    }

// Helper function to find the ThemeForReport model for a given element_id and css_property_id
    private function findThemeForReport($themeForReports, $elementId, $cssPropertyId) {
        foreach ($themeForReports as $themeForReport) {
            if ($themeForReport->element_id == $elementId && $themeForReport->css_property_id == $cssPropertyId) {
                return $themeForReport;
            }
        }
        return null;
    }

    public function actionReportTestTheme() {

        $themeReportData = ThemeForReport::model()->findAll();

        $this->render('report', array('Themeforreport' => $themeReportData));
    }

//public function actionSaveThemeValues() {
//    print_r($_POST);  // Print POST data for debugging
//die();
//    if (isset($_POST['values'])) {
//        foreach ($_POST['values'] as $key => $value) {
//            // Extract element_id and css_property_id from the input field name
//            list($element_id, $css_property_id) = explode('_', $key);
//
//            // Find the corresponding record and update the value
//            $themeReportModel = ThemeForReport::model()->findByAttributes(array(
//                'reference_id' => $_POST['themeReportId'],
//                'element_id' => $element_id,
//                'css_property_id' => $css_property_id,
//            ));
//
//            if ($themeReportModel !== null) {
//                $themeReportModel->value = $value;
//                
//                // Debug: Print the values before saving
//                echo "Element ID: $element_id, CSS Property ID: $css_property_id, New Value: $value<br>";
//
//                // Save the changes
//                if ($themeReportModel->save()) {
//                    echo "Record updated successfully<br>";
//                } else {
//                    echo "Failed to update record<br>";
//                    print_r($themeReportModel->getErrors()); // Display errors, if any
//                    die(); // Stop execution for debugging
//                }
//            } else {
//                echo "Record not found for Element ID: $element_id, CSS Property ID: $css_property_id<br>";
//            }
//        }
//    }
//
//    // Redirect to the tab view page
//    $this->redirect(array('tabThemeReport', 'themeReportId' => $_POST['themeReportId']));
//}
//      public function actionSaveThemeValues() {
//    if (isset($_POST['themeReportId'])) {
//        $referenceId = $_POST['themeReportId'];
//
//        // Fetch all records for the given reference_id
//        $themeForReports = ThemeForReport::model()->findAllByAttributes(array('reference_id' => $referenceId));
//
//        // Iterate through the fetched records and update them
//        foreach ($themeForReports as $themeReportModel) {
//            // Check if the input field name matches the pattern "{theme_name} - {element_id}_{css_property_id}"
//            $key = $themeReportModel->theme_name . ' - ' . $themeReportModel->element_id . '_' . $themeReportModel->css_property_id . '_value';
//
//            if (isset($_POST['theme_values'][$key])) {
//                $value = $_POST['theme_values'][$key];
//                $themeReportModel->value = $value;
//
//                // Save the changes
//                if ($themeReportModel->save()) {
//                    
////                    print_r($themeReportModel);
////                    die();
//                    echo "Record updated successfully<br>";
//                } else {
//                    echo "Failed to update record<br>";
//                    print_r($themeReportModel->getErrors()); // Display errors, if any
//                    die(); // Stop execution for debugging
//                }
//            }
//        }
//
//        // Continue with the rest of your logic or redirect as needed
//    }
//}


    private function combinationValidation($model) {
        // Fetch form_id, field_id, and css_property_id from the POST model
        $themeName = $model->theme_name;
        $elementId = $model->element_id;
        $cssPropertyId = $model->css_property_id;

        // Check if the combination already exists in the database
        $existingModel = ThemeForReport::model()->findByAttributes(array(
            'theme_name' => $themeName,
            'element_id' => $elementId,
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
}
