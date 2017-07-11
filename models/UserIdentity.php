<?php
namespace wkapi\models;

use yii\db\ActiveRecord;

class UserIdentity extends ActiveRecord implements \yii\web\IdentityInterface{

	public static function tablename(){
		return "user";
	}

	public static function findIdentity($id){
		return static::findOne($id);
	}

	public static function findIdentityByAccessToken($token,$type=null){
		return static::findOne(['accessToken'=>$token,'state'=>1]);
	}

	public static function findByUsername($username,$state=1){
		return static::findOne(['username'=>$username,'state'=>$state]);
	}

	public static function findByEmail($email,$state=1){
		return static::findOne(['email'=>$email,'state'=>$state]);
	}

	public function getId(){
		return $this->id;
	}

	public function getAuthkey(){
		return $this->auth_key;
	}

	public function setPassword($password)
    {
        return $this->password = md5($password);
    }

	public function validateAuthKey($authKey){
		return $this->auth_key ===$authKey;
	}

	public function validatePassword($password){
		return $this->password ===md5($password);
	}

	public function generateAuthKey(){
		return $this->auth_key=\Yii::$app->security->generateRandomString();
		// return $this->save();
	}

	public function generatewkapiToken(){
		$this->accessToken = \Yii::$app->security->generateRandomString()."_".time();
	}

	public static function apiTokenIsValid($token){
		if(empty($token)){
			return false;
		}

		$timestamp = (int) substr($token, strrpos($token, '_')+1);
		$expire    = 	Yii::$app->params['user.wkapiTokenExpire'];
		return $timestamp + $expire >= time();
	}
}
?>