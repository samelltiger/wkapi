<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

class WeekReportController extends BaseController
{
	public $modelClass = 'wkapi\models\WeekReport';

	public function actions(){
		return [
			'index'=>[
				'class'=>'wkapi\actions\Testa',
                'modelClass' => $this->modelClass,
			],
		];
	}
}
