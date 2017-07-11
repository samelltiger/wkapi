<?php
namespace wkapi\models;

use yii\db\ActiveRecord;

class UserGroup extends ActiveRecord
{
	
	public static function tablename(){
		return "user_group";
	}

	
}


?>