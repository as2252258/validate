<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2017/9/19 0019
 * Time: 9:08
 */

namespace Yoc\validate;

use Yoc\validate\ulity\ArrayData;
use Yoc\validate\ulity\ValidateInterface;

abstract class AbstractValidate implements ValidateInterface
{
	private $objects = [];
	
	public $rule;
	
	public $params = [];
	
	public $field;
	
	public $value;
	
	protected $message;
	
	public $model;
	
	public $isOk = true;
	
	private static $validate = [
		'required'  => 'Yoc\validate\RequiredValidate',
		'not empty' => 'Yoc\validate\NotEmptyValidate',
		'int'       => 'Yoc\validate\NumberValidate',
		'string'    => 'Yoc\validate\StringValidate',
		'boolean'   => 'Yoc\validate\RequiredValidate',
		'json'      => 'Yoc\validate\JsonValidate',
		'serialize' => 'Yoc\validate\SerializeValidate',
		'time'      => [
			'class' => 'Yoc\validate\DatetimeAbstractValidate',
			'type'  => DatetimeAbstractValidate::TYPE_TIME
		],
		'date'      => [
			'class' => 'Yoc\validate\DatetimeAbstractValidate',
			'type'  => DatetimeAbstractValidate::TYPE_DATE
		],
		'datetime'  => [
			'class' => 'Yoc\validate\DatetimeAbstractValidate',
			'type'  => DatetimeAbstractValidate::TYPE_DATE_TIME
		],
		'strtotime' => [
			'class' => 'Yoc\validate\DatetimeAbstractValidate',
			'type'  => DatetimeAbstractValidate::TYPE_STRTOTIME
		],
		'maxLength' => [
			'class' => 'Yoc\validate\LengthValidate',
			'type'  => LengthValidate::MAX_LENGTH,
		],
		'minLength' => [
			'class' => 'Yoc\validate\LengthValidate',
			'type'  => LengthValidate::MIN_LENGTH
		],
		'length'    => [
			'class' => 'Yoc\validate\LengthValidate',
			'type'  => LengthValidate::MIN_LENGTH
		]
	];
	
	/**
	 * @param string       $type
	 * @param array        $rule AS [
	 *                           ['phone', 'email', 'password'] , 'required', 'not empty'],
	 *                           [['phone', 'email'] , 'checkUnique'],
	 *                           ['phone', 'patten' => '/^1[35789]\d{9}$/'],
	 *                           ['password', 'patten' => '/^[a-zA-Z0-9]{32}$/'],
	 *                           ['nickname','filter','maxLength'=>'50','minLength'=>1],
	 *                           ]
	 *                           function smsVery($object, $fields, $rule, $attributes){
	 *                           if($isFalse = false){
	 *                           return $object->addError('');
	 *                           }
	 *                           return $object->isOk;
	 *                           }
	 * @param array|string $fields
	 * @param array        $attributes
	 *
	 * @return static
	 */
	public static function createValidate($type, object $model, $fields, $attributes = [])
	{
		$isInstance = is_object($model);
		if (is_string($type) && isset(static::$validate[$type])) {
			$_class = static::$validate[$type];
		} else if ($isInstance && method_exists($model, $type)) {
			$_class = [[$model, $type], new static(), $fields, $attributes];
		} else if (is_callable($type, true)) {
			$_class = [$type, new static(), $fields, $attributes];
		} else if (is_array($type) && isset(static::$validate[key($type)])) {
			$_class['class'] = static::$validate[key($type)];
			$_class['rule'] = $type[key($type)];
		}
		if (empty($_class)) {
			$_class = 'Yoc\validate\StringValidate';
		}
		if (!is_array($_class)) {
			$_class['class'] = $_class;
		}
		
		$_class = ArrayData::merger($_class, ['params' => $attributes, 'fields' => $fields]);
		
		if (isset($_class['class'])) {
			$className = $_class['class'];
			unset($_class['class']);
		} else {
			$className = array_shift($_class);
		}
		
		return static::create($className, $_class);
	}
	
	protected static function create($className, $config = [])
	{
		if (is_array($className)) {
			$class = array_shift($className);
			if (is_object($class)) {
				if (!method_exists($class, array_shift($className))) {
					throw new \Exception('method not exists');
				}
			} else {
				$class = self::reflection($className);
			}
		} else if (is_callable($className, true)) {
			return call_user_func_array($className, $config);
		} else {
			$class = self::reflection($className);
		}
		
		if (!empty($config)) {
			foreach ($config as $key => $val) {
				$className->$key = $val;
			}
		}
		return $class;
	}
	
	protected static function reflection($className)
	{
		$reflection = new \ReflectionClass($className);
		if (!$reflection->isInstantiable()) {
			throw new \Exception('Class ' . $reflection->getName() . ' Con\'t Instance');
		}
		return $reflection->newInstanceArgs();
	}
	
	/**
	 * @param $objects
	 *
	 * @return int
	 */
	public function append($objects)
	{
		return array_push($this->objects, $objects);
	}
	
	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function trigger()
	{
		if (empty($this->objects)) return true;
		foreach ($this->objects as $key => $val) {
			if (is_object($val)) {
				if (!method_exists($val, 'check')) {
					throw new \Exception('Not Find ' . get_class($val) . '::check');
				}
				$data = $val->check();
			} else {
				$data = call_user_func_array(array_shift($val), $val);
			}
			if (!$data) {
				return $this->addError('field ' . $val->field . ' format error');
			}
		}
		return true;
	}
	
	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function has($name)
	{
		return !empty($this->datas) && isset($this->datas[$name]);
	}
	
	/**
	 * @param $message
	 *
	 * @return bool
	 */
	public function addError($message)
	{
		$this->isOk = false;
		if (empty($this->message)) {
			$this->message = $message;
		}
		return $this->isOk;
	}
	
	/**
	 * @return mixed
	 */
	public function getMessage()
	{
		return $this->message;
	}
	
	public function __set($name, $value)
	{
		if (property_exists($this, $name)) {
			$this->$name = $value;
		} else if (method_exists($this, 'set' . ucfirst($name))) {
			$this->{'set' . ucfirst($name)}($value);
		} else {
			throw new \Exception('unknown property : ' . $name . ' at class ' . get_called_class());
		}
	}
}