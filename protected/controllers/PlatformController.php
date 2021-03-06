<?php

class PlatformController extends NController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin_one';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
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
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','uncheck','search','Ispassed','Isrejected','operate'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
        private function check($examine,$operate,$show=true){
            NDataRender::$show_operation = $show;
            $condition = "t.`examine`=$examine";
            $modelName = ucfirst($this->id);
            if(!empty($_POST[$modelName])) {
                 $nsearchcondition = new NSearchConditon($this);
                 $s_condtion = $nsearchcondition->condition;
    //             var_dump($s_condtion);
                 if($s_condtion!='')
                    $condition  = "$s_condtion and t.`examine`=$examine";
    //             var_dump($condition);
            }
            $criteria=new CDbCriteria(array(  
                'condition'=>$condition,  
                'order'=>'t.`created` DESC',  
//                'join'=>'',  
                 'with'=>array('company'),
            )); 
            NDataRender::$dataProviderOptions = array(
                'criteria' => $criteria,
            );
//            return;
            $dataRender = new NDataRender($this,'uncheck',array(
                'show_checkbox_select'=>true,
                'operate'=>$operate,
            ));
//            $data = $dataRender->dataProvider;
        }
        public function actionUncheck(){
            $this->check(0,'examine');
        }
        public function actionIspassed(){
            $this->check(1,'delete',true);
        }
        public function actionIsrejected(){
           $this->check(2,'e_delete');
        }
        /**
         * 批量操作
         */
        public function actionOperate(){
            new NOprerator($this);
        }
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Platform;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Platform'])){     
                      
                        $_POST['Platform']["p_name_pin"] = GlobalFunction::cn2pinyin($_POST['Platform']["p_name"]);//拼音
                        $_POST['Platform']['initial'] = ucfirst(substr($_POST['Platform']["p_name_pin"],0,1));
                        if(is_numeric($_POST['Platform']['initial'])) $_POST['Platform']['initial'] = 0;
                     // $_POST['Platform']['created'] = GlobalFunction::$date;
                     $_POST['Platform']['examine'] = 1;
//                        $_POST['Platform']['city'] .= '-'.$_POST['Platform']['city1'];
                        $model->setAttributes($_POST['Platform'],false);
			if($model->save())
				$this->redirectUser($model->id);
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Platform']))
		{
                       $_POST['Platform']["p_name_pin"] = GlobalFunction::cn2pinyin($_POST['Platform']["p_name"]);//拼音
                        $_POST['Platform']['initial'] = ucfirst(substr($_POST['Platform']["p_name_pin"],0,1));
                        if(is_numeric($_POST['Platform']['initial'])) $_POST['Platform']['initial'] = 0;
//                        $_POST['Platform']['created'] = GlobalFunction::$date;
//                        $_POST['Platform']['examine'] = 1;
//                        $_POST['Platform']['city'] .= '-'.$_POST['Platform']['city1'];
                        $model->setAttributes($_POST['Platform'],false);
//                        var_dump($model->attributes);
//                        return;
			if($model->save())
				$this->redirectUser($model->id);
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->myredirect('uncheck');
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Platform('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Platform']))
			$model->attributes=$_GET['Platform'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Platform the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Platform::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Platform $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='platform-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
