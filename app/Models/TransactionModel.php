<?php
namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['product_id', 'type', 'quantity', 'user_id', 'notes'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField  = '';
    
    public function logTransaction($data)
    {
        $db = \Config\Database::connect();
        
        try {
            $db->transStart();
            
            // Debug log
            log_message('debug', 'Starting transaction: ' . json_encode($data));
            
        
            $this->insert($data);
            
           
            $productModel = new ProductModel();
            $product = $productModel->find($data['product_id']);
            
            if (!$product) {
                throw new \Exception('Product not found: ' . $data['product_id']);
            }
            
            $newStock = $data['type'] === 'inbound' 
                ? $product['stock'] + $data['quantity']
                : $product['stock'] - $data['quantity'];
                
            if ($newStock < 0) {
                throw new \Exception('Insufficient stock. Current: ' . $product['stock'] . ', Requested: ' . $data['quantity']);
            }
            
            $productModel->update($data['product_id'], ['stock' => $newStock]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }
                // After successful transaction, broadcast update
            $updatedProduct = $productModel->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->find($data['product_id']);

             $this->broadcastStockUpdate($updatedProduct);
            
            return true;
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Transaction failed: ' . $e->getMessage());
            return false;
        }
    }

    private function broadcastStockUpdate($product)
{
    try {
        $client = new \WebSocket\Client("ws://127.0.0.1:8090");
        $client->send(json_encode([
            'type' => 'stock_update',
            'product' => $product
        ]));
        $client->close();
    } catch (\Exception $e) {
        log_message('error', 'WebSocket broadcast failed: ' . $e->getMessage());
    }
}

    public function getTransactionHistory()
    {
        return $this->select('transactions.*, products.name as product_name, users.username')
                    ->join('products', 'products.id = transactions.product_id')
                    ->join('users', 'users.id = transactions.user_id')
                    ->orderBy('transactions.created_at', 'DESC')
                    ->findAll();
    }
    
    public function getRecentTransactions($limit = 5)
    {
        return $this->select('transactions.*, products.name as product_name, 
                            users.username')
                    ->join('products', 'products.id = transactions.product_id')
                    ->join('users', 'users.id = transactions.user_id')
                    ->orderBy('transactions.created_at', 'DESC')
                    ->findAll($limit);
    }

    public function getTransactionSummary($startDate = null, $endDate = null)
{
    $builder = $this->select('type, SUM(quantity) as total_quantity')
                   ->groupBy('type');
    
    if ($startDate) {
        $builder->where('created_at >=', $startDate);
    }
    
    if ($endDate) {
        $builder->where('created_at <=', $endDate);
    }
    
    $result = $builder->findAll();
    
    $summary = [
        'inbound' => 0,
        'outbound' => 0
    ];
    
    foreach ($result as $row) {
        $summary[$row['type']] = $row['total_quantity'];
    }
    
    return $summary;
}


}
