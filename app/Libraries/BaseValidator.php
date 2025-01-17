<?php
namespace App\Libraries;

class BaseValidator
{
    // Product validation rules
    public static function getProductRules($isUpdate = false, $id = null)
    {
        return [
            'name' => [
                'rules' => 'required|min_length[3]|max_length[255]',
                'errors' => [
                    'required' => 'Product name is required',
                    'min_length' => 'Product name must be at least 3 characters',
                    'max_length' => 'Product name cannot exceed 255 characters'
                ]
            ],
            'sku' => [
                'rules' => $isUpdate ? "required|is_unique[products.sku,id,{$id}]" : 'required|is_unique[products.sku]',
                'errors' => [
                    'required' => 'SKU is required',
                    'is_unique' => 'This SKU is already in use'
                ]
            ],
            'price' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Price is required',
                    'numeric' => 'Price must be a number',
                    'greater_than' => 'Price must be greater than 0'
                ]
            ],
            'stock' => [
                'rules' => 'required|integer|greater_than_equal_to[0]',
                'errors' => [
                    'required' => 'Stock quantity is required',
                    'integer' => 'Stock must be a whole number',
                    'greater_than_equal_to' => 'Stock cannot be negative'
                ]
            ],
            'category_id' => [
                'rules' => 'required|integer|is_not_unique[categories.id]',
                'errors' => [
                    'required' => 'Category is required',
                    'is_not_unique' => 'Selected category does not exist'
                ]
            ]
        ];
    }

    // Transaction validation rules
    public static function getTransactionRules()
    {
        return [
            'product_id' => [
                'rules' => 'required|integer|is_not_unique[products.id]',
                'errors' => [
                    'required' => 'Product selection is required',
                    'is_not_unique' => 'Selected product does not exist'
                ]
            ],
            'type' => [
                'rules' => 'required|in_list[inbound,outbound]',
                'errors' => [
                    'required' => 'Transaction type is required',
                    'in_list' => 'Invalid transaction type'
                ]
            ],
            'quantity' => [
                'rules' => 'required|integer|greater_than[0]',
                'errors' => [
                    'required' => 'Quantity is required',
                    'integer' => 'Quantity must be a whole number',
                    'greater_than' => 'Quantity must be greater than 0'
                ]
            ]
        ];
    }
}