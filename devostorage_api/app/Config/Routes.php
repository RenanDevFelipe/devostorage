<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Rotas de download (sem JWT filter)
$routes->get('download/(:any)', 'DownloadController::arquivo/$1');

$routes->options('api/(:any)', function($path){
    return service('response')
        ->setStatusCode(200)
        ->setHeader('Access-Control-Allow-Origin', '*') // ou seu domínio permitido
        ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->send();
});

$routes->group('api', function ($routes) {

    // Rotas para usuários
    $routes->get('users/me', 'UserController::me');
    $routes->post('users/login', 'UserController::login');
    $routes->resource('users', ['controller' => 'UserController', 'filter' => 'jwt']);

    // Rotas para produtos
    $routes->resource('produtos', ['controller' => 'ProdutoController', 'filter' => 'jwt']);

    // Movimentações (protegidas por JWT)
    $routes->get('movimentacoes', 'MovimentacaoController::index', ['filter' => 'jwt']);
    $routes->post('movimentacoes/entrada', 'MovimentacaoController::entrada', ['filter' => 'jwt']);
    $routes->post('movimentacoes/saida', 'MovimentacaoController::saida', ['filter' => 'jwt']);

    $routes->group('relatorios', ['filter' => 'jwt'], function ($routes) {
        // Relatórios JSON
        $routes->get('estoque', 'RelatorioController::estoque');
        $routes->get('movimentacoes', 'RelatorioController::movimentacoes');
        $routes->get('produto/(:num)/movimentacoes', 'RelatorioController::movimentacoesProduto/$1');

        // Exportar estoque
        $routes->get('estoque/pdf', 'RelatorioController::estoquePdf');
        $routes->get('estoque/excel', 'RelatorioController::estoqueExcel');

        // Exportar movimentações
        $routes->get('movimentacoes/pdf', 'RelatorioController::movimentacoesPdf');
        $routes->get('movimentacoes/excel', 'RelatorioController::movimentacoesExcel');
    });

    // Download de arquivos (com JWT filter)
    $routes->get('download/(:any)', 'DownloadController::downloadJson/$1');
    $routes->get('downloads', 'DownloadController::listar', ['filter' => 'jwt']);
});