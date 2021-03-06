<?php

class SelfController extends Controller
{
    public $layout = 'login';
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				//mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}
        
	public function actionIndex(){
		if(isset(Yii::app()->session['sign'])){
			if (Yii::app()->session['sign'] == $this->id){
				 $this->redirect(Yii::app()->createUrl('companyself/index'));
			}else{
				$this->redirect(Yii::app()->createUrl($this->id.'/login'));
			}
		}else{
			 $this->redirect(Yii::app()->createUrl($this->id.'/login'));
		}
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm($this->id);
		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			if($_POST['LoginForm']['rememberMe'])
				$_POST['LoginForm']['rememberMe'] = true;
			else
				$_POST['LoginForm']['rememberMe'] = false;
			
			$model->attributes = $_POST['LoginForm'];
			//var_dump($model->attributes); return;
			// validate user input and redirect to the previous page if valid
				if($model->validate() && $model->login()){
					$this->redirect(array("index"));
				}else{
                                    $url = Yii::app()->createUrl('self/login');
                                    echo "<script>alert('密码或用户名有误或在审核中请等待！');location.href='$url'</script>";
                                    
                                }	
		}
		// display the login form
		$this->render('platformLogin',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->createUrl('self/login'));
	}
}
