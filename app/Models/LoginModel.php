<?php

namespace App\Models;
use CodeIgniter\Model;

class LoginModel extends Model
{
    protected $table      = 'users_login';
    protected $primaryKey = 'id_user';

    protected $allowedFields = [
        'nama_lengkap',
        'username',
        'password',
        'role',
        'last_active',
        'created_at',
        'updated_at',
        'disabled_at',
        'created_by',
        'updated_by',
        'disabled_by'
    ];
}
