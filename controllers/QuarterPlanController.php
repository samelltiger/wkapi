<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

class QuarterPlanController extends BaseController
{
	public $modelClass = 'wkapi\models\QuarterPlan';

}
