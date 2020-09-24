
# Domingo às Dez

Este repositório contém todo o código que corre no website do Domingo às Dez. É agora OpenSource para permitir uma manutenção do código por todos os que queiram ajudar.

## Em primeiro lugar
- Não há docker, instalar XAMPP ou LAMP dependendo do OS (Deve trazer ja todas as extenções)
- Não ha testes, nem unitarios nem de integração
- Produção corre com o código de master
- Só ha atualmente 1 instancia a correr

## Como Contribuir
- Faz pull do codigo mais recente de master
- Cria uma branch local a partir de master
- Faz lá o código
- Commit
- Abre pull request para master (Título pequeno e descrição com tudo o que foi alterado/adicionado)
- Alguém vai dar review e merge do código com master

## Como correr localmente
- Clone do repositório
- criar ficheiro .env e copiar o conteudo de .env.example la para dentro
- Editar .env com as credenciais para a DB local
- Correr migrações: php artisan migrate
- Escrever php artisan serve no terminal dentro da pasta do projeto
- Website is now up
- Registar uma conta para ter acesso. Nao deve estar nenhum serviço de email configurado por isso nao vais receber codigo. Ver base de dados
- Dar permissoes, again, base de dados

