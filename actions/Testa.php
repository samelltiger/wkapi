<?php
namespace wkapi\actions;

use Yii;
use yii\rest\Action;

class Testa extends Action{
	
	public $modelClass = 'wkapi\models\UserDepartment';

	public function  run(){
		return ['actions'=>'testa'];
	}
}

?>