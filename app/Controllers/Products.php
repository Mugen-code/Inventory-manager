<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use CodeIgniter\RESTful\ResourceController;
use App\Libraries\BaseValidator;
use App\Libraries\ErrorLogger;

class Products extends ResourceController
{
    protected $productModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        try {
            $search = $this->request->getGet('search');
            $category = $this->request->getGet('category');
            
            // Start with base query
            $query = $this->productModel->select('products.*, categories.name as category_name')
                                       ->join('categories', 'categories.id = products.category_id');
            
            // Apply search filters
            if ($search) {
                $query->groupStart()
                      ->like('products.name', $search)
                      ->orLike('products.sku', $search)
                      ->groupEnd();
            }
            
            if ($category) {
                $query->where('category_id', $category);
            }
            
            $data = [
                'products' => $query->findAll(),
                'categories' => $this->categoryModel->findAll(),
                'search' => $search,
                'selectedCategory' => $category
            ];

            return view('products/index', $data);

        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Product listing exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'An error occurred while loading products');
        }
    }

    public function new()
    {
        try {
            $data = [
                'categories' => $this->categoryModel->findAll()
            ];
            
            return view('products/create', $data);

        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Product create form exception', [
                'message' => $e->getMessage()
            ]);
            
            return redirect()->to('/products')
                ->with('error', 'Unable to load create form');
        }
    }

    public function create()
    {
        try {
            $rules = BaseValidator::getProductRules();
            
            if (!$this->validate($rules)) {
                ErrorLogger::logError('validation', 'Product creation failed', [
                    'errors' => $this->validator->getErrors(),
                    'input' => $this->request->getPost()
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $data = [
                'name' => $this->request->getPost('name'),
                'sku' => $this->request->getPost('sku'),
                'price' => $this->request->getPost('price'),
                'stock' => $this->request->getPost('stock'),
                'category_id' => $this->request->getPost('category_id')
            ];

            $this->productModel->insert($data);
            
            ErrorLogger::logError('info', 'Product created successfully', [
                'product_data' => $data
            ]);

            return redirect()->to('/products')
                ->with('message', 'Product created successfully');
                
        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Product creation exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the product');
        }
    }

    public function edit($id = null)
    {
        try {
            if ($id === null) {
                ErrorLogger::logError('validation', 'Product edit failed - No ID specified');
                return redirect()->to('/products')->with('error', 'Product ID not specified');
            }

            $data = [
                'product' => $this->productModel->find($id),
                'categories' => $this->categoryModel->findAll()
            ];

            if ($data['product'] === null) {
                ErrorLogger::logError('validation', 'Product edit failed - Product not found', [
                    'product_id' => $id
                ]);
                return redirect()->to('/products')->with('error', 'Product not found');
            }

            return view('products/edit', $data);

        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Product edit form exception', [
                'product_id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return redirect()->to('/products')
                ->with('error', 'Unable to load edit form');
        }
    }

    public function update($id = null)
    {
        try {
            if ($id === null) {
                ErrorLogger::logError('validation', 'Product update failed - No ID specified');
                return redirect()->to('/products')->with('error', 'Product ID not specified');
            }

            $rules = BaseValidator::getProductRules(true, $id);
            
            if (!$this->validate($rules)) {
                ErrorLogger::logError('validation', 'Product update validation failed', [
                    'errors' => $this->validator->getErrors(),
                    'input' => $this->request->getPost()
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $data = [
                'name' => $this->request->getPost('name'),
                'sku' => $this->request->getPost('sku'),
                'price' => $this->request->getPost('price'),
                'stock' => $this->request->getPost('stock'),
                'category_id' => $this->request->getPost('category_id')
            ];

            $this->productModel->update($id, $data);
            
            ErrorLogger::logError('info', 'Product updated successfully', [
                'product_id' => $id,
                'updated_data' => $data
            ]);

            return redirect()->to('/products')->with('message', 'Product updated successfully');
            
        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Product update exception', [
                'product_id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the product');
        }
    }

    public function delete($id = null)
    {
        try {
            if ($id === null) {
                ErrorLogger::logError('validation', 'Product deletion failed - No ID specified');
                return redirect()->to('/products')->with('error', 'Product ID not specified');
            }

            // Check if product has any transactions
            $transactionModel = new \App\Models\TransactionModel();
            if ($transactionModel->where('product_id', $id)->countAllResults() > 0) {
                ErrorLogger::logError('validation', 'Product deletion failed - Has transactions', [
                    'product_id' => $id
                ]);
                return redirect()->to('/products')
                    ->with('error', 'Cannot delete product with existing transactions');
            }

            $this->productModel->delete($id);
            
            ErrorLogger::logError('info', 'Product deleted successfully', [
                'product_id' => $id
            ]);

            return redirect()->to('/products')->with('message', 'Product deleted successfully');
            
        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Product deletion exception', [
                'product_id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return redirect()->to('/products')->with('error', 'Failed to delete product');
        }
    }

    public function list()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $products = $this->productModel->getProductsWithCategory($limit, $offset);
            $total = $this->productModel->countAll();

            return $this->respond([
                'data' => $products,
                'total' => $total,
                'current_page' => $page,
                'per_page' => $limit
            ]);

        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Product API listing exception', [
                'message' => $e->getMessage()
            ]);
            
            return $this->fail('Failed to retrieve products');
        }
    }
}