<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

use wkapi\models\Department;
use wkapi\models\DepartmentForm;

class DepartmentController extends BaseController
{
	public $modelClass = 'wkapi\models\Department';

	public function actions(){
		return [
			'index'=>[
				'class'=>'wkapi\actions\IndexAction',
                'modelClass' => $this->modelClass,
			],
		];
	}

	//获取指定组织的所有部门
	public function actionGetOne(){
		$get = $this->get();
		if( isset($get['id']) ){
			$data = Department::findOne(['id' , $get['id']] /*,isset( $get['state'] )? 0 : 1*/);
			if( $data )
				return static::renderJson([$data],1,200);
			else
				return static::renderJson([],0,404,'没有此组织');
		}

		return static::renderJson([],0,310,'参数不合法');
	}

	//添加一个部门
	public function actionAdd(){
		$post = $this->post();
		$model = new DepartmentForm();
		if($model->load($post) && $model->validate()){
			if(  $group = $model->save())
				return static::renderJson([Department::findOne($group->id)],1,200);
				
			return static::renderJson([],0,200,'保存失败！');
		}

		$str = $this->getModelOneStrErrors($model);
		return static::renderJson([],0,310,$str?$str:'参数不合法');
	}

	//删除组织，$post_data['data']是一个一维数组，表示要删除的组织id
	public function actionDel(){
		$post_data = $this->post();
		if( isset($post_data['data']))
			$data = $post_data['data'];
		else 
			return static::renderJson([],0,310,'数据不合法');


		list($max,$min) =  $this->array_deep($data);  	//获取数组的维数
		if(!($max==$min && $max==1)){		//判读是否为一维数组
			return static::renderJson([],0,310,'数据不合法');
		}

		$arr = array_map([$this,'is_id'], $data);   //对传来的数据进行验证（只能是 id）
		if(in_array(0, $arr)){		//判断除了id、email，是否还有其他非法字符
			return static::renderJson([],0,310,'数据不合法');
		}

		$state = \Yii::$app->db->createCommand('UPDATE department SET state=if(state=1,0,1) WHERE id IN ('.implode(', ', $data).')')->execute();

		if($state)
			return static::renderJson([$data],1,200,'删除成功');
	}

	public function actionChange(){
		$get_data = $this->get();
		$post_data = $this->post();
		$key = 'department_id';
		$form = 'DepartmentForm';

		if( !(  isset($get_data[$key]) && isset($post_data[$form])  ) )
			return static::renderJson([],0,310,'数据不合法');

		$department_id = $this->is_id($get_data[$key])?$get_data[$key]:-1;
		$group = Department::findOne($department_id);
		if( !$group )
			return static::renderJson([],0,404,'没有此组织');

		//直接使用组织注册的验证器来验证用户修改信息的输入
		$model = new DepartmentForm();
		//先保存用户源数据，以确保用户未修改的信息不变
		$formdata = $this->
			loadModelValue($group,$form,['user_id','group_id','name','desc',]);
		
		//在把用户要修改的字段及信息到进来
		if($model->load($formdata)){
			$signform = $post_data[$form];
			isset($signform['user_id']) and ($model->user_id = $signform['user_id']);
			isset($signform['group_id']) and ($model->group_id = $signform['group_id']);
			isset($signform['name']) and ($model->name = $signform['name']);
			isset($signform['desc']) and ($model->desc = $signform['desc']);

			$model->validate(); //开始验证

			if( $model->hasErrors() ){
				$str = $this->getModelOneStrErrors($model);
				return static::renderJson([],0,310,$str?$str:'参数不合法');
			}else{
				$group ->user_id = $model->user_id ;
				$group ->group_id = $model->group_id ;
				$group ->name = $model->name  ;
				$group ->desc = $model->desc  ;
				if( $group->save() )
					return static::renderJson([$group]);
			}
		}
	}
}
