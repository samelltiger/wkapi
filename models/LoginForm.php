<?php
namespace wkapi\models;

use yii\base\Model;
use wkapi\models\Account;
use wkapi\models\UserIdentity;


class LoginForm extends Model
{
	const LOGIN_BY_USERNAME = "username";
	const LOGIN_BY_EMAIL = "email";

	public $loginway ;
	public $email ;
	public $username ;
	public $password ;
	public $_account ;

	public function scenarios(){
		return [
			self::LOGIN_BY_USERNAME =>['loginway','username','password'],
			self::LOGIN_BY_EMAIL =>['loginway','email','password'],
		];
	}
	public function rules(){
		return [
			[['loginway','username','password'],'required','on'=>self::LOGIN_BY_USERNAME],
			['username','verifyUsername','on'=>self::LOGIN_BY_USERNAME],

			[['loginway','email','password'],'required','on'=>self::LOGIN_BY_EMAIL],
			['email','verifyEmail','on'=>self::LOGIN_BY_EMAIL],
		];
	}

	//验证用户名是否正确，同时验证密码
	public function verifyUsername( $attribute , $params){
		if(!$this->hasErrors()){
			$this->getAccountByusername();
			if( !$this->_account || md5($this->password) !== $this->_account['password'])
				$this->addError($attribute,"用户名或密码错误");
		}
	}

	//验证邮箱是否正确，同时验证密码
	public function verifyEmail( $attribute , $params){
		if(!$this->hasErrors()){
			$this->getAccountByemil();
			// print_r([md5($this->password) ,$this->_account['password']]);exit;
			if( !$this->_account || md5($this->password) !== $this->_account['password'])
				$this->addError($attribute,"邮箱或密码错误");
		}
	}

	//通过邮箱获取用户
	public function getAccountByemil(){
		if($this->_account == null){
			$this->_account = UserIdentity::findByEmail($this->email);
		}
		return $this->_account;
	}

	//通过用户名获取用户
	public function getAccountByusername(){
		if($this->_account == null){
			$this->_account = UserIdentity::findByUsername($this->username);
		}
		return $this->_account;
	}
}

?>