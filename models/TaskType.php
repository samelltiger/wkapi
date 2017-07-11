<?php
namespace wkapi\models;

use yii\db\ActiveRecord;

class TaskType extends ActiveRecord
{
	
	public static function tablename(){
		return "task_type";
	}

	
}


?>