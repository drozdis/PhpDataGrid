<?php
namespace Widget;

/**
 * Class Helper
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class Helper
{
    /**
     * @param object $object
     * @param array  $options
     */
    public static function setConstructorOptions($object, $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . self::normalizeKey($key);
            if (method_exists($object, $method)) {
                $object->$method($value);
            }
        }
    }

    /**
     * @param string $key name param
     *
     * @return string
     */
    public static function normalizeKey($key)
    {
        $option = str_replace('_', ' ', strtolower($key));
        $option = str_replace(' ', '', ucwords($option));

        return $option;
    }

    /**
     * Merge options recursively
     *
     * @param array   $array1
     * @param mixed   $array2
     * @param boolean $override
     *
     * @return array
     */
    public static function mergeOptions(array $array1, array $array2, $override = false)
    {
        if (is_array($array2)) {
            foreach ($array2 as $key => $val) {
                if (is_array($array2[$key])) {
                    $value = $array1[$key] = (array_key_exists($key, $array1) && is_array($array1[$key]))
                        ? self::mergeOptions($array1[$key], $array2[$key], $override)
                        : $array2[$key];
                    if ($override === true) {
                        $array1[$key] = $value;
                    } elseif (!isset($array1[$key])) {
                        $array1[$key] = $value;
                    }
                } else {
                    if ($override === true) {
                        $array1[$key] = $val;
                    } elseif (!isset($array1[$key])) {
                        $array1[$key] = $val;
                    }
                }
            }
        }

        return $array1;
    }

    /**
     * Рекурсивная array_map
     *
     * @param string $func
     * @param array  $arr
     *
     * @return array
     */
    public static function arrayMap($func, array $arr)
    {
        foreach ($arr as &$row) {
            if (is_array($row)) {
                $row = self::arrayMap($func, $row);
            } else {
                $row = call_user_func($func, $row);
            }
        }

        return $arr;
    }

    /**
     * Фильтровать не значения ''
     *
     * @param array &$arr
     *
     * @return array
     */
    public static function filterNotEmpty(&$arr)
    {
        $res = array();
        foreach ($arr as $n => &$row) {
            if (is_array($row)) {
                $res[$n] = self::filterNotEmpty($row);
            } elseif ($row !== '' && $row !== null) {
                $res[$n] = $row;
            }
        }

        return $res;
    }

    /**
     * Получение параметра с Хеша
     * @param string      $hash
     * @param string|Null $name
     *
     * @return array|string
     */
    public static function getParam($hash, $name = null)
    {
        try {
            $params = self::arrayMap('urldecode', (array) json_decode(base64_decode($hash), true));
        } catch (Exception $e) {
            $params = array();
        }
        if ($name !== null) {
            return array_key_exists($name, $params) ? $params[$name] : '';
        }

        return $params;
    }

    /**
     * Формирование хеша
     * @param array $params
     *
     * @return string
     */
    public static function url($params)
    {
        return base64_encode(json_encode($params));
    }

    /**
     * @param object|array $row
     * @param string       $key
     *
     * @return mixed|null
     */
    public static function getValue($row, $key)
    {
        if (is_array($row)) {
            return isset($row[$key]) ? $row[$key] : null;
        } elseif (is_object($row)) {
            $method = 'get' . self::normalizeKey($key);
            if (method_exists($row, $method)) {
                return call_user_func(array($row, $method));
            } else {
                return null;
            }
        }

        return $row;
    }
}
