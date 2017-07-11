<?php
namespace wkapi\models;

use Yii;
use yii\base\Model;
use wkapi\models\UserIdentity;
use wkapi\models\User;
use wkapi\models\Account;

class SigninForm extends Model
{
	
	public $email;
	public $username;
	public $password;
	public $_account = null;
	public $verifycode;

	public function rules(){
		return [
			[['username','password','email',/*'verifycode'*/],"required"],
			['username','filter','filter'=>'trim',"skipOnArray"=>true],
			['username','string','max'=>30,'min'=>1],
			['username','verifyusername'],
			['email','string','max'=>20,'min'=>6],
			['email','email'],
			// ['verifycode','captcha'],
			['password','string','max'=>16,'min'=>6],
			['email','verifyemail']
		];
	}

	public function verifyemail($attribute,$param){
		if(!$this->hasErrors()){
			$this->getAccountByemil();
			if( $this->_account )
				$this->addError($attribute,"该邮箱已存在");
		}
	}

	public function verifyusername( $attribute , $param ){
		if(!$this->hasErrors()){
			$this->getAccountByusername();
			if( $this->_account )
				$this->addError($attribute,"该用户名已存在");
		}
	}

	public function getAccountByemil(){
		if($this->_account == null){
			$this->_account = UserIdentity::findByEmail($this->email);
		}
		return $this->_account;
	}

	public function getAccountByusername(){
		if($this->_account == null){
			$this->_account = UserIdentity::findByUsername($this->username);
		}
		return $this->_account;
	}

	public function sava( ){		//还要修改，要用mysql的事务进行处理，要么同时成功，要么同时失败
		if($this->validate( ) ){
			$user = new UserIdentity( );
			//测试一个事务
			$connection = $user->getDb( );
			$userTransaction = $connection -> beginTransaction();
			try {
				$connection->createCommand( )->insert(
					Account::tablename(),[
					'email' => $this ->email,
					'username' => $this ->username,
					'password' => $user->setPassword($this ->password),
					'auth_key' => $user ->generateAuthKey(),
					]
					)->execute();

				$date = date('Y-m-d H-m-s',time()+8*60*60);
				$user = UserIdentity::findByEmail( $this->email );

				$connection->createCommand( )->insert(
					User::tablename(),[
					'account_id' => $user['id'],
					'created_at' => $date,
					'updated_at' => $date,
					]
					)->execute();
				$userTransaction->commit();
			} catch (Exception $e) {
				$userTransaction->rollback();
				return false;
			}

			// $user->email = $this ->email;
			// $user->username = $this ->username;
			// $user->setPassword($this ->password);
			// $user ->generateAuthKey();
			// if(!$user->save())
			// 	return false;
			// $user = UserIdentity::findByEmail($user->email);
			// $usr = new User();
			// $date = date('Y-m-d H-m-s',time()+8*60*60);
			// $usr->account_id = $user['id'];
			// $usr->created_at = $date;
			// $usr->updated_at = $date;
			// if( !$usr->save() ) {
			// 	return false; 
			// }
			$data = Account::getUserInfo('id' , $user['id'] );
			return [ $data,$data->users ];
		}else
			return false;
	}
}

?>