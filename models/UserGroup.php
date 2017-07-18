<?php
namespace wkapi\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseArrayHelper;

use wkapi\models\Group;
use wkapi\models\User;


class UserGroup extends ActiveRecord
{
	
	public static function tablename(){
		return "user_group";
	}

	//获取指定用户所在的所有组织信息
	public static function getUserOfGroups($user_id){
		$items = static::findAll(['user_id'=>$user_id]);
		if( !$items )
			return null;
		$ids = ArrayHelper::getColumn($items,'group_id');
		$data = Group::findAll($ids);
		return $data;
	}

	//获取指定组织的所有用户信息
	public static function getUsersInGroup($group_id){
		$items = static::findAll(['group_id'=>$group_id]);
		if( !$items )
			return null;
		$ids = ArrayHelper::getColumn($items,'user_id');
		$data = User::findAll(['id'=>$ids]);
		return array_map(function($v){
			unset($v->password);
			unset($v->token);
			unset($v->is_admin);
			return $v;
		}, $data);
	}

	//获取指定指定组织信息及指定用户信息
	public static function getUserInGroup($group_id , $user_id){
		$item = static::findOne(['user_id'=>$user_id,'group_id'=>$group_id]);
		if( !$item )
			return null;
		$data = [];
		$user = User::findOne($user_id);
		if($user){
			unset($user->password);
			unset($user->token);
			unset($user->is_admin);
			array_push($data, $user);
		}else{
			return null;
		}

		$group = Group::findOne($group_id);
		if($group){
			array_push($data, $user);
			return $data;
		}
		return null;

	}
	
}


?>