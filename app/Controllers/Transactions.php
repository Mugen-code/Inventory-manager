<?php

namespace App\Controllers;

use App\Libraries\BaseValidator;
use App\Libraries\ErrorLogger;

class Transactions extends BaseController
{
    protected $transactionModel;
    protected $productModel;

    public function __construct()
    {
        $this->transactionModel = new \App\Models\TransactionModel();
        $this->productModel = new \App\Models\ProductModel();
    }

    public function index()
    {
        try {
            $data = [
                'title' => 'Transactions',
                'transactions' => $this->transactionModel->getTransactionHistory()
            ];
            return view('transactions/index', $data);
        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Transaction listing exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'An error occurred while loading transactions');
        }
    }

    public function create()
    {
        try {
            $data = [
                'title' => 'New Transaction',
                'products' => $this->productModel->findAll()
            ];
            return view('transactions/create', $data);
        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Transaction create form exception', [
                'message' => $e->getMessage()
            ]);
            
            return redirect()->to('/transactions')
                ->with('error', 'Unable to load transaction form');
        }
    }

    public function store()
    {
        try {
            ErrorLogger::logError('debug', 'Transaction store method called');
            
            $rules = BaseValidator::getTransactionRules();

            if (!$this->validate($rules)) {
                ErrorLogger::logError('validation', 'Transaction validation failed', [
                    'errors' => $this->validator->getErrors(),
                    'input' => $this->request->getPost()
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $data = [
                'product_id' => $this->request->getPost('product_id'),
                'type' => $this->request->getPost('type'),
                'quantity' => $this->request->getPost('quantity'),
                'user_id' => session()->get('id'),
                'notes' => $this->request->getPost('notes')
            ];

            ErrorLogger::logError('debug', 'Starting transaction', [
                'transaction_data' => $data
            ]);

            // Check product stock for outbound transactions
            if ($data['type'] === 'outbound') {
                $product = $this->productModel->find($data['product_id']);
                if ($product['stock'] < $data['quantity']) {
                    ErrorLogger::logError('validation', 'Insufficient stock', [
                        'product_id' => $data['product_id'],
                        'available_stock' => $product['stock'],
                        'requested_quantity' => $data['quantity']
                    ]);
                    
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Insufficient stock. Available: ' . $product['stock']);
                }
            }

            if ($this->transactionModel->logTransaction($data)) {
                ErrorLogger::logError('info', 'Transaction recorded successfully', [
                    'transaction_data' => $data
                ]);
                
                return redirect()->to('transactions')
                    ->with('message', 'Transaction recorded successfully');
            }

            ErrorLogger::logError('error', 'Transaction failed to save', [
                'transaction_data' => $data
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to record transaction');

        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Transaction store exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $this->request->getPost()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while processing the transaction');
        }
    }

    // Helper method to get transaction details
    private function getTransactionDetails($transactionId)
    {
        try {
            $transaction = $this->transactionModel->getTransactionHistory([
                'transaction_id' => $transactionId
            ]);

            if (empty($transaction)) {
                throw new \Exception('Transaction not found');
            }

            return reset($transaction);
        } catch (\Exception $e) {
            ErrorLogger::logError('system', 'Get transaction details failed', [
                'transaction_id' => $transactionId,
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}