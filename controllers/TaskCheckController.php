<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

class TaskCheckController extends BaseController
{
	public $modelClass = 'wkapi\models\TaskCheck';

	public function actions(){
		return [
			'index'=>[
				'class'=>'wkapi\actions\IndexAction',
                'modelClass' => $this->modelClass,
			],
		];
	}
}
