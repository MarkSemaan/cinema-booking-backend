<?php

require_once 'model.php';

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['username', 'email', 'password', 'is_admin'];
}
