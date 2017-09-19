<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2017/9/19 0019
 * Time: 10:03
 */

namespace Yoc\validate\ulity;

class ArrayData
{
	
	/**
	 * @return array|mixed
	 * 数据合并
	 */
	public static function merger()
	{
		$params = func_get_args();
		if (is_callable($params, true)) {
			return call_user_func($params);
		} else if (!is_array($params) || empty($params)) {
			return [];
		} else {
			$first = array_shift($params);
			if (empty($params)) {
				return is_array($first) ? $first : [];
			}
			foreach ($params as $value) {
				foreach ($value as $childKey => $childValue) {
					$first[$childKey] = $childValue;
					if (!is_numeric($childKey)) {
						if (array_key_exists($childKey, $first)) {
							if (is_array($childValue)) {
								$first[$childKey] = self::merger($first[$childKey], $childValue);
							} else {
								$first[$childKey] = $childValue;
							}
						} else {
							$first[$childKey] = $childValue;
							if (is_array($childValue)) {
								$first[$childKey] = self::merger($first[$childKey], $childValue);
							}
						}
					} else if (is_array($childValue)) {
						$first[$childKey] = self::merger($first[$childKey], $childValue);
					} else {
						$first[$childKey] = $childValue;
					}
				}
			}
			return $first;
		}
	}
	
	/**
	 * @param $data
	 * @param $start
	 * @param $length
	 *
	 * @return array|mixed
	 * @throws \Exception
	 * 对数据进行分割操作
	 */
	public static function slice($data, $start = 0, $length = 20)
	{
		if (is_callable($data, true)) {
			$data = call_user_func($data);
		}
		if (!is_array($data)) {
			throw new \Exception('Data Must Array');
		}
		if (count($data) > $length) return $data;
		if ($start >= $length) {
			$start = 0;
		}
		return array_slice($data, $start, $length);
	}
}