<?php

namespace Abdullahhafizh\Auth\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

class AuthorizationMiddleware
{
    private const JWT_KEY = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApscnXB6rBqFbgopEWt+5
7wh7be/So1wA0Rx6lntF4Ud77jLf9vXerbvKdleFx9YBgwb8SMCM0E5w0KBbySBW
b+MrqQGoezsjTn7RM+udwkGlHSZEqlDAPK7sJ6B/N535NCDrZbonmVegjoIE0/VC
kKs4iiZZmtoPnmtbBDdQSswVCRii28016NvvvCArOPfu6Fk7josaDTSdg3dhYlp6
0iYSmBYi7h6Jyd7mI4Oz5FJ+Ybjbrjv+Rkd8K+POHGdECjHV9sNs79+p6SCJkD6O
Nzczi6SGI4S110e89egxxes029x+hl2D/1C/FtMD7H8gZR+R04/3+25W8LulWySL
jwIDAQAB
-----END PUBLIC KEY-----
EOD;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $header = $request->header('Authorization', null);

        if (isset($header)) {
            $accessToken = $this->extractAccessToken($header);
            if (!empty($accessToken)) {
                try {
                    $jwt = JWT::decode($accessToken, self::JWT_KEY, ['RS256']);
                    $request->attributes->add(['jwt' => $jwt]);
                    return $next($request);

                } catch (ExpiredException $e) {
                    //todo: expired token
                    $response = $this->getExpiredJson();
                    return response()->json($response, 401);

                } catch (\Exception $genericException) {
                    //todo: invalid token
                    $response = $this->getExceptionJson($genericException);
                    return response()->json($response, 401);
                }

            } else {
                //todo: invalid token
                $response = $this->getInvalidToken();
                return response()->json($response, 401);
            }


        } else {
            $response = $this->getUnauthorizedJson();
            return response()->json($response, 401);
        }
    }

    private function extractAccessToken($header)
    {
        if (Str::startsWith($header, 'Bearer: ')) {
            return Str::substr($header, 8);
        }
        return '';
    }

    /**
     * @return array
     */
    private function getUnauthorizedJson(): array
    {
        $response = [
            'response' => [
                'code' => 401,
                'message' => 'Unauthorized',
                'host' => getHostByName(getHostName())
            ],
            'data' => (object)null
        ];
        return $response;
    }

    /**
     * @return array
     */
    private function getExpiredJson(): array
    {
        $response = [
            'response' => [
                'code' => 401,
                'message' => 'Exipred token',
                'host' => getHostByName(getHostName())
            ],
            'data' => (object)null
        ];
        return $response;
    }

    private function getExceptionJson($ex): array
    {
        $response = [
            'response' => [
                'code' => 401,
                'message' => 'Exception: ' . $ex->getMessage(),
                'host' => getHostByName(getHostName())
            ],
            'data' => (object)null
        ];
        return $response;
    }

    private function getInvalidToken(): array
    {
        $response = [
            'response' => [
                'code' => 401,
                'message' => 'Invalid token',
                'host' => getHostByName(getHostName())
            ],
            'data' => (object)null
        ];
        return $response;
    }
}
