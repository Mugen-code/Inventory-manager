<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Libraries\JWTHandler;
use CodeIgniter\API\ResponseTrait;

class Auth extends ResourceController
{
    use ResponseTrait;

    protected $format = 'json';  

    public function login()
    {
        log_message('debug', 'Raw request body: ' . $this->request->getBody());
        
        // Get JSON data instead of POST data
        $json = $this->request->getJSON(true);
        log_message('debug', 'Parsed JSON data: ' . json_encode($json));

        // Validate required fields
        if (!isset($json['username']) || !isset($json['password'])) {
            return $this->fail('Username and password are required', 400);
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('username', $json['username'])->first();
        
        log_message('debug', 'User found: ' . ($user ? 'Yes' : 'No'));

        if (!$user || !password_verify($json['password'], $user['password'])) {
            log_message('debug', 'Password verification failed');
            return $this->failUnauthorized('Invalid credentials');
        }

        $jwt = new JWTHandler();
        $token = $jwt->generateToken($user);

        return $this->respond(['token' => $token]);
    }
}