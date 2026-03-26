# CRUD de Alunos – Fork com Melhorias

> **Fork desenvolvido como atividade prática da disciplina de Desenvolvimento Web.**  
> Repositório original: [UNIAENE-GTI/CRUD](https://github.com/UNIAENE-GTI/CRUD)

---

## 📋 Sobre o projeto

Sistema CRUD completo em PHP para gerenciamento de alunos, com operações de **Cadastrar, Listar, Editar e Excluir** (Create, Read, Update, Delete), utilizando PDO para conexão com MySQL.

---

## 🔍 Análise do projeto original

Após estudar o código original, foram identificados os seguintes problemas:

### Bugs e inconsistências

| Arquivo | Problema identificado |
|---|---|
| `index.php` | Label "Curso" não correspondia ao campo `document` no banco |
| `store.php` | Duplicava SQL em vez de usar a classe `User` já existente |
| `update.php` | Idem – SQL duplicado, sem usar `User::update()` |
| `delete.php` | Idem – SQL duplicado, sem usar `User::delete()` |
| Todos os `.php` de view | Sem estrutura HTML5 (`DOCTYPE`, `<head>`, `charset`) |
| Todas as views | Nenhum CSS ou layout visual |
| `store.php` / `update.php` | Sem validação de formato de e-mail no servidor |
| `store.php` / `update.php` | Sem tratamento de exceções de banco de dados |
| `edit.php` / `index.php` | Não usavam a classe `User` para buscar os dados |

---

## ✅ Melhorias implementadas

### PHP – Backend

- **`store.php`**: refatorado para usar `User::create()`, adicionada validação de e-mail com `filter_var()`, tratamento de exceções com `try/catch` e flash messages via `session`
- **`update.php`**: refatorado para usar `User::update()`, mesmas melhorias de validação e feedback
- **`delete.php`**: refatorado para usar `User::delete()`, verifica se o aluno existe antes de excluir, feedback via flash message
- **`connect.php`**: adicionado `PDO::ATTR_EMULATE_PREPARES => false` para usar prepared statements reais
- **`User.php`**: documentação aprimorada, sem alterações funcionais (já estava bem estruturado)

### HTML/CSS – Frontend

- **Estrutura HTML5 completa** em todas as views (DOCTYPE, meta charset, viewport, title)
- **`style.css`** criado do zero com:
  - Design system com variáveis CSS (cores, sombras, raios, transições)
  - Paleta dark navy `#0f172a` com accent azul `#3b82f6`
  - Tipografia **Inter** (Google Fonts)
  - Cards com glassmorphism sutil e sombras
  - Campo de formulário com animação de foco e validação visual
  - Tabela responsiva com hover e striping
  - Botões com micro-animações (lift no hover)
  - Flash messages com animação de slide-down
  - Layout responsivo (mobile-first)

### Segurança

- `htmlspecialchars()` em todos os outputs para prevenção de XSS
- Validação server-side de e-mail com `filter_var()`
- Proteção contra ID inválido em `delete.php` e `edit.php`
- Flash messages via `$_SESSION` para feedback sem expor dados na URL

---

## 🗂️ Estrutura de arquivos

```
├── connect.php   # Conexão PDO (Singleton)
├── User.php      # Classe com todas as operações de banco (CRUD)
├── index.php     # Listagem + formulário de cadastro
├── edit.php      # Formulário de edição
├── store.php     # Processar cadastro (POST)
├── update.php    # Processar atualização (POST)
├── delete.php    # Processar exclusão (GET)
└── style.css     # Design system completo
```

---

## 🗃️ Banco de dados

Crie o banco e a tabela no MySQL:

```sql
CREATE DATABASE aula01 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE aula01;

CREATE TABLE users (
    id       INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name     VARCHAR(120) NOT NULL,
    email    VARCHAR(160) NOT NULL UNIQUE,
    document VARCHAR(20)  NOT NULL
);
```

---

## ▶️ Como executar

1. Instale o [XAMPP](https://www.apachefriends.org/) ou equivalente
2. Clone este repositório na pasta `htdocs/`
3. Ajuste as credenciais em `connect.php` se necessário
4. Crie o banco de dados conforme o SQL acima
5. Acesse `http://localhost/fork/` no navegador

---

## 🛠️ Tecnologias

- **PHP 8.1+** com PDO e Prepared Statements
- **MySQL 8+**
- **HTML5** semântico
- **CSS3** puro (sem frameworks)
- **Google Fonts** – Inter
