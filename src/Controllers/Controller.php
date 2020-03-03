<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function validateParametersRequest(Request $request, $expectedParameters, $start)
    {
        $validator = Validator::make($request->all(), $expectedParameters);
        $isFailed = $validator->fails();
        if ($isFailed) {
            $data = array(
                'response' => array(
                    'code' => 400,
                    'message' => 'Wrong parameters',
                    'latency' => microtime(true) - $start,
                    'host' => getHostByName(getHostName())
                ),
                'data' => (object)new \stdClass
            );

            $validationResponse = response()->json($data, 401);
            return $validationResponse;
        }
        return null;
    }

    protected function getJsonSuccess($message, $data, $start)
    {
        return $response = [
            'response' => [
                'code' => 200,
                'message' => $message,
                'latency' => microtime(true) - $start,
                'host' => getHostByName(getHostName())
            ],
            'data' => $data];
    }

    protected function getJsonFailed($mesage, $start)
    {
        return $response = [
            'response' => [
                'code' => '401',
                'message' => $mesage,
                'latency' => microtime(true) - $start,
                'host' => getHostByName(getHostName())
            ],
            'data' => (object)new \stdClass
        ];
    }
}
