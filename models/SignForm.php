<?php
namespace wkapi\models;

use yii\base\Model;
use wkapi\controllers\common\BaseController;
use wkapi\models\UserIdentity;


class SignForm extends Model
{
	public $email ;
	public $username ;
	public $password ;
	public $is_admin=0 ;
	public $_user=null;

	public function rules(){
		return [
			[['email','username','password'],'required'],
			['username','string','min'=>5,'max'=>18],
			['email','email',/*'message'=>
				BaseController::renderJson([],0,210,'请输入正确的email')*/],
			['email','verifyEmail'],
			['is_admin','boolean'],
			['password','string','min'=>6, 'max'=>'16'],
		];
	}

	//验证邮箱是否正确，同时验证密码
	public function verifyEmail( $attribute , $params){
		if(!$this->hasErrors()){
			$this->getAccountByemil();
			// print_r($this->_user);die;
			if( $this->_user ){
				$this->addError($attribute,"邮箱已被使用");
				return false;
			}
		}
	}

	//通过邮箱获取用户
	public function getAccountByemil(){
		if($this->_user === null){
			$this->_user = UserIdentity::findByEmail($this->email);
		}
		return $this->_user;
	}

	public function save(){
		$user = new UserIdentity();
		$user->email = $this->email;
		$user->username = $this->username;
		$user->setPassword( $this->password);
		$user->token = UserIdentity::generatewkapiToken();
		return $user->save();
	}
}

?>