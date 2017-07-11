<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

class DepartmentController extends BaseController
{
	public $modelClass = 'wkapi\models\Department';

}
