<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

class WeekReportController extends BaseController
{
	public $modelClass = 'wkapi\models\WeekReport';

}
