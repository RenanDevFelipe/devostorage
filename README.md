# DevoStorage — Monorepo

> Visão unificada dos subprojetos **devostorage_api** (API) e **devostorage_web** (front-end).

![Monorepo](https://img.shields.io/badge/Monorepo-DevOS_Orange-blue?style=flat-square)

Este README principal reúne a visão geral, diagramas de caso de uso e classes, instruções rápidas de execução e links para os READMEs específicos de cada subprojeto.

## Índice
- Visão Geral
- Como rodar rapidamente
- Diagramas
  - Caso de Uso (Mermaid)
  - Diagrama de Classes (Mermaid)
- Estrutura dos Subprojetos
- Links úteis

---

## Visão Geral

O monorepo contém dois subprojetos principais:

- `devostorage_api/` — API RESTful em PHP (CodeIgniter 4) responsável por autenticação, gerenciamento de produtos, movimentações e geração de relatórios (PDF / Excel).
- `devostorage_web/` — SPA em React + TypeScript que consome a API e fornece UI para gerenciamento, dashboard e downloads.

Este README centraliza os diagramas de Caso de Uso e de Classes para facilitar entendimento arquitetural do sistema como um todo.

---

## Como rodar rapidamente (ambientes locais)

Recomenda-se abrir dois terminais separados — um para a API e outro para o front-end.

PowerShell — iniciar API (CodeIgniter):
```powershell
cd c:\xampp\htdocs\devostorange\devostorage_api
composer install
cp env .env
# editar .env conforme necessário (database, JWT_SECRET, baseURL)
php spark migrate
php spark serve
```

PowerShell — iniciar Web (Vite):
```powershell
cd c:\xampp\htdocs\devostorange\devostorage_web
npm install
npm run dev
```

Observação: configure `VITE_API_URL` no front-end (arquivo `.env` ou `src/services/api.ts`) apontando para o `baseURL` da API.

---

## Diagramas

As seções abaixo usam Mermaid para diagramas.

### Caso de Uso

Descreve as funcionalidades acessíveis por Funcionários e Administradores.

```mermaid

    actor Emp as "Funcionário"
    actor Admin as "Administrador"
    Admin --|> Emp

    package "DevOS Storage System" {
        usecase UC_Login as "Autenticar (Login)"
        usecase UC_Me as "Consultar Perfil"
        
        usecase UC_Prod as "Gerenciar Produtos (CRUD)"
        
        usecase UC_Mov as "Registrar Entrada/Saída"
        usecase UC_Hist as "Visualizar Histórico"
        
        usecase UC_Rep as "Gerar Relatórios (PDF/Excel)"
        usecase UC_Download as "Baixar Arquivos"
        
        usecase UC_Users as "Gerenciar Usuários"
    }

    Emp --> UC_Login
    Emp --> UC_Me
    Emp --> UC_Prod
    Emp --> UC_Mov
    Emp --> UC_Hist
    Emp --> UC_Rep
    Emp --> UC_Download

    Admin --> UC_Users
```

### Diagrama de Classes (API)

Mostra a estrutura do backend, destacando a separação entre Controllers, Services e Models, e como o ReportGenerator orquestra os dados.

```mermaid
classDiagram
    %% Classes Base do CodeIgniter
    class ResourceController {
        <<Framework>>
    }
    class Model {
        <<Framework>>
    }

    %% Controllers da Aplicação
    class UserController {
        +login()
        +me()
        +create()
    }
    class MovimentacaoController {
        +entrada()
        +saida()
        -registrarMovimentacao()
    }
    class RelatorioController {
        +estoquePdf()
        +estoqueExcel()
        +movimentacoesPdf()
    }
    class DownloadController {
        +arquivo()
        +listar()
    }

    %% Herança
    ResourceController <|-- UserController
    ResourceController <|-- MovimentacaoController
    ResourceController <|-- RelatorioController
    
    %% Services e Models
    class ReportGenerator {
        +gerarPdfEstoque()
        +gerarExcelMovimentacoes()
    }
    class AuthUser {
        +id()
        +tipo()
    }

    class MovimentacaoModel {
        +comDetalhes()
        +porProduto()
    }
    class ProdutoModel {
        +atualizarEstoque()
    }

    Model <|-- MovimentacaoModel
    Model <|-- ProdutoModel

    %% Relacionamentos
    RelatorioController --> ReportGenerator : "Usa para gerar arquivos"
    ReportGenerator ..> MovimentacaoModel : "Lê dados"
    ReportGenerator ..> ProdutoModel : "Lê dados"
    
    MovimentacaoController ..> MovimentacaoModel : "Grava histórico"
    MovimentacaoController ..> ProdutoModel : "Atualiza saldo"
    MovimentacaoController ..> AuthUser : "Verifica usuário"
```

---

## Estrutura dos Subprojetos

- `devostorage_api/` — veja `devostorage_api/README.md` para documentação detalhada da API: endpoints, configuração, migrações e exemplos.
- `devostorage_web/` — veja `devostorage_web/README.md` para instruções do front-end, arquitetura de componentes e setup de desenvolvimento.

---

## Links úteis

- README da API: `devostorage_api/README.md`
- README do Front-end: `devostorage_web/README.md`

---
