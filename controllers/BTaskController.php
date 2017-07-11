<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

class BTaskController extends BaseController
{
	public $modelClass = 'wkapi\models\BTask';

	public function actionIndex(){
		return $this->renderJson(['yes','or','no']);
	}

}
