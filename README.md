# Biblioteca Lusa

Aplicacao web de gestao de biblioteca desenvolvida em Laravel 12 com Jetstream (Livewire), autenticacao com 2FA, interface customizada com DaisyUI e tema visual focado em conforto e legibilidade.

## Funcionalidades principais

- Autenticacao com Laravel Jetstream
- Login com Two-Factor Authentication (2FA)
- Gestao completa (CRUD) de:
  - Livros
  - Autores
  - Editoras
- Relacoes entre entidades:
  - Livro pertence a uma Editora
  - Livro pertence a multiplos Autores
- Pesquisa, filtros e ordenacao nas tabelas
- Exportacao de livros para Excel (`.xlsx`)
- Dados sensiveis cifrados na base de dados (Eloquent casts `encrypted`)
- Dashboard com metricas principais e secoes de destaque

## Stack tecnica

- PHP 8.2+
- Laravel 12
- Laravel Jetstream (Livewire)
- Laravel Fortify (2FA)
- Tailwind CSS + DaisyUI
- Maatwebsite/Laravel-Excel
- SQLite (configuracao base, adaptavel a MySQL/PostgreSQL)

## Requisitos validados

- Jetstream instalado e funcional
- 2FA ativo no perfil do utilizador
- Tema customizado com componentes DaisyUI
- Menus: Livros, Autores, Editoras
- Tabelas com pesquisa, filtros e ordenacao
- Campos principais implementados:
  - Livros: ISBN, Nome, Editora, Autores, Sinopse, Imagem da Capa, Preco
  - Autores: Nome, Foto, Bibliografia
  - Editoras: Nome, Logotipo, Notas
- Dados cifrados em base de dados
- Exportacao Excel de Livros

## Instalacao local

1. Clonar o repositorio
2. Instalar dependencias PHP

```bash
composer install
```

3. Instalar dependencias frontend

```bash
npm install
```

4. Copiar o ficheiro de ambiente

```bash
cp .env.example .env
```

5. Gerar chave da aplicacao

```bash
php artisan key:generate
```

6. Executar migracoes e seeders

```bash
php artisan migrate --seed
```

7. Criar link de storage publico

```bash
php artisan storage:link
```

8. Compilar assets

```bash
npm run build
```

9. Iniciar servidor

```bash
php artisan serve
```

## Credenciais de demonstracao

Criadas pelo seeder:

- `admin@biblioteca.test` / `password`
- `editor@biblioteca.test` / `password`

## Comandos uteis

- Limpar caches

```bash
php artisan optimize:clear
```

- Executar testes

```bash
php artisan test
```

- Build frontend de producao

```bash
npm run build
```

## Estrutura funcional

- `app/Http/Controllers/` - logica de negocio (livros, autores, editoras)
- `app/Models/` - modelos e cifragem de campos
- `app/Exports/` - exportacao Excel
- `resources/views/` - interface (layouts, paginas e componentes)
- `database/migrations/` - estrutura da BD
- `database/seeders/` - dados de demonstracao

## UX e performance

- Interface com identidade visual coerente (tipografia, contraste e estados de interacao)
- Melhorias de performance aplicadas nos controladores para reduzir carga em memoria
- Meta recomendada: tempo de abertura por pagina ate 5 segundos em ambiente de producao

## Publicacao / producao

Checklist recomendado antes de deploy:

- `APP_DEBUG=false`
- `APP_ENV=production`
- Credenciais e chaves seguras no `.env`
- Cache de config/rotas/views gerada
- Storage link ativo
- Build de assets atualizado
