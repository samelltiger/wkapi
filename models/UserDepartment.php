<?php
namespace wkapi\models;

use yii\db\ActiveRecord;

class UserDepartment extends ActiveRecord
{
	
	public static function tablename(){
		return "user_department";
	}

	
}


?>