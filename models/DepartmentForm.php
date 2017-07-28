<?php 
namespace wkapi\models;

use yii\base\Model;

use wkapi\models\Department;
use wkapi\models\lib\ValidateFun as VFun;

/**
* 
*/
class DepartmentForm extends Model
{
	
	public $user_id;
	public $group_id;
	public $name;
	public $desc;
	public $state=0;


	public function attributeLabels(){
		return [
			'user_id' => '负责人',
			'group_id' => '所属组织',
			'name'	  => '部门名',
			'desc' 	  => '部门描述',
		];
	}

	public function rules(){
		return [
			[['user_id','group_id','name'],'required'],
			[['user_id','group_id'] , 'is_id'],
			['name','string','min'=>2,'max'=>15],
			['desc','string','max'=>'125'],
			['state','boolean'],
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
		$group = new Department();
		$group->user_id = $this->user_id;
		$group->group_id = $this->group_id;
		$group->name = $this->name;
		$group->desc = $this->desc;
		if($group->save()){
			return $group;
		}
		return false;
	}
}

?>