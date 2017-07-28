<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

class TaskTypeController extends BaseController
{
	public $modelClass = 'wkapi\models\TaskType';

	public function actions(){
		return [
			'index'=>[
				'class'=>'wkapi\actions\IndexAction',
                'modelClass' => $this->modelClass,
			],
		];
	}
}
