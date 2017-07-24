<?php
namespace wkapi\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use wkapi\models\BTaskForm;
use wkapi\models\Department;
use wkapi\models\UserGroup;

class BTask extends ActiveRecord
{

	public static function tablename(){
		return "b_task";
	}

	public static function getTasksInGroup($group_id){
		$users = UserGroup::find()->select('user_id')->where(['group_id'=>$group_id])->distinct()->all();
		if(!$users)
			return null;
		$ids = ArrayHelper::getColumn($users,'user_id');
		$tasks = static::findAll(['user_id'=>$ids]);
		return $tasks;

	}

	public function getUsers(){
		return $this->hasOne(User::className(),['account_id'=>"id"]);
	}

	public static function getUserInfo( $type , $id){
		$data = Account::find()->where([ $type => $id ])->with("users")->one();
		unset($data['password']);
		return $data;
	}
}

?>