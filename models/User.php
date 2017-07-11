<?php
namespace wkapi\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord
{
	
	public static function tablename(){
		return "user";
	}

	
}


?>