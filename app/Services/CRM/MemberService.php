<?php

namespace App\Services\CRM;

use GuzzleHttp\Client;

class MemberService
{
    /* 2022/1/7 先停用，之後重構再處理
    private $http_client;

    public function __construct()
    {
        $this->http_client = new Client([
            'base_uri' => config('crm.api.base_uri'),
        ]);
    }
    */

    /**
     * 重設會員密碼
     *
     * @param string $token
     * @param array $payloads
     * @return array
     */
    /*
    public function resetPassword($token, $payloads)
    {
        $password = $payloads['password'];
        $endpoint = '/crm/v2/members/reset-password';

        $response = $this->http_client->request('POST', $endpoint, [
            'timeout' => 180,
            'connect_timeout' => 10,
            // 'verify' => false,
            'http_errors' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
            'json' => [
                'password' => $password,
            ],
        ]);

        $status_code = $response->getStatusCode();
        $body = $response->getBody();
        $response_payloads = json_decode($body, true);

        return [
            'status_code' => $status_code,
            'payloads' => $response_payloads,
        ];
    }
    */
}
