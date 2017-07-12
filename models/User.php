<?php
namespace wkapi\models;


use yii\db\ActiveRecord;

class User extends ActiveRecord
{
	
	public static function tablename(){
		return "user";
	}

	public static function getUserInfo($type , $for){
		return self::findOne([$type=>$for]);
	}
	
}


?>