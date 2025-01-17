<?php

namespace App\Controllers;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new \App\Models\UserModel();
    }

    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Users Management',
            'users' => $this->userModel->findAll()
        ];
        
        return view('users/index', $data);
    }
}