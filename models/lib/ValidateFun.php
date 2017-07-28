<?php 
namespace wkapi\models\lib;

/**
* 用于验证的函数库
*/
class ValidateFun 
{
	
	public static function is_email($value){
		return preg_match('/[\w\d_-]+@[\w\d_-]+(\.[\w\d_-]+)+$/', $value);
	}

	public static function is_id($value){
		return preg_match('/[\d]+$/', $value);
	}

	/**
	* 判读传来的值是否合法
	* @param mixed $value 值
	* @return int 0:id,1:email,2：数据不合法
	*/
	public static function is_email_or_id($value){
		if(static::is_id($value))
			return 0;
		elseif(static::is_email($value))
			return 1;
		else
			return 2;
	}

	/**
	* 递归读取数组的深度
	* @param mixed $value 值
	* @return array 返回array($max,$min)
	*/
	public static function array_deep($arr){
		$i = 0;
		$max = 0;
		$min = 100000000;
		if(!is_array($arr))
			return 0;

		foreach ($arr as $v) {
			if(!is_array($v)){
				$i = 1;
			}else{
				$i = 1+max(static::array_deep($v)) ;
			}
			if($max < $i)
				$max = $i;

			if($min > $i ){
				$min = $i;
			}
		}
		return [$max,$min];
	}


	public static function is_date($dateStr){
		return preg_match("/(\d{2}|\d{4})(?:\-)?([0]{1}\d{1}|[1]{1}[0-2]{1})(?:\-)?([0-2]{1}\d{1}|[3]{1}[0-1]{1})(?:\s)?([0-1]{1}\d{1}|[2]{1}[0-3]{1})(?::)?([0-5]{1}\d{1})(?::)?([0-5]{1}\d{1})/", $dateStr);
	}

	public static function str2timestamp($str){
		$arrdate = preg_split("/[\.\/-]/", $str);
		$arrtime = explode(":", str_replace(" ", ":", $arrdate[2]));
		$timestamp = mktime($arrtime[1],$arrtime[2],$arrtime[3],$arrdate[1],$arrtime[0],$arrdate[0]);
		return $timestamp;
	}
}


?>