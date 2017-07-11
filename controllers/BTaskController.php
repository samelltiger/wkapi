<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

class BTaskController extends BaseController
{
	public $modelClass = 'wkapi\models\BTask';

	public function actionCcc(){
		return $this->renderJson(['yes','or','no']);
	}

	public function actionTest(){
		return ['content'=>'faefaefa'];
	}

}
