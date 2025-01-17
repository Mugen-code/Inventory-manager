<?php
namespace App\Filters;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\JWTHandler;

class ApiAuthFilter implements FilterInterface
{
    use ResponseTrait;

    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');
        if (!$header) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'No token provided']);
        }

        $token = str_replace('Bearer ', '', $header);
        $jwtHandler = new JWTHandler();
        $userData = $jwtHandler->validateToken($token);

        if (!$userData) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Invalid token']);
        }

        // Store user data in request for later use
        $request->user = $userData;
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after
    }
}