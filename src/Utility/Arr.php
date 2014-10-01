<?php

namespace ForumHouse\SelectelStorageApi\Utility;

/**
 * Array utilities class
 *
 * @package ForumHouse\SelectelStorageApi\Utility
 */
class Arr
{
    /**
     * Finds keys, that are absent in the array
     *
     * @param array $array Assoc array to search in
     * @param array $keys  A list of keys to check for absence
     *
     * @return array
     */
    public static function findAbsent(array $array, array $keys)
    {
        return array_keys(array_diff_key(array_flip($keys), $array));
    }

    /**
     * Finds keys, that are absent in the array
     *
     * @param array $array Assoc array to search in
     *
     * @return array
     */
    public static function findEmpty(array $array)
    {
        return array_filter($array, function ($value) {
            if (empty($value)) {
                return true;
            }
            return false;
        });
    }
}
 