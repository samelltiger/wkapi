<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

class MonthPlanController extends BaseController
{
	public $modelClass = 'wkapi\models\MonthPlan';

	public function actions(){
		return [
			'index'=>[
				'class'=>'wkapi\actions\IndexAction',
                'modelClass' => $this->modelClass,
			],
		];
	}
}
