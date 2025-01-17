<?php

namespace App\Controllers;

class Home extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $transactionModel;

    public function __construct()
    {
        $this->productModel = new \App\Models\ProductModel();
        $this->categoryModel = new \App\Models\CategoryModel();
        $this->transactionModel = new \App\Models\TransactionModel();
    }

    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('auth/login');
        }

        // Prepare dashboard data
        $data = [
            'title' => 'Dashboard',
            'totalProducts' => $this->productModel->countAll(),
            'totalCategories' => $this->categoryModel->countAll(),
            'lowStockProducts' => $this->productModel->where('stock <', 10)->countAllResults(),
            'recentTransactions' => $this->transactionModel->getRecentTransactions(5),
            'isAdmin' => session()->get('role') === 'admin'
        ];

        return view('dashboard/index', $data);
    }
}