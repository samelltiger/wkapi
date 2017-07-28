<?php 
namespace wkapi\models;

use yii\base\Model;

use wkapi\models\Group;
use wkapi\models\lib\ValidateFun as VFun;

/**
* 
*/
class GroupForm extends Model
{
	
	public $user_id;
	public $name;
	public $desc;
	public $state=0;


	public function attributeLabels(){
		return [
			'user_id' => '用户',
			'name'	  => '组织名',
			'desc' 	  => '组织描述',
		];
	}

	public function rules(){
		return [
			[['user_id','name'],'required'],
			['user_id','is_id'],
			['name','string','min'=>2,'max'=>15],
			['desc','string','max'=>'350'],
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
		$group = new Group();
		$group->user_id = $this->user_id;
		$group->name = $this->name;
		$group->desc = $this->desc;
		if($group->save()){
			return $group;
		}
		return false;
	}
}

?>