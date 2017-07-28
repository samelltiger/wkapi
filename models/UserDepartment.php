<?php
namespace wkapi\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class UserDepartment extends ActiveRecord
{
	
	public static function tablename(){
		return "user_department";
	}

	public static function getUsersInDepartment($group_id,$department_id,$state=1){
		$items = static::findAll(['group_id'=>(int)$group_id,'department_id'=>(int)$department_id,'state'=>(int)$state]);
		if( !$items )
			return null;
		$ids = ArrayHelper::getColumn($items,'user_id');
		$data = User::find()->select('id,username,email')->where(['id'=>$ids])->all();
		return $data;
	}

	public static function getUserOfDepartment($group_id , $user_id , $state=1){
		$items = static::findAll(['group_id'=>$group_id ,'state'=>$state]);
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
}


?>