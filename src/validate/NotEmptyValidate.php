<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2017/9/19 0019
 * Time: 11:01
 */

namespace Yoc\validate;


class NotEmptyValidate extends AbstractValidate
{
	/**
	 * @return bool
	 */
	public function check()
	{
		$this->message = $this->field . ' Must Not Empty';
		return $this->isOk = $this->field === null || (empty($this->field) && !is_numeric($this->field));
	}
}