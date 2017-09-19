<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2017/9/19 0019
 * Time: 9:08
 */

namespace Yoc\validate\ulity;


interface ValidateInterface
{
	
	/**
	 * @return bool
	 */
	public function trigger();
	
	
	public function check();
	
	
//	public function addError($message);
}