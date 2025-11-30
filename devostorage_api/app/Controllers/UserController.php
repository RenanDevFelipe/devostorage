<?php 

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserController extends ResourceController
{
    protected $modelName = UserModel::class;
    protected $format = 'json';
    protected $request;

    public function __construct()
    {
        $this->request = request();
    }

    // Listar todos (GET /users)
    public function index()
    {
        $users = $this->model->findAll();
        // ocultar passwords
        array_walk($users, function (&$u) { unset($u['password']); });
        return $this->respond($users);
    }

    // Mostrar 1 usuário (GET /users/{id})
    public function show($id = null)
    {
        $user = $this->model->find($id);
        if (!$user) return $this->failNotFound("Usuário não encontrado.");
        unset($user['password']);
        return $this->respond($user);
    }

    // Criar usuário (POST /users) - registro
    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        // validação básica (model valida ao inserir)
        if (!$this->model->insert($data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        $userId = $this->model->getInsertID();
        $user = $this->model->find($userId);
        unset($user['password']);

        return $this->respondCreated($user);
    }

    // Atualizar (PUT/PATCH /users/{id})
    public function update($id = null)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();

        // impedir alteração de email para um que já exista (model's rule handles is_unique)
        // ajustar a regra is_unique para permitir o próprio id já foi feito no model com {id}
        // porém, CodeIgniter precisa que o placeholder seja substituído — para simplicidade, vamos setar a regra dinamicamente
        $this->model->setValidationRules([
            'nome' => 'required|min_length[3]|max_length[255]',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'tipo' => 'required|in_list[administrador,funcionario]',
        ]);

        if (isset($data['password']) && $data['password'] === '') {
            unset($data['password']);
        }

        if (!$this->model->update($id, $data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        $user = $this->model->find($id);
        unset($user['password']);
        return $this->respond($user);
    }

    // Deletar (DELETE /users/{id})
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Usuário não encontrado.');
        }

        $this->model->delete($id);
        return $this->respondDeleted(['id' => $id]);
    }

    /**
     * Login (POST /login)
     * Body: { "email": "x@x.com", "password": "senha" }
     *
     * Retorna: { access_token, token_type, expires_in, user }
     */
    public function login()
    {
        $request = $this->request->getJSON(true);
        if (!$request) $request = $this->request->getPost();

        $email = $request['email'] ?? null;
        $password = $request['password'] ?? null;

        if (!$email || !$password) {
            return $this->failValidationErrors('Email e senha são obrigatórios.');
        }

        $user = $this->model->findByEmail($email);
        if (!$user) {
            return $this->failNotFound('Credenciais inválidas.');
        }

        if (!password_verify($password, $user['password'])) {
            return $this->fail('Credenciais inválidas.', 401);
        }

        // gerar JWT
        $secret = env('app.JWT_SECRET');
        if (!$secret) {
            return $this->failServerError('JWT_SECRET não configurado no .env (app.JWT_SECRET).');
        }

        $issuedAt   = time();
        $expire     = $issuedAt + (60 * 60 * 24); // 24h
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'sub' => $user['id'],
            'email' => $user['email'],
            'tipo' => $user['tipo'],
        ];

        $token = JWT::encode($payload, $secret, 'HS256');

        unset($user['password']);
        return $this->respond([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => $expire,
            'user'         => $user,
        ]);
    }

    // Exemplo de endpoint protegido (GET /me)
    public function me()
    {
        $auth = $this->request->getHeaderLine('Authorization');
        if (!$auth) return $this->failUnauthorized('Header Authorization ausente.');

        if (strpos($auth, 'Bearer ') !== 0) return $this->failUnauthorized('Token inválido.');

        $token = trim(str_replace('Bearer', '', $auth));

        $secret = env('app.JWT_SECRET');
        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            $userId = $decoded->sub ?? null;

            if (!$userId) return $this->failUnauthorized('Token inválido.');

            $user = $this->model->find($userId);
            if (!$user) return $this->failNotFound('Usuário não encontrado.');

            unset($user['password']);
            return $this->respond($user);
        } catch (\Exception $e) {
            return $this->failUnauthorized('Token inválido: ' . $e->getMessage());
        }
    }
}
