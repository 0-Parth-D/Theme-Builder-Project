<?php

/**
 * This is the model class for table "form_field_figma_mapping".
 *
 * The followings are the available columns in table 'form_field_figma_mapping':
 * @property integer $id
 * @property integer $form_id
 * @property integer $field_id
 * @property string $class_name
 * @property string $html_tag
 * @property string $frame_name
 * @property integer $flag
 */
class FormFieldFigmaMapping extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'form_field_figma_mapping';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
//			array('form_id, field_id, class_name, html_tag, frame_name, flag', 'required'),
			array('form_id, field_id, flag', 'numerical', 'integerOnly'=>true),
			array('class_name, html_tag, frame_name', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, form_id, field_id, class_name, html_tag, frame_name, flag', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'form_id' => 'Form',
			'field_id' => 'Field',
			'class_name' => 'Class Name',
			'html_tag' => 'Html Tag',
			'frame_name' => 'Frame Name',
			'flag' => 'Flag',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('form_id',$this->form_id);
		$criteria->compare('field_id',$this->field_id);
		$criteria->compare('class_name',$this->class_name,true);
		$criteria->compare('html_tag',$this->html_tag,true);
		$criteria->compare('frame_name',$this->frame_name,true);
		$criteria->compare('flag',$this->flag);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return FormFieldFigmaMapping the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
