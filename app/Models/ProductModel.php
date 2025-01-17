<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'sku', 'price', 'stock', 'category_id', 'created_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField  = '';
    
    // Validation rules
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]',
        'sku' => 'required|is_unique[products.sku,id,{id}]',
        'price' => 'required|numeric|greater_than[0]',
        'stock' => 'required|integer|greater_than_equal_to[0]',
        'category_id' => 'required|integer|is_not_unique[categories.id]'
    ];

    // Get products with category information
    public function getProductsWithCategory($limit = null, $offset = 0)
    {
        return $this->select('products.*, categories.name as category_name')
                    ->join('categories', 'categories.id = products.category_id')
                    ->findAll($limit, $offset);
    }

    public function search($term = '', $category = null)
    {
        $query = $this->select('products.*, categories.name as category_name')
                     ->join('categories', 'categories.id = products.category_id');
        
        if ($term) {
            $query->groupStart()
                  ->like('products.name', $term)
                  ->orLike('products.sku', $term)
                  ->groupEnd();
        }
        
        if ($category) {
            $query->where('products.category_id', $category);
        }
        
        return $query->findAll();
    }
    // Add this method
public function getTopProducts($limit = 5)
{
    return $this->select('products.*, categories.name as category_name, 
                         COUNT(transactions.id) as transaction_count')
                ->join('categories', 'categories.id = products.category_id')
                ->join('transactions', 'transactions.product_id = products.id', 'left')
                ->where('transactions.type', 'outbound')
                ->groupBy('products.id')
                ->orderBy('transaction_count', 'DESC')
                ->findAll($limit);
}

public function getLowStockProducts($threshold = 10)
{
    return $this->select('products.*, categories.name as category_name')
                ->join('categories', 'categories.id = products.category_id')
                ->where('stock <', $threshold)
                ->findAll();
}
}