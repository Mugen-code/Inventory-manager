<?php

namespace App\Models;

use CodeIgniter\Model;
use SebastianBergmann\Type\FalseType;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password', 'role'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField  = '';
    
    protected $validationRules = [
        'username' => 'required|min_length[4]|is_unique[users.username,id,{id}]',
        'password' => 'required|min_length[6]',
        'role' => 'required|in_list[admin,user]'
    ];

    public function findUserByUsername($username)
    {
        return $this->where('username', $username)
                    ->first();
    }

    public function isAdmin($userId)
    {
        $user = $this->find($userId);
        return $user && $user['role'] === 'admin';
    }
}