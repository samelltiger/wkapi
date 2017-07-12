<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
// use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

use wkapi\models\UserIdentity;
use wkapi\models\SignForm;
use wkapi\models\User;

class UserController extends BaseController
{
	public $modelClass = 'wkapi\models\User';


	public function actionGetOne(){
		$get = $this->get();
		if( isset( $get['email'] ) ){
				$data = User::getUserInfo('email' , $get['email'] );
				if( $data )
					return UserController::renderJson([$data],1,200);
		}
		return UserController::renderJson([],0,210,'参数不合法');
	}

	public function actionSignin(){
		$post = $this->post();
		$model = new SignForm();
		if(!empty($post) && $model->load($post) && $model->validate()){
			if(!$model->save())
				return BaseController::renderJson([],0,200,'保存失败！');
			$data = User::getUserInfo('email' , $model->email );
			return BaseController::renderJson([$data],1,200);

		}
		return BaseController::renderJson([],0,210,$this->getModelOneStrErrors($model));
	}

	// public function behaviors(){
	// 	return ArrayHelper::merge(parent::behaviors(),[
	// 		'authenticator' => [
	// 				'class' =>QueryParamAuth::className(),
	// 				// 'allowActions' =>[
	// 				// 	'login',
	// 				// 	'signup-test',
	// 				// ],
	// 			],
	// 		]);
	// }

	public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            // 'captcha' => [
            //     'class' => 'yii\captcha\CaptchaAction',
            //     'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            // ],
        ];
    }

	// public function actionTest(){
	// 	return ['content'=>Yii::$app->request->post('login')['email']];
	// }

	// public function actionSignin(){
	// 	// if( !\Yii::$app->user->isGuest )
	// 	// 	return ['已登陆'];
	// 	$model = new SigninForm();
	// 	$params = $this->post('signin');
	// 	if( !empty( $params ) )
	// 	{
	// 		$model ->email = $params['email'];
	// 		$model ->username = 
	// 			\str_replace([" ","\t","\n","\r"],"",$params['username']);
	// 		$model ->password = $params['password'];
	// 		if( $user = $model->sava() ){
	// 			unset($user[0]['password']);
	// 			return $this->renderJson($user,1,201);
	// 		}
	// 	}

	// 	if( $model->hasErrors() ){
	// 		$message = $model->getErrors();
	// 		foreach ($message as $value) {
	// 			$msg=$value;
	// 		}
	// 	}
	// 	return $this->renderJson([],0,302,isset($msg) ? $msg : null);
	// }

	// public function actionLogin(){
	// 	$params = $this->post("login");
	// 	$model = new LoginForm();

	// 	if(!empty($params)&&isset($params['loginway'])){
	// 		$model->loginway = $params['loginway']=="username" ? "username" : "email";
	// 		$model->setScenario( $model->loginway );
	// 		$model->username = isset($params['username'])&&
	// 			!empty($params['username']) ? $params['username'] : null;
	// 		$model->email = isset($params['email'])&&
	// 			!empty($params['email'])  ? $params['email'] : null;
	// 		$model->password = isset($params['password'])&&
	// 			!empty($params['password'])  ? $params['password'] : null;
	// 	}else{
	// 		return $this->renderJson([],0,301,'传递的参数有误');
	// 	}

	// 	if( $model->validate() ){
	// 		$account = $model->_account;
	// 		$data = Account::getUserInfo('id' , $account['id'] );
	// 		return $this->renderJson([$data,$data->users] , 1 , 200 , '');
	// 	}

	// 	if( $model->hasErrors() ){
	// 		$message = $model->getErrors();
	// 		foreach ($message as $value) {
	// 			$msg=$value;
	// 		}
	// 	}
	// 	return $this->renderJson([],0,302,isset($msg) ? $msg : null);
	// }

	// public function actionGetAll(){
	// 	return ['aaa'];
	// }
}