<?php
namespace wkapi\models;

use yii\db\ActiveRecord;

class UserRole extends ActiveRecord
{
	
	public static function tablename(){
		return "user_role";
	}

	
}


?>