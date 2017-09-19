<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2017/9/19 0019
 * Time: 9:40
 */

namespace xianglin\validate;


class DatetimeValidate extends AbstractValidate
{
	const TYPE_TIME = 'time';
	
	const TYPE_DATE_TIME = 'datetime';
	
	const TYPE_DATE = 'date';
	
	const TYPE_STRTOTIME = 'strtotime';
	
	public $type;
	
	/**
	 * @return bool
	 */
	public function check()
	{
		if (empty($this->value)) {
			return $this->addError($this->field . ' Must Not Empty');
		}
		return $this->{$this->type}();
	}
	
	public function strtotime()
	{
		return is_numeric($this->value) && mb_strlen($this->value) == 11;
	}
	
	public function time()
	{
		return preg_match('/\d{2}:\d{2}(:\d{2}){0,}/', $this->value);
	}
	
	public function date()
	{
		return preg_match('/\d{4}-\d{2}-\d{2}/', $this->value);
	}
	
	public function datetime()
	{
		if (preg_match('/\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}/', $this->value)) {
			return true;
		}else if(preg_match('/\d{4}\d{2}\d{2}\d{2}\d{2}\d{2}/', $this->value)){
			return true;
		}
		return false;
	}
}