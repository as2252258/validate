<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2017/9/19 0019
 * Time: 13:00
 */

namespace validate;


class NumberValidate extends AbstractValidate
{
	public function check()
	{
		if(!is_numeric($this->value)){
			return preg_replace('/[^0-9]+/','',$this->value) == $this->value;
		}
		return false;
	}
}