<?php 
namespace wkapi\controllers;

use Yii;
use wkapi\controllers\common\BaseController;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

class GroupController extends BaseController
{
	public $modelClass = 'wkapi\models\Group';

}
