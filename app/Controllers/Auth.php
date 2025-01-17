<?php
namespace App\Controllers;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new \App\Models\UserModel();
    }

    public function register()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }
        return view('auth/register');
    }

    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }
        return view('auth/login', ['title' => 'Login']);
    }

    public function doRegister()
    {
        $rules = [
            'username' => 'required|min_length[4]|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'user'
        ];

        $this->userModel->insert($data);
        return redirect()->to('auth/login')->with('message', 'Registration successful. Please login.');
    }

    public function doLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)->first();
        log_message('debug', 'Login attempt - User found: ' . ($user ? 'Yes' : 'No') . ', Username: ' . $username);
        if ($user) {
        log_message('debug', 'Password verification: ' . (password_verify($password, $user['password']) ? 'Success' : 'Failed'));
}

        if ($user && password_verify($password, $user['password'])) {
            $sessionData = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'isLoggedIn' => true
            ];
            session()->set($sessionData);
            log_message('debug', 'Login successful. Session data: ' . json_encode($sessionData));
            return redirect()->to('/');
        }

        return redirect()->back()->with('error', 'Invalid username or password');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('auth/login');
    }
}