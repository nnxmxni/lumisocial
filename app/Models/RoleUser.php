<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $table = 'role_users';

    protected $fillable = ['role_id', 'user_id'];

    protected $hidden = ['created_at', 'updated_at'];
}
