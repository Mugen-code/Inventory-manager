<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Libraries\WebSocketServer;

class StartWebSocketServer extends BaseCommand
{
    protected $group       = 'Development';
    protected $name       = 'websocket:serve';
    protected $description = 'Starts the WebSocket server';

    public function run(array $params): void
    {
        try {
            $ws = new WebSocketServer();
            $server = IoServer::factory(
                new HttpServer(
                    new WsServer($ws)
                ),
                8090,
                '127.0.0.1'  // Explicitly bind to localhost
            );

            CLI::write('WebSocket Server started on ws://127.0.0.1:8090');
            $server->run();
        } catch (\Exception $e) {
            CLI::error($e->getMessage());
        }
    }
}