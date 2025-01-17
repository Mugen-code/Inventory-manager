<?php
namespace App\Libraries;

class ErrorLogger
{
    public static function logError($type, $message, $context = [])
    {
        $logger = service('logger');
        
        $logMessage = sprintf(
            "[%s] %s | User: %s | IP: %s | Details: %s",
            strtoupper($type),
            $message,
            session()->get('username') ?? 'Guest',
            service('request')->getIPAddress(),
            json_encode($context)
        );

        switch ($type) {
            case 'validation':
                $logger->notice($logMessage);
                break;
            case 'transaction':
                $logger->warning($logMessage);
                break;
            case 'security':
                $logger->error($logMessage);
                break;
            default:
                $logger->info($logMessage);
        }
    }
}