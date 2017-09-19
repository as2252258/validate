<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2017/9/19 0019
 * Time: 9:17
 */

namespace validate;


class RequiredValidate extends AbstractValidate
{
	
	/**
	 * @return bool
	 */
	public function check()
	{
		if(is_string($this->field)){
			if(isset($this->params[$this->field])){
				$param = $this->params[$this->field];
				if(empty($param) && !is_numeric($param)){
					return $this->addError($this->field . ' is required');
				}
			}
			return true;
		}
		foreach ($this->field as $value){
			if(!isset($this->params[$value]) || empty($this->params[$value])){
				return $this->addError($value . ' is required');
			}
		}
		return true;
	}
	
}