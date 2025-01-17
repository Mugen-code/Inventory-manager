<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use CodeIgniter\RESTful\ResourceController;
use App\Libraries\BaseValidator;
use App\Libraries\ErrorLogger;

class Categories extends ResourceController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        try {
            $data = [
                'title' => 'Categories',
                'categories' => $this->categoryModel->getCategoryWithProductCount()
            ];
            
            return view('categories/index', $data);
        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Category listing exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'An error occurred while loading categories');
        }
    }

    public function new()
    {
        try {
            $data = [
                'title' => 'Create Category'
            ];
            return view('categories/create', $data);
        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Category create form exception', [
                'message' => $e->getMessage()
            ]);
            
            return redirect()->to('/categories')
                ->with('error', 'Unable to load create form');
        }
    }

    public function create()
    {
        try {
            $rules = [
                'name' => [
                    'rules' => 'required|min_length[3]|max_length[255]|is_unique[categories.name]',
                    'errors' => [
                        'required' => 'Category name is required',
                        'min_length' => 'Category name must be at least 3 characters long',
                        'max_length' => 'Category name cannot exceed 255 characters',
                        'is_unique' => 'A category with this name already exists'
                    ]
                ],
                'description' => [
                    'rules' => 'permit_empty|max_length[1000]',
                    'errors' => [
                        'max_length' => 'Description cannot exceed 1000 characters'
                    ]
                ]
            ];

            if (!$this->validate($rules)) {
                ErrorLogger::logError('validation', 'Category creation failed', [
                    'errors' => $this->validator->getErrors(),
                    'input' => $this->request->getPost()
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $data = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description')
            ];

            $this->categoryModel->insert($data);
            
            ErrorLogger::logError('info', 'Category created successfully', [
                'category_data' => $data
            ]);

            return redirect()->to('/categories')
                ->with('message', 'Category created successfully');

        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Category creation exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the category');
        }
    }

    public function edit($id = null)
    {
        try {
            if ($id === null) {
                ErrorLogger::logError('validation', 'Category edit failed - No ID specified');
                return redirect()->to('/categories')
                    ->with('error', 'Category ID not specified');
            }

            $category = $this->categoryModel->find($id);
            
            if ($category === null) {
                ErrorLogger::logError('validation', 'Category edit failed - Category not found', [
                    'category_id' => $id
                ]);
                return redirect()->to('/categories')
                    ->with('error', 'Category not found');
            }

            $data = [
                'title' => 'Edit Category',
                'category' => $category
            ];

            return view('categories/edit', $data);

        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Category edit form exception', [
                'category_id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return redirect()->to('/categories')
                ->with('error', 'Unable to load edit form');
        }
    }

    public function update($id = null)
    {
        try {
            if ($id === null) {
                ErrorLogger::logError('validation', 'Category update failed - No ID specified');
                return redirect()->to('/categories')
                    ->with('error', 'Category ID not specified');
            }

            $rules = [
                'name' => [
                    'rules' => "required|min_length[3]|max_length[255]|is_unique[categories.name,id,{$id}]",
                    'errors' => [
                        'required' => 'Category name is required',
                        'min_length' => 'Category name must be at least 3 characters long',
                        'max_length' => 'Category name cannot exceed 255 characters',
                        'is_unique' => 'A category with this name already exists'
                    ]
                ],
                'description' => [
                    'rules' => 'permit_empty|max_length[1000]',
                    'errors' => [
                        'max_length' => 'Description cannot exceed 1000 characters'
                    ]
                ]
            ];

            if (!$this->validate($rules)) {
                ErrorLogger::logError('validation', 'Category update validation failed', [
                    'errors' => $this->validator->getErrors(),
                    'input' => $this->request->getPost()
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $data = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description')
            ];

            $this->categoryModel->update($id, $data);
            
            ErrorLogger::logError('info', 'Category updated successfully', [
                'category_id' => $id,
                'updated_data' => $data
            ]);

            return redirect()->to('/categories')
                ->with('message', 'Category updated successfully');

        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Category update exception', [
                'category_id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the category');
        }
    }


    public function delete($id = null)
    {
        try {
            if ($id === null) {
                ErrorLogger::logError('validation', 'Category deletion failed - No ID specified');
                return redirect()->to('/categories')
                    ->with('error', 'Category ID not specified');
            }

            // Check if category has products
            $category = $this->categoryModel->getCategoryWithProductCount();
            $category = array_filter($category, function($cat) use ($id) {
                return $cat['id'] == $id;
            });
            $category = reset($category);
            $productCount = $category['product_count'] ?? 0;

            if ($productCount > 0) {
                ErrorLogger::logError('validation', 'Category deletion failed - Has products', [
                    'category_id' => $id,
                    'product_count' => $productCount
                ]);
                return redirect()->back()
                    ->with('error', 'Cannot delete category with existing products');
            }

            $this->categoryModel->delete($id);
            
            ErrorLogger::logError('info', 'Category deleted successfully', [
                'category_id' => $id
            ]);

            return redirect()->to('/categories')
                ->with('message', 'Category deleted successfully');

        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Category deletion exception', [
                'category_id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return redirect()->to('/categories')
                ->with('error', 'Failed to delete category');
        }
    }
}