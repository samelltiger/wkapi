<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

use wkapi\models\UserGroup;
use wkapi\models\Group;
use wkapi\models\User;
use wkapi\models\UserGroupForm;

class UserGroupController extends BaseController
{
	public $modelClass = 'wkapi\models\UserGroup';

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

		if( isset( $get['id'] ) && isset($get['type'] ) ){
			if( $this->is_id($get['id']) && $get['type']==='user' ){
				$items1 = UserGroup::getUserOfGroups($get['id']);
				$items0 = UserGroup::getUserOfGroups($get['id'],0);
				if($items0||$items1)
					return static::renderJson([$items0,$items1]);
				else
					return static::renderJson([],1,201,'此用户未加入任何组织');

			}elseif( $this->is_id($get['id']) && $get['type']==='group' ){
				$items1 = UserGroup::getUsersInGroup($get['id']);
				$items0 = UserGroup::getUsersInGroup($get['id'],0);
				if($items1||$items0)
					return static::renderJson([$items0,$items1]);
				else
					return static::renderJson([],1,201,'此组织没有任何用户');
			}

		}elseif ( isset( $get['id'] ) && isset($get['group_id'] )) {
			if( $this->is_id($get['id']) && $this->is_id( $get['group_id']) ){
				$item = UserGroup::getUserInGroup( $get['group_id'] , $get['id'] );
				if($item)
					return static::renderJson([$item]);
				else
					return static::renderJson([],1,201,'此组织没有该用户');
			}
		}else{
			return static::renderJson([],0,310,'参数不合法');
		}
	}

	//把某用户添加到组织
	public function actionAdd(){
		$post = $this->post();
		$model = new UserGroupForm();
		if($model->load($post) && $model->validate()){
			if( !UserGroup::findOne(['user_id'=>$model->user_id,'group_id'=>$model->group_id]) && $group = $model->save())
				return static::renderJson([UserGroup::findOne($group->id)],1,200);
				
			return static::renderJson([],0,200,'保存失败！,请勿重复保存');
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
			\Yii::$app->db->createCommand('UPDATE user_group SET state=if(state=1,0,1) WHERE user_id='.$v[0].' AND group_id='.$v[1])->execute();
		}
		
		return static::renderJson([$data],1,200,'删除成功');
	}

	public function actionChange(){
		$post_data = $this->post();

		if( !(  isset($post_data['user_id']) && isset($post_data['group_id']) && isset($post_data['new_id']) ) )
			return static::renderJson([],0,310,'数据不合法');

		if( !$this->is_id($post_data['new_id']) ) 
			return static::renderJson([],0,310,'数据不合法');

		$user_id = $this->is_id($post_data['user_id'])?$post_data['user_id']:-1;
		$group_id = $this->is_id($post_data['group_id'])?$post_data['group_id']:-1;
		$new_id = $post_data['new_id'];
		$group = UserGroup::findOne(['user_id'=>$user_id,'group_id'=>$group_id]);
		if( !$group )
			return static::renderJson([],0,404,'该组织没有该用户');

		if( User::findOne($new_id) ){
			$group->user_id = $new_id;
			if($group->save())
				return static::renderJson([$group]);
			return static::renderJson([$group],0,311,'保存失败！');
		}else{
			return static::renderJson([],0,310,'您指定的用户不存在，请新选择');
		}
	}
}
