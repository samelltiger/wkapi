<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

class QuarterReportController extends BaseController
{
	public $modelClass = 'wkapi\models\QuarterReport';

	public function actions(){
		return [
			'index'=>[
				'class'=>'wkapi\actions\Testa',
                'modelClass' => $this->modelClass,
			],
		];
	}
}
