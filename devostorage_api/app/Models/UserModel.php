<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'nome', 'email', 'password', 'tipo'
    ];

    // timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation rules (used se chamar $model->insert() ou $model->update() com $skipValidation=false)
    protected $validationRules = [
        'nome'     => 'required|min_length[3]|max_length[255]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]', // is_unique com exceção no update
        'password' => 'required|min_length[6]|max_length[255]',
        'tipo'     => 'required|in_list[administrador,funcionario]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Este email já está em uso.',
        ],
        'password' => [
            'min_length' => 'A senha deve ter pelo menos 6 caracteres.',
        ],
    ];

    // Before insert/update hooks para hashear senha
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPasswordOnUpdate'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        return $data;
    }

    // Só hash se a senha foi enviada no update
    protected function hashPasswordOnUpdate(array $data)
    {
        if (isset($data['data']['password']) && $data['data']['password'] !== '') {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            // garante que não sobrescreva senha vazia no update
            unset($data['data']['password']);
        }

        return $data;
    }

    // busca por email
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }
}
