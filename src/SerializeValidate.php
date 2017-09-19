<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2017/9/19 0019
 * Time: 13:03
 */

namespace validate;


class SerializeValidate extends AbstractValidate
{
	public function check()
	{
		if (is_null(unserialize($this->value))) {
			return $this->addError('The Not\'s A Serialize');
		}
		return $this->isOk;
	}
}