<?php
namespace app\helpers;

class AssocArrayDiff
{
    public static function array_diff_assoc_recursive($array1, $array2)
    {
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key])) {
                    $difference[$key] = $value;
                } elseif (!is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = static::array_diff_assoc_recursive($value, $array2[$key]);
                    if ($new_diff != false) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif (!isset($array2[$key]) || $array2[$key] != $value) {
                $difference[$key] = $value;
            }
        }
        return !isset($difference) ? [] : $difference;
    }

    public static function diff($a1, $a2)
    {
        $arr1_flat = self::flatten($a1);
        $arr2_flat = self::flatten($a2);

        $ret = array_diff_assoc($arr1_flat, $arr2_flat);

        return self::inflate($ret);
    }

    static function flatten($arr, $base = "", $divider_char = "/")
    {
        $ret = [];
        if (is_array($arr)) {
            foreach ($arr as $k => $v) {
                if (is_array($v)) {
                    $tmp_array = self::flatten($v, $base . $k . $divider_char, $divider_char);
                    $ret = array_merge($ret, $tmp_array);
                } else {
                    $ret[$base . $k] = $v;
                }
            }
        }
        return $ret;
    }

    static function inflate($arr, $divider_char = "/")
    {
        if (!is_array($arr)) {
            return false;
        }

        $split = '/' . preg_quote($divider_char, '/') . '/';

        $ret = [];
        foreach ($arr as $key => $val) {
            $parts = preg_split($split, $key, -1, PREG_SPLIT_NO_EMPTY);
            $leafpart = array_pop($parts);
            $parent = &$ret;
            foreach ($parts as $part) {
                if (!isset($parent[$part])) {
                    $parent[$part] = [];
                } elseif (!is_array($parent[$part])) {
                    $parent[$part] = [];
                }
                $parent = &$parent[$part];
            }

            if (empty($parent[$leafpart])) {
                $parent[$leafpart] = $val;
            }
        }
        return $ret;
    }
}
