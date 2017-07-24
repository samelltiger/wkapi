<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

use wkapi\models\BTask;
use wkapi\models\BTaskForm;
use wkapi\models\STask;

class BTaskController extends BaseController
{
	public $modelClass = 'wkapi\models\BTask';

	public function actions(){
		return [
			'index'=>[
				'class'=>'wkapi\actions\IndexAction',
                'modelClass' => $this->modelClass,
			],
		];
	}

	
	//获取指定组织的所有任务
	public function actionGetOne(){
		$get = $this->get();
		$modelClass = $this->modelClass;

		if( isset($get['id']) && !isset($get['type'])){
			$task = $modelClass::findOne(['id'=>$get['id']]);
			if($task)
				return static::renderJson([$task]);
			return static::renderJson([],0,404,'未找到该任务');

		}elseif (isset($get['id']) && isset($get['type']) && $get['type']==='user'&& isset($get['group_id']) && $this->is_id($get['group_id'])) {
			$tasks = $modelClass::findAll(['user_id'=>$get['id'],"group_id"=>$get['group_id']]);
			if($tasks)
				return static::renderJson([$tasks]);
			return static::renderJson([],0,404,'未找到此组织中该用户的任务');

		}elseif (isset($get['id']) && isset($get['type']) && $get['type']==='group') {
			$tasks = BTask::getTasksInGroup((int)$get['id']);
			if($tasks)
				return static::renderJson([$tasks]);
			return static::renderJson([],0,404,'未找到该组织内用户的任务');

		}

		return static::renderJson([],0,310,'参数不合法');
	}

	//添加一个任务
	public function actionAdd(){
		$post = $this->post();
		$model = new BTaskForm();
		if($model->load($post) && $model->validate()){
			if(  $group = $model->save() )
				return static::renderJson([BTask::findOne($group->id)]);
				
			return static::renderJson([],0,200,'保存失败！');
		}

		$str = $this->getModelOneStrErrors($model);
		return static::renderJson([],0,310,$str?$str:'参数不合法');
	}

	//删除任务，$post_data['data']是一个一维数组，表示要删除的任务id
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
		if(in_array(0, $arr)){		//判断id
			return static::renderJson([],0,310,'数据不合法');
		}

		// $ids = STask::find()->select('') ;
		// print_r($ids);
		$state = \Yii::$app->db->createCommand('UPDATE b_task SET state=if(state=1,0,1) WHERE id IN ('.implode(', ', $data).')')->execute();
		$state = \Yii::$app->db->createCommand('UPDATE s_task SET state=if(state=1,0,1) WHERE b_task_id IN ('.implode(', ', $data).')')->execute();

		if($state)
			return static::renderJson([$data],1,200,'删除成功');
	}
	
	public function actionChange(){
		$get_data = $this->get();
		$post_data = $this->post();
		$key = 'btask_id';
		$form = 'BTaskForm';

		if( !(  isset($get_data[$key]) && isset($post_data[$form])  ) )
			return static::renderJson([],0,310,'数据不合法');

		$department_id = $this->is_id($get_data[$key])?$get_data[$key]:-1;
		$group = BTask::findOne($department_id);
		if( !$group )
			return static::renderJson([],0,404,'没有此组织');

		//直接使用组织注册的验证器来验证用户修改信息的输入
		$model = new BTaskForm();
		//先保存用户源数据，以确保用户未修改的信息不变
		$formdata = $this->
			loadModelValue($group,$form,['name','end_date','is_finished','finished_time','user_id','type_id','group_id','desc','state',]);
		
		//在把用户要修改的字段及信息到进来
		if($model->load($formdata)){
			$signform = $post_data[$form];
			isset($signform['name']) and ($model->name = $signform['name']);
			isset($signform['end_date']) and ($model->end_date = $signform['end_date']);
			isset($signform['is_finished']) and ($model->is_finished = $signform['is_finished']);
			isset($signform['finished_time']) and ($model->finished_time = $signform['finished_time']);
			isset($signform['user_id']) and ($model->user_id = $signform['user_id']);
			isset($signform['type_id']) and ($model->type_id = $signform['type_id']);
			isset($signform['state']) and ($model->state = $signform['state']);
			isset($signform['desc']) and ($model->desc = $signform['desc']);

			$model->validate(); //开始验证

			if( $model->hasErrors() ){
				$str = $this->getModelOneStrErrors($model);
				return static::renderJson([],0,310,$str?$str:'参数不合法');
			}else{
				$group ->name = $model->name ;
				$group ->end_date = $model->end_date ;
				$group ->is_finished = (int)$model->is_finished  ;
				$group ->finished_time = $model->finished_time  ;
				$group ->user_id = (int)$model->user_id  ;
				$group ->type_id = (int)$model->type_id  ;
				$group ->state = (int)$model->state  ;
				$group ->desc = $model->desc  ;
				// print_r($group);die;
				if( $group->save() )
					return static::renderJson([$group]);
				return static::renderJson([],0,310,'修改失败，请稍后再试');
			}
		}
	}
}
