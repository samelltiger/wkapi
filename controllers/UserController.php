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

	//通过id或邮箱获取用户
	public function actionGetOne(){
		$get = $this->get();
		if( isset( $get['email'] ) ){
			$data = User::getUserInfo('email' , $get['email'] /*, isset( $get['state'] )? 0 : 1*/);
			if( $data )
				return UserController::renderJson([$data],1,200);
			else
				return UserController::renderJson([],0,404,'没有此用户');
		}

		if( isset( $get['id'] )){
			$data = User::getUserInfo('id' , $get['id'] /*,isset( $get['state'] )? 0 : 1*/);
			if( $data )
				return UserController::renderJson([$data],1,200);
			else
				return UserController::renderJson([],0,404,'没有此用户');
		}

		return UserController::renderJson([],0,310,'参数不合法');
	}

	//添加一个用户
	public function actionAdd(){
		$post = $this->post();
		$model = new SignForm();
		if(!empty($post) && $model->load($post) && $model->validate()){
			if(!$model->save())
				return BaseController::renderJson([],0,200,'保存失败！');
			$data = User::getUserInfo('email' , $model->email );
			return BaseController::renderJson([$data],1,200);
		}

		$str = $this->getModelOneStrErrors($model);
		return BaseController::renderJson([],0,310,$str?$str:'参数不合法');
	}

	//删除用户，$post_data['data']是一个一维数组，表示要删除的用户id或邮箱
	public function actionDel(){
		$post_data = $this->post();
		$data = $post_data['data'];

		list($max,$min) =  $this->array_deep($data);  	//获取数组的维数
		if(!($max==$min && $max==1)){		//判读是否为一维数组
			return BaseController::renderJson([],0,310,'数据不合法');
		}

		$arr = array_map([$this,'is_email_or_id'], $data);//对传来的数据进行验证（只能是 id、email）
		if(in_array(2, $arr)){		//判断除了id、email，是否还有其他非法字符
			return BaseController::renderJson([],0,310,'数据不合法');
		}

		$counts = array_count_values($arr);		//
		
		if(  isset($counts[0])&&$counts[0] == count($arr) ||  isset($counts[1])&&$counts[1] == count($arr) ){
			if(isset($counts[0]) && $counts[0]!=0){
				$state = \Yii::$app->db->createCommand('UPDATE user set state= if(state=1,0,1) where id in ('.implode(', ', $data).')')->execute();
				if($state)
					return BaseController::renderJson([$data],1,200,'删除成功');
			}

			if(isset($counts[1]) && $counts[1]!=0){
				$state = \Yii::$app->db->createCommand("UPDATE user set state= if(state=1,0,1) where email in ('".implode("', '", $data)."')")->execute();
				if($state)
					return BaseController::renderJson([$data],1,200,'删除成功');
			}
			return BaseController::renderJson([],0,311,'操作失败，请勿重复执行该动作');
		}else{
			return BaseController::renderJson([],0,310,'数据不合法');
		}
	}

	public function actionChange(){
		$get_data = $this->get();
		$post_data = $this->post();

		if( !(  isset($get_data['user_id']) && isset($post_data['SignForm'])  ) )
			return BaseController::renderJson([],0,310,'数据不合法');

		$user_id = $get_data['user_id'];
		$user = User::findOne($user_id);
		if( !$user )
			return BaseController::renderJson([],0,404,'没有此用户');

		//直接使用用户注册的验证器来验证用户修改信息的输入
		$model = new SignForm();
		//先保存用户源数据，以确保用户未修改的信息不变
		$formdata = $this->
			loadModelValue($user,'SignForm',['email','username','password','is_admin']);
		
		//在把用户要修改的字段及信息到进来
		if($model->load($formdata)){
			$signform = $post_data['SignForm'];
			isset($signform['email']) and ($model->email = $signform['email']);
			isset($signform['username']) and ($model->username = $signform['username']);
			isset($signform['is_admin']) and ($model->is_admin = $signform['is_admin']);
			isset($signform['password']) and ($model->password = $signform['password']);

			//由于密码长度为6-16，如果用户为修改密码，就会保存数据库中md5的32位密码，所有再次判断，并设一个6-16位之内的密码
			$has_set_password = isset($signform['password']);
			if(!$has_set_password ){	
				$model->password = '1234567';
			}

			if( isset($signform['email']) ){
				if( !$this->is_email( $signform['email'] ) )
					return BaseController::renderJson([],0,310,'邮箱格式不正确');
			}

			$model->validate(); //开始验证

			if( $model->hasErrors() ){  //如果有错误，
				$errors = $model->getErrors();  //获取验证的错误信息
				$has_email_in_errors = array_key_exists('email',$errors); //如果email在错误信息中
				if( $has_email_in_errors && count($errors)==1 ){	//并且错误的个数大于1
					if($user->id == $model->_user->id){
						$user->username = $model->username;
						$user->is_admin = $model->is_admin;
						if( $has_set_password ){	
							$user->password = User::setPassword($model->password);
						}
						if($user->save())
							return BaseController::renderJson([User::findOne($user_id)],1,200);
					}else{
						return BaseController::renderJson([],0,310,'邮箱已被使用');
					}
				}else{
					$str = count($errors)>1 ? (call_user_func( function($errors){
											$i = 0;
											foreach ($errors as $value) {
												if($i++)
													return $value[0];
											}
										},$errors) ) : $this->getModelOneStrErrors($model);
										//错误大于1的话，就获取处理emial的其他的错误

					return BaseController::renderJson([],0,310,$str?$str:'参数不合法');
				}
			}else{
				$user->email = $model->email;
				$user->username = $model->username;
				$user->is_admin = $model->is_admin;
				if( $has_set_password ){	
					$user->password = User::setPassword($model->password);
				}
				if($user->save())
					return BaseController::renderJson([User::findOne($user_id)],1,200);
			}
		}
	}
}