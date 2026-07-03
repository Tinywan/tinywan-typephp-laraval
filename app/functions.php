<?php
declare(strict_types=1);
/** Here is your custom functions. */

use Illuminate\Http\JsonResponse;

if (!function_exists('response_json')) {
    function response_json($msg = '', $code = 0, $data = [], $httpCode = 200, $header = [], $isObject = true): JsonResponse
    {
        if (empty($data) && $isObject) {
            $data = (object) $data;
        }

        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];

        return response()->json($result, $httpCode, $header);
    }
}
