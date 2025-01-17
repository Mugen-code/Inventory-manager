<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class ApiConfig extends BaseConfig
{
    public $jwtSecret;
    public $tokenExpiration = 3600; // 1 hour 

    public function __construct()
    {
        //JWT_SECRET from .env file
        $this->jwtSecret = getenv('JWT_SECRET') ?: 'default_secret_key_for_development';
    }
}