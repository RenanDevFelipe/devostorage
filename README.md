# DevoStorage — Monorepo

> Visão unificada dos subprojetos **devostorage_api** (API) e **devostorage_web** (front-end).

![Monorepo](https://img.shields.io/badge/Monorepo-DevOS_Orange-blue?style=flat-square)

Este README principal reúne a visão geral, diagramas de caso de uso, classes e sequencia, instruções rápidas de execução e links para os READMEs específicos de cada subprojeto.

## Índice
- Visão Geral
- Como rodar rapidamente
- Diagramas
  - Diagrama de Casos de Uso
  - Diagrama de Classes (API)
  - Diagrama de Sequencia (Movimentacação)
- Estrutura dos Subprojetos
- Links úteis

---

## Visão Geral

O repositorio contém dois subprojetos principais:

- `devostorage_api/` — API RESTful em PHP (CodeIgniter 4) responsável por autenticação, gerenciamento de produtos, movimentações e geração de relatórios (PDF / Excel).
- `devostorage_web/` — SPA em React + TypeScript que consome a API e fornece UI para gerenciamento, dashboard e downloads.

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

# 🛠️ Tecnologias Utilizadas

O projeto **DevoStorage** foi desenvolvido utilizando uma arquitetura moderna, separando o Backend (API) do Frontend (SPA). Abaixo estão listadas as principais linguagens, frameworks e bibliotecas empregadas.

## 🔙 Backend (API)

A API reside no diretório `devostorage_api/` e é responsável por toda a regra de negócio, autenticação e acesso a dados.

* **Linguagem**: [PHP 8.1+](https://www.php.net/)
* **Framework**: [CodeIgniter 4](https://codeigniter.com/) (v4.6.3)
* **Gerenciador de Dependências**: [Composer](https://getcomposer.org/)

## 🖥️ Frontend (Web)

A interface web reside no diretório `devostorage_web/` e consome a API para fornecer a experiência do usuário.

* **Framework**: [React](https://react.dev/)
* **Linguagem**: [TypeScript](https://www.typescriptlang.org/)
* **Build Tool**: [Vite](https://vitejs.dev/)
* **Tipo de Aplicação**: Single Page Application (SPA)

## 🗄️ Banco de Dados

* **SGBD**: [MySQL](https://www.mysql.com/) (ou MariaDB compatível)
* **Driver**: MySQLi (Padrão do CodeIgniter)

## 📚 Bibliotecas e Recursos Adicionais

As seguintes bibliotecas foram integradas ao backend para fornecer funcionalidades específicas:

| Biblioteca | Versão | Propósito |
| :--- | :---: | :--- |
| **[firebase/php-jwt](https://github.com/firebase/php-jwt)** | `^6.11` | Implementação de autenticação via JSON Web Tokens (JWT) para segurança da API. |
| **[mpdf/mpdf](https://github.com/mpdf/mpdf)** | `^8.2` | Geração de relatórios de estoque e movimentações em formato **PDF**. |
| **[phpoffice/phpspreadsheet](https://github.com/PHPOffice/PhpSpreadsheet)** | `^5.3` | Geração e manipulação de planilhas **Excel** (`.xlsx`) para exportação de dados. |

---

> **Nota:** Para instalar as dependências do backend, execute `composer install` dentro da pasta `devostorage_api/`. Para o frontend, utilize `npm install` na pasta `devostorage_web/`.

---

## Diagramas

As seções abaixo mostram os diagramas.

### Caso de Uso

Descreve as funcionalidades acessíveis por Funcionários e Administradores.

![Diagrama de Casos de Uso](/documents/diagrama_casos_uso.png)

### Diagrama de Classes (API)

Mostra a estrutura do backend, destacando a separação entre Controllers, Services e Models, e como o ReportGenerator orquestra os dados.

![Diagrama de Classes](/documents/diagrama_classes.png)

### Diagrama de Sequencia (Movimentação)

Detalha o processo técnico de uma movimentação de entrada, garantindo a integridade do estoque via transação.

![Diagrama de Sequencia](/documents/diagrama_sequencia_movimentacao.png)

---

## Estrutura dos Subprojetos

- `devostorage_api/` — veja `devostorage_api/README.md` para documentação detalhada da API: endpoints, configuração, migrações e exemplos.
- `devostorage_web/` — veja `devostorage_web/README.md` para instruções do front-end, arquitetura de componentes e setup de desenvolvimento.

---

## Links úteis

- README da API: `devostorage_api/README.md`
- README do Front-end: `devostorage_web/README.md`

---
