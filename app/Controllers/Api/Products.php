<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Products extends ResourceController
{
    use ResponseTrait;

    protected $productModel;

    public function __construct()
    {
        $this->productModel = new \App\Models\ProductModel();
    }

    // GET /api/products
    public function index()
    {
        $page = $this->request->getGet('page') ?? 1;
        $limit = $this->request->getGet('limit') ?? 10;
        $search = $this->request->getGet('search');
        $category = $this->request->getGet('category');

        $offset = ($page - 1) * $limit;

        $query = $this->productModel->select('products.*, categories.name as category_name')
                                  ->join('categories', 'categories.id = products.category_id');

        if ($search) {
            $query->groupStart()
                  ->like('products.name', $search)
                  ->orLike('products.sku', $search)
                  ->groupEnd();
        }

        if ($category) {
            $query->where('category_id', $category);
        }

        $total = $query->countAllResults(false);
        $products = $query->limit($limit, $offset)->find();

        return $this->respond([
            'data' => $products,
            'total' => $total,
            'page' => (int)$page,
            'total_pages' => ceil($total / $limit)
        ]);
    }

    // POST /api/products
    public function create()
    {
        log_message('debug', 'Starting create product with data: ' . json_encode($this->request->getJSON()));
        
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'sku' => 'required|is_unique[products.sku]',
            'price' => 'required|numeric|greater_than[0]',
            'stock' => 'required|integer|greater_than_equal_to[0]',
            'category_id' => 'required|integer|is_not_unique[categories.id]'
        ];
    
        if (!$this->validate($rules)) {
            log_message('error', 'Validation failed: ' . json_encode($this->validator->getErrors()));
            return $this->fail($this->validator->getErrors());
        }
    
        $data = $this->request->getJSON(true);
        log_message('debug', 'Attempting to insert product with data: ' . json_encode($data));
    
        try {
            $productId = $this->productModel->insert($data);
            log_message('debug', 'Product created with ID: ' . $productId);
            $product = $this->productModel->find($productId);
            return $this->respondCreated(['product' => $product]);
        } catch (\Exception $e) {
            log_message('error', 'Error creating product: ' . $e->getMessage());
            return $this->fail($e->getMessage());
        }
    }

    // GET /api/products/:id
    public function show($id = null)
    {
        $product = $this->productModel->select('products.*, categories.name as category_name')
                                     ->join('categories', 'categories.id = products.category_id')
                                     ->where('products.id', $id)
                                     ->first();
                                     
        if (!$product) {
            return $this->failNotFound('Product not found');
        }
        return $this->respond(['product' => $product]);
    }

    // PUT /api/products/:id
    public function update($id = null)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->failNotFound('Product not found');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'sku' => "required|is_unique[products.sku,id,{$id}]",
            'price' => 'required|numeric|greater_than[0]',
            'stock' => 'required|integer|greater_than_equal_to[0]',
            'category_id' => 'required|integer|is_not_unique[categories.id]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getRawInput()['name'],
            'sku' => $this->request->getRawInput()['sku'],
            'price' => $this->request->getRawInput()['price'],
            'stock' => $this->request->getRawInput()['stock'],
            'category_id' => $this->request->getRawInput()['category_id']
        ];

        try {
            $this->productModel->update($id, $data);
            $product = $this->productModel->find($id);
            return $this->respond(['product' => $product]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    // DELETE /api/products/:id
    public function delete($id = null)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->failNotFound('Product not found');
        }

        try {
            $this->productModel->delete($id);
            return $this->respondDeleted(['message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function import()
{
    // Validate file upload
    $file = $this->request->getFile('csv_file');
    if (!$file || !$file->isValid()) {
        return $this->fail('No valid CSV file provided');
    }

    if ($file->getExtension() !== 'csv') {
        return $this->fail('File must be a CSV');
    }

    try {
        // Read and parse CSV
        $handle = fopen($file->getTempName(), 'r');
        
        // Read header row
        $headers = fgetcsv($handle);
        $required_headers = ['name', 'sku', 'price', 'stock', 'category_id'];
        
        // Validate headers
        if (!$headers || array_diff($required_headers, array_map('strtolower', $headers))) {
            return $this->fail('Invalid CSV format. Required columns: ' . implode(', ', $required_headers));
        }

        $products = [];
        $errors = [];
        $row_number = 1;

        // Read data rows
        while (($data = fgetcsv($handle)) !== FALSE) {
            $row_number++;
            if (count($data) !== count($headers)) {
                $errors[] = "Row {$row_number}: Column count mismatch";
                continue;
            }

            $product = array_combine($headers, $data);
            
            // Validate each product
            $validation = \Config\Services::validation();
            $rules = [
                'name' => 'required|min_length[3]|max_length[255]',
                'sku' => 'required|is_unique[products.sku]',
                'price' => 'required|numeric|greater_than[0]',
                'stock' => 'required|integer|greater_than_equal_to[0]',
                'category_id' => 'required|integer|is_not_unique[categories.id]'
            ];

            if (!$validation->setRules($rules)->run($product)) {
                $errors[] = "Row {$row_number}: " . implode(', ', $validation->getErrors());
                continue;
            }

            $products[] = $product;
        }

        fclose($handle);

        // If there were any errors, return them
        if (!empty($errors)) {
            return $this->fail(['errors' => $errors]);
        }

        // Insert valid products
        $inserted = 0;
        foreach ($products as $product) {
            if ($this->productModel->insert($product)) {
                $inserted++;
            }
        }

        return $this->respondCreated([
            'message' => "Successfully imported {$inserted} products",
            'total_processed' => count($products)
        ]);

    } catch (\Exception $e) {
        log_message('error', 'CSV import error: ' . $e->getMessage());
        return $this->fail('Error processing CSV file: ' . $e->getMessage());
    }
}

public function export()
{
    try {
        // Get all products with their category names
        $products = $this->productModel->select('products.*, categories.name as category_name')
                                     ->join('categories', 'categories.id = products.category_id')
                                     ->findAll();

        // Create a temporary file
        $temp_file = tempnam(sys_get_temp_dir(), 'products_export_');
        $handle = fopen($temp_file, 'w');

        // Write headers
        $headers = ['id', 'name', 'sku', 'price', 'stock', 'category_id', 'category_name', 'created_at'];
        fputcsv($handle, $headers);

        // Write data rows
        foreach ($products as $product) {
            fputcsv($handle, array_values((array)$product));
        }

        fclose($handle);

        // Read the file contents
        $file_content = file_get_contents($temp_file);
        unlink($temp_file); // Delete temporary file

        // Set headers for download
        $response = $this->response;
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="products_export_'.date('Y-m-d').'.csv"');
        $response->setHeader('Expires', '0');
        $response->setHeader('Cache-Control', 'must-revalidate');
        $response->setHeader('Pragma', 'public');

        // Send the file
        return $response->setBody($file_content);

    } catch (\Exception $e) {
        log_message('error', 'CSV export error: ' . $e->getMessage());
        return $this->fail('Error generating CSV file: ' . $e->getMessage());
    }
}
}