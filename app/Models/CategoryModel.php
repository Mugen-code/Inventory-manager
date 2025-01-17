<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description'];
    
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]',
        'description' => 'permit_empty|max_length[1000]'
    ];

    public function getCategoryWithProductCount()
    {
        return $this->select('categories.*, COUNT(products.id) as product_count')
                    ->join('products', 'products.category_id = categories.id', 'left')
                    ->groupBy('categories.id')
                    ->findAll();
    }
}