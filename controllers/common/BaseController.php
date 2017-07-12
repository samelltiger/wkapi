<?php
namespace  wkapi\controllers\common;

use Yii;
use yii\rest\ActiveController;

class BaseController extends ActiveController
{
	
	public function get( $name = null , $default=null){
		return \Yii::$app->request->get( $name , $default );
	}

	public function post( $name = null , $default=null ){
		return \Yii::$app->request->post( $name , $default );
	}

	/**
	* @$data  渲染的数据
	* @$state 标识此次请求是否成功
	* @$code  返回的状态码
	* @$message 错误信息
	*/
	public static function renderJson( $data , $state=1 , $code=200 , $message=null ){
		$response =[
			'success' => $state?"success":"fail",
			'code' 	  => $code,
			'message' => $message,
			'data'	  => $data,
		];
		return $response;
	}

	public function getModelOneStrErrors($model){
		if($model->hasErrors()){
			$errors = $model->getFirstErrors();
			foreach ($errors as $value) {
				return $value;
			}
		}
		return false;
	}

}

?>