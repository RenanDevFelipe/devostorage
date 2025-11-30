<?php namespace App\Models;

use CodeIgniter\Model;

class ProdutoModel extends Model
{
    protected $table      = 'produtos';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'nome', 'categoria', 'quantidade', 'preco'
    ];

    protected $useTimestamps = true;

    protected $validationRules = [
        'nome'       => 'required|min_length[2]',
        'categoria'  => 'permit_empty|max_length[150]',
        'quantidade' => 'permit_empty|integer',
        'preco'      => 'permit_empty|decimal',
    ];

    protected $validationMessages = [];
}
