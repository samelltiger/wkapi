<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

use wkapi\models\Group;
use wkapi\models\GroupForm;

class GroupController extends BaseController
{
	public $modelClass = 'wkapi\models\Group';

	//通过id获取一个组织
	public function actionGetOne(){
		$get = $this->get();
		if( isset( $get['id'] ) ){
			$data = Group::findOne(['id' , $get['id']] /*,isset( $get['state'] )? 0 : 1*/);
			// print_r($data);die;
			if( $data )
				return static::renderJson([$data],1,200);
			else
				return static::renderJson([],0,404,'没有此组织');
		}

		return static::renderJson([],0,310,'参数不合法');
	}

	//添加一个组织
	public function actionAdd(){
		$post = $this->post();
		$model = new GroupForm();
		if($model->load($post) && $model->validate()){
			if(  $group = $model->save())
				return static::renderJson([Group::findOne($group->id)],1,200);
				
			return static::renderJson([],0,200,'保存失败！');
		}

		$str = $this->getModelOneStrErrors($model);
		return static::renderJson([],0,310,$str?$str:'参数不合法');
	}

	//删除组织，$post_data['data']是一个一维数组，表示要删除的组织id
	public function actionDel(){
		$post_data = $this->post();
		$data = $post_data['data'];

		list($max,$min) =  $this->array_deep($data);  	//获取数组的维数
		if(!($max==$min && $max==1)){		//判读是否为一维数组
			return static::renderJson([],0,310,'数据不合法');
		}

		$arr = array_map([$this,'is_id'], $data);//对传来的数据进行验证（只能是 id、email）
		if(in_array(0, $arr)){		//判断除了id、email，是否还有其他非法字符
			return static::renderJson([],0,310,'数据不合法');
		}

		$state = \Yii::$app->db->createCommand('UPDATE groups SET state=if(state=1,0,1) WHERE id IN ('.implode(', ', $data).')')->execute();

		if($state)
			return static::renderJson([$data],1,200,'删除成功');
	}

	public function actionChange(){
		$get_data = $this->get();
		$post_data = $this->post();

		if( !(  isset($get_data['group_id']) && isset($post_data['GroupForm'])  ) )
			return static::renderJson([],0,310,'数据不合法');

		$group_id = $this->is_id($get_data['group_id'])?$get_data['group_id']:-1;
		$group = Group::findOne($group_id);
		if( !$group )
			return static::renderJson([],0,404,'没有此组织');

		//直接使用组织注册的验证器来验证用户修改信息的输入
		$model = new GroupForm();
		//先保存用户源数据，以确保用户未修改的信息不变
		$formdata = $this->
			loadModelValue($group,'GroupForm',['user_id','name','desc',]);
		
		//在把用户要修改的字段及信息到进来
		if($model->load($formdata)){
			$signform = $post_data['GroupForm'];
			isset($signform['user_id']) and ($model->user_id = $signform['user_id']);
			isset($signform['name']) and ($model->name = $signform['name']);
			isset($signform['desc']) and ($model->desc = $signform['desc']);

			$model->validate(); //开始验证

			if( $model->hasErrors() ){
				$str = $this->getModelOneStrErrors($model);
				return static::renderJson([],0,310,$str?$str:'参数不合法');
			}else{
				$group ->user_id = $model->user_id ;
				$group ->name = $model->name  ;
				$group ->desc = $model->desc  ;
				if( $group->save() )
					return static::renderJson([$group]);
			}


			// if( $model->hasErrors() ){  //如果有错误，
			// 	$errors = $model->getErrors();  //获取验证的错误信息
			// 	$has_email_in_errors = array_key_exists('email',$errors); //如果email在错误信息中
			// 	if( $has_email_in_errors && count($errors)==1 ){	//并且错误的个数大于1
			// 		if($user->id == $model->_user->id){
			// 			$user->username = $model->username;
			// 			$user->is_admin = $model->is_admin;
			// 			if( $has_set_password ){	
			// 				$user->password = User::setPassword($model->password);
			// 			}
			// 			if($user->save())
			// 				return static::renderJson([User::findOne($user_id)],1,200);
			// 		}else{
			// 			return static::renderJson([],0,310,'邮箱已被使用');
			// 		}
			// 	}else{
			// 		$str = count($errors)>1 ? (call_user_func( function($errors){
			// 								$i = 0;
			// 								foreach ($errors as $value) {
			// 									if($i++)
			// 										return $value[0];
			// 								}
			// 							},$errors) ) : $this->getModelOneStrErrors($model);
			// 							//错误大于1的话，就获取处理emial的其他的错误

			// 		return static::renderJson([],0,310,$str?$str:'参数不合法');
			// 	}
			// }else{
			// 	$user->email = $model->email;
			// 	$user->username = $model->username;
			// 	$user->is_admin = $model->is_admin;
			// 	if( $has_set_password ){	
			// 		$user->password = User::setPassword($model->password);
			// 	}
			// 	if($user->save())
			// 		return static::renderJson([User::findOne($user_id)],1,200);
			// }
		}
	}
}
