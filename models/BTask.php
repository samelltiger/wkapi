<?php
namespace wkapi\models;

use Yii;
use yii\db\ActiveRecord;
use wkapi\models\UserIdenti;
use wkapi\models\User;

class BTask extends ActiveRecord
{

	public static function tablename(){
		return "b_task";
	}

	public function verifypassword($attribute,$param){
		if(!$this->hasErrors()){
			$this->getAccount();
			if(!$this->_account || !$this->validatePassword($this->password))
				$this->addError($attribute,"邮箱或密码错误！");
		}
	}

	public function getAccount(){
		if($this->_account == null){
			;
		}
		return $this->_account;
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