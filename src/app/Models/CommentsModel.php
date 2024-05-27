<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentsModel extends Model
{
    protected $table = 'comments';
    protected $allowedFields = ['name', 'text', 'date'];

    protected $validationRules = [
        'name' => 'required|valid_email',
        'text'  => 'required|min_length[10]'
    ];

    protected $validationMessages = [
        'name' => [
            'required'    => 'Name is required',
            'valid_email' => 'Please enter a valid email address'
        ],
        'text' => [
            'required' => 'Comment text is required',
            'min_length' => 'min length = 10'
        ]
    ];
}
