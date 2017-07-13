<?php
namespace wkapi\models;


use yii\db\ActiveRecord;

class User extends ActiveRecord
{
	
	public static function tablename(){
		return "user";
	}

	public static function getUserInfo($type , $forv , $state=1){
		return self::find()->
			where($type.'=:parm1'  /*$type.'=:parm1 and '.'state='.$state*/,[':parm1'=>$forv])->one();
	}

	public static function setPassword($password)
    {
        return md5($password);
    }
	
}


?>