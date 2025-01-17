<?php
namespace App\Libraries;

use Config\ApiConfig;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler
{
    private $config;

    public function __construct()
    {
        $this->config = new ApiConfig();
    }

    public function generateToken($userData)
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->config->tokenExpiration;

        $payload = [
            'iss' => 'inventory_system',
            'aud' => 'api_users',
            'iat' => $issuedAt,
            'exp' => $expire,
            'data' => [
                'user_id' => $userData['id'],
                'username' => $userData['username'],
                'role' => $userData['role']
            ]
        ];

        return JWT::encode($payload, $this->config->jwtSecret, 'HS256');
    }

    public function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->config->jwtSecret, 'HS256'));
            return (array) $decoded->data;
        } catch (Exception $e) {
            return false;
        }
    }
}