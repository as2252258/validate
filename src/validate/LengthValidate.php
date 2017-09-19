<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2017/9/19 0019
 * Time: 9:38
 */

namespace Yoc\validate;


class LengthValidate extends AbstractValidate
{
	
	const MAX_LENGTH = 'maxLength';
	
	const MIN_LENGTH = 'minLength';
	
	const TYPE_LENGTH = 'fixedLength';
	
	public $type;
	
	public $length;
	
	public $value;
	
	/**
	 * @return bool
	 * 检查
	 */
	public function check()
	{
		if (empty($this->value) || mb_strlen($this->value) < 1) {
			return $this->addError($this->field . ' Length At Least One');
		}
		if ($this->length < 1) {
			throw new \Exception('Length Filter Config Error');
		}
		return $this->{$this->type}();
	}
	
	/**
	 * @return bool
	 * 是否为最大长度
	 */
	protected function maxLength()
	{
		return mb_strlen($this->value) <= $this->length;
	}
	
	/**
	 * @return bool
	 * 是否为最小长度
	 */
	protected function minLength()
	{
		return mb_strlen($this->value) >= $this->length;
	}
	
	/**
	 * @return bool
	 * 检查是否为指定长度
	 */
	protected function fixedLength()
	{
		return mb_strlen($this->value) == $this->length;
	}
}