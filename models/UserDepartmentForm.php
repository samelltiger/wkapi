<?php 
namespace wkapi\models;

use yii\base\Model;

use wkapi\models\UserDepartment;
use wkapi\models\lib\ValidateFun as VFun;

/**
* 
*/
class UserDepartmentForm extends Model
{
	
	public $user_id;
	public $group_id;
	public $department_id;


	public function attributeLabels(){
		return [
			'user_id' => '用户',
			'group_id'	  => '组织',
			'department_id'	  => '部门',
		];
	}

	public function rules(){
		return [
			[['user_id','group_id','department_id'],'required'],
			[['user_id','group_id','department_id'],'is_id'],
		];
	}

	public function is_id($attribute,$param){
		if( !$this->hasErrors() ){
			if( !VFun::is_id($this->$attribute) ){
				$this->addError($attribute,'参数不合法');
			}
		}
	}

	public function save(){
		$group = new UserDepartment();
		$group->user_id = $this->user_id;
		$group->group_id = $this->group_id;
		$group->department_id = $this->department_id;
		if($group->save()){
			return $group;
		}
		return false;
	}
}

?>