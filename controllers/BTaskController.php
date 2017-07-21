<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

class BTaskController extends BaseController
{
	public $modelClass = 'wkapi\models\BTask';

	public function actions(){
		return [
			'index'=>[
				'class'=>'wkapi\actions\IndexAction',
                'modelClass' => $this->modelClass,
			],
		];
	}

	public function actionTest(){
		return ['content'=>'faefaefa'];
	}

}
