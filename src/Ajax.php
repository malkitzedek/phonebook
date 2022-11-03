<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev;

/**
 * Класс предназначен для передачи данных по Ajax
 *
 */
class Ajax
{
    /**
     * @param $response
     */
    public static function print($response)
    {
        print json_encode($response);
        exit();
    }

    /**
     * @param $data
     */
    public static function success($data)
    {
        print json_encode(['status' => 'success', 'data' => $data]);
        exit();
    }

    /**
     * @param $data
     */
    public static function error($data)
    {
        print json_encode(['status' => 'error', 'data' => $data]);
        exit();
    }
}
