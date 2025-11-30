<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Token não informado.']);
        }

        $token = trim(substr($header, 7));

        $secret = getenv('app.JWT_SECRET');

        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));

            // Normaliza payload para garantir que exista a chave 'id'
            $payload = (array) $decoded;
            if (isset($payload['sub']) && !isset($payload['id'])) {
                $payload['id'] = $payload['sub'];
            }

            // Salva o usuário autenticado no serviço global
            service('authUser')->setUser($payload);

        } catch (\Exception $e) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Token inválido ou expirado.']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
