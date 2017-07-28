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

	// 获取模型验证的第一条错误
	public function getModelOneStrErrors($model){
		if($model->hasErrors()){
			$errors = $model->getFirstErrors();
			foreach ($errors as $value) {
				return $value;
			}
		}
		return false;
	}

	public function is_email($value){
		return preg_match('/[\w\d_-]+@[\w\d_-]+(\.[\w\d_-]+)+$/', $value);
	}

	public function is_id($value){
		return preg_match('/[\d]+$/', $value);
	}

	/**
	* 判读传来的值是否合法
	* @param mixed $value 值
	* @return int 0:id,1:email,2：数据不合法
	*/
	public function is_email_or_id($value){
		if($this->is_id($value))
			return 0;
		elseif($this->is_email($value))
			return 1;
		else
			return 2;
	}

	//递归判断所有的值是否为id
	public function is_id_map($arr){
		$state = true;

		if(is_array($arr)){
			foreach ($arr as $v) {
				if(is_array($v)){
					if ( !$this->is_id_map($v) ) 
						return false ;
				}
				else{
					if( !$this->is_id($v) ) 
						return false;
				}
			}
			return true;
		}else{
			return $this->is_id($arr);
		}
	}


	/**
	* 递归读取数组的深度
	* @param mixed $value 值
	* @return array 返回array($max,$min)
	*/
	public function array_deep($arr){
		$i = 0;
		$max = 0;
		$min = 100000000;
		if(!is_array($arr))
			return 0;

		foreach ($arr as $v) {
			if(!is_array($v)){
				$i = 1;
			}else{
				$i = 1+max($this->array_deep($v)) ;
			}
			if($max < $i)
				$max = $i;

			if($min > $i ){
				$min = $i;
			}
		}
		return [$max,$min];
	}

	public function loadModelValue($obj,$modelname,$arr_str){
		$arr=[];
		foreach ($obj as $key => $value) {
			if( in_array($key, $arr_str) )
				$arr[$modelname][$key] = $value;
		}
		return $arr;
	}
}

?>