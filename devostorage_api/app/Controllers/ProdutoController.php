<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProdutoModel;

class ProdutoController extends ResourceController
{
    protected $modelName = ProdutoModel::class;
    protected $format = 'json';
    protected $request;

    public function __construct()
    {
        $this->request = request();
    }

    // GET /produtos
    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    // GET /produtos/{id}
    public function show($id = null)
    {
        $produto = $this->model->find($id);
        if (!$produto)
            return $this->failNotFound("Produto não encontrado.");

        return $this->respond($produto);
    }

    // POST /produtos
    public function create()
    {
        $data = $this->request->getJSON(true);

        if (!$this->model->insert($data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        $data['id'] = $this->model->getInsertID();
        return $this->respondCreated($data);
    }

    // PUT or PATCH /produtos/{id}
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (!$this->model->update($id, $data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respond($this->model->find($id));
    }

    // DELETE /produtos/{id}
    public function delete($id = null)
    {
        if (!$this->model->find($id))
            return $this->failNotFound("Produto não encontrado.");

        $this->model->delete($id);
        return $this->respondDeleted(['id' => $id]);
    }
}
