<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

use wkapi\models\UserDepartment;
use wkapi\models\User;
use wkapi\models\Department;
use wkapi\models\UserDepartmentForm;

class UserDepartmentController extends BaseController
{
	public $modelClass = 'wkapi\models\UserDepartment';
	
	public function actions(){
		return [
			'index'=>[
				'class'=>'wkapi\actions\Testa',
                'modelClass' => $this->modelClass,
			],
		];
	}


	//通过用户和组织相关信息
	public function actionGetOne(){
		$get = $this->get();

		if( isset( $get['id'] ) && isset($get['department_id']) || isset( $get['id'] ) && isset($get['user_id'] ) ){
			if( isset( $get['id'] ) && isset($get['department_id'])&&$this->is_id($get['id']) && $this->is_id($get['department_id']) ){
				$items1 = UserDepartment::getUsersInDepartment($get['id'],$get['department_id']);
				$items0 = UserDepartment::getUsersInDepartment($get['id'],$get['department_id'],0);
				if($items0||$items1)
					return static::renderJson([$items0,$items1]);
				else
					return static::renderJson([],0,201,'该部门没有任何用户');

			}elseif( isset( $get['id'] ) && isset($get['user_id']) && 
				$this->is_id($get['id']) && $this->is_id($get['user_id']) ){
				$user_department = UserDepartment::
					findOne(['group_id'=>$get['id'] , 'user_id'=> $get['user_id']] );
				$default_user =  Department::findAll(
					['user_id'=>$get['user_id'],'group_id'=>$get['id'] ] );

				if( !($user_department || $default_user) )
					return static::renderJson([],1,201,'此用户还未加入任何部门');

				$department =null;
				if( $user_department )
					$department = Department::
						findOne( [ 'id'=>$user_department->department_id ]);

				if( $department || $default_user )
					return static::renderJson([$department , $default_user]);
				else
					return static::renderJson([],1,201,'此用户还未加入任何部门');
			}

		}else{
			return static::renderJson([],0,310,'参数不合法');
		}
	}

	//把某用户添加到组织
	public function actionAdd(){
		$post = $this->post();
		$model = new UserDepartmentForm();
		if($model->load($post) && $model->validate()){
			if( !UserDepartment::findOne([
						'user_id'=>$model->user_id,
						'group_id'=>$model->group_id,
						'department_id'=>$model->department_id
					]) && ( $group = $model->save() ))
				return static::renderJson(
					[UserDepartment::findOne($group->id)],1,200);
				
			return static::renderJson([],0,200,'保存失败！数据已存在');
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

		if(!($max==$min && $max==2)){		//判读是否为二维数组
			return static::renderJson([],0,310,'数据不合法');
		}

		$is_all = $this->is_id_map($data);   //对传来的数据进行验证（只能是 id）
		if( !$is_all ){		//判断二维数组是否都为id
			return static::renderJson([],0,310,'数据不合法');
		}

		foreach ($data as $v) {
			$sql = 'UPDATE user_department SET state=if(state=1,0,1) WHERE user_id='.$v[0].' AND group_id='.$v[1].' AND department_id='.$v[2];
			// echo $sql;die;
			\Yii::$app->db->createCommand($sql)->execute();
		}
		
		return static::renderJson([$data],1,200,'删除成功');
	}

	public function actionChange(){
		$post_data = $this->post();

		if( !(  isset($post_data['user_id']) && isset($post_data['group_id']) && isset($post_data['department_id']) ) )
			return static::renderJson([],0,310,'数据不合法');

		if( !($this->is_id($post_data['user_id']) && $this->is_id($post_data['group_id']) && $this->is_id($post_data['department_id']) )) 
			return static::renderJson([],0,310,'数据不合法');

		
		$group = UserDepartment::findOne(['user_id'=>$post_data['user_id'],'group_id'=>$post_data['group_id'],'department_id'=>$post_data['department_id']]);
		if( !$group )
			return static::renderJson([],0,404,'该组织没有该用户');

		$loaddata = $this->loadModelValue(
			$group,'UserDepartmentForm',['user_id','group_id','department_id']);

		$nu_id = isset($post_data['new_user_id']) ? $post_data['new_user_id'] : false;
		$ng_id = isset($post_data['new_group_id']) ? $post_data['new_group_id'] : false;
		$nd_id = isset($post_data['new_department_id']) 
				? $post_data['new_department_id'] : false;

		$model = new UserDepartmentForm();
		$model->load($loaddata);

		$nu_id = ($nu_id && $this->is_id( $nu_id ) ? ( $model->user_id=	(int)$nu_id )  : false );
		$ng_id = ($ng_id && $this->is_id( $ng_id ) ? ( $model->group_id= 	(int)$ng_id )  :  false );
		$nd_id = ($nd_id && $this->is_id( $nd_id ) ? ( $model->department_id = 
						(int)$nd_id )  :  false );

		if( $model->validate() ){
				$nu_id ? ($group->user_id = $model->user_id) :'' ;
				$ng_id ? ($group->group_id = $model->group_id) :'' ;
				$nd_id ? ($group->department_id = $model->department_id) :'' ;
				if( !UserDepartment::findOne([
						'user_id'=> $model->user_id ,
						'group_id'=> $model->group_id ,
						'department_id'=> $model->department_id 
					]) && $group->save() )
					return static::renderJson([$group]);

				return static::renderJson([],0,311,'操作失败！');
		}

		$str = $this->getModelOneStrErrors($model);
		return static::renderJson([],0,310,$str?$str:'参数不合法');
	}
}

