<?php
namespace App\Controllers;

class Reports extends BaseController
{
    protected $productModel;
    protected $transactionModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->productModel = new \App\Models\ProductModel();
        $this->transactionModel = new \App\Models\TransactionModel();
        $this->categoryModel = new \App\Models\CategoryModel();
    }

    public function index()
    {
        $data = [
            'lowStockProducts' => $this->productModel->getLowStockProducts(10),
            'topSellingProducts' => $this->productModel->getTopProducts(5),
            'recentTransactions' => $this->transactionModel->getRecentTransactions(5),
            'transactionSummary' => $this->transactionModel->getTransactionSummary()
        ];

        return view('reports/index', $data);
    }

    public function lowStock()
    {
        $threshold = $this->request->getGet('threshold') ?? 10;
        
        $data = [
            'products' => $this->productModel->getLowStockProducts($threshold),
            'threshold' => $threshold
        ];

        return view('reports/low_stock', $data);
    }

    public function transactions()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        
        $data = [
            'transactions' => $this->transactionModel->getTransactionHistory([
                'start_date' => $startDate,
                'end_date' => $endDate
            ]),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'summary' => $this->transactionModel->getTransactionSummary($startDate, $endDate)
        ];

        return view('reports/transactions', $data);
    }

    public function topProducts()
    {
        $limit = $this->request->getGet('limit') ?? 10;
        
        $data = [
            'products' => $this->productModel->getTopProducts($limit),
            'limit' => $limit
        ];

        return view('reports/top_products', $data);
    }
}