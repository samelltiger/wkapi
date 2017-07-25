<?php 
namespace wkapi\models;

use yii\base\Model;

use wkapi\models\BTask;
use wkapi\models\lib\ValidateFun as VFun;

/**
* 
*/
class BTaskForm extends Model
{
	
	public $name;
	public $end_date;
	public $is_finished=0;
	public $finished_time;
	public $user_id;
	public $type_id;
	public $group_id;
	public $desc;
	public $state=1;


	public function attributeLabels(){
		return [
			'name' => "任务名",
			'end_date' => "终止时间",
			'is_finished' => "是否完成",
			'finished_time' => "完成时间",
			'user_id' => "用户",
			'type_id' => "任务类型",
			'group_id' => "组织",
			'desc' => "任务描述",
			'state' => "任务状态",
		];
	}

	public function rules(){
		return [
			[['name','end_date','user_id','group_id','type_id',],'required'],
			[['user_id','type_id','group_id'] , 'is_id'],
			['end_date','is_date'],
			['name','string','min'=>2,'max'=>15],
			['desc','string','max'=>'125'],
			[['state','is_finished'],'boolean'],
		];
	}

	public function is_id($attribute,$param){
		if( !$this->hasErrors() ){
			if( !VFun::is_id($this->$attribute) ){
				$this->addError($attribute,'参数不合法');
			}
		}
	}

	public function is_date($attribute,$param){
		if(!$this->hasErrors()){
			if(!VFun::is_date($this->$attribute)){
				$this->addError($attribute,'日期格式出错');
			}
			// VFun::str2timestamp($this->$attribute);
			if( !$this->hasErrors() && VFun::str2timestamp($this->$attribute) < time()+8*60*60 )
				$this->addError($attribute,'结束时间必须大于现在');
		}
	}

	public function errIsEndDate($issetEndDate){
			$errors = $this->getErrors();
			if(\count($errors)>=2||isset($errors['end_date'])&&count($errors['end_date'])>=2)	//如果错误多于两个，则直接返回true
				return false;
			if(!\array_key_exists('end_date',$errors) )		//如果错误与不是由end_date引起的，则直接返回
				return false;
			// echo $errors['end_date'][0];
			// var_dump( $errors['end_date'] !=="结束时间必须大于现在");die();
			// if( $errors['end_date'][0] !=="结束时间必须大于现在" )
			// 	return false;
			// return true;
			if( $issetEndDate && $errors['end_date'][0] =="结束时间必须大于现在"){
				return false;
			}else{
				if($errors['end_date'][0] !=="结束时间必须大于现在")
					return false;
				return true;
			}
	}

	public function save(){
		$task = new BTask();
		$task->name 			= $this->name ;
		$task->end_date 		= $this->end_date ;
		$task->is_finished 		= (int)$this->is_finished ;
		$task->user_id 			= (int)$this->user_id ;
		$task->group_id 		= (int)$this->group_id ;
		$task->type_id 			= (int)$this->type_id ;
		$task->desc 			= $this->desc ;

		if($task->save()){
			return $task;
		}
		return false;
	}
}

?>