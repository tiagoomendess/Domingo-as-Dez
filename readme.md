# Domingo às Dez

Este repositório contém todo o código que corre no website do Domingo às Dez. É agora OpenSource para permitir uma manutenção do código por todos os que queiram ajudar. O código é bastante antigo e vai contra algumas boas práticas e padrões de desenvolvimento, mas funciona e é preciso mantê-lo. Porquê OpenSource?
- O site precisa de ser mantido
- Quem o manter fica com contribuições públicas num projeto OpenSource, o que é sempre bom para o currículo.
- Win win situation

## Em primeiro lugar
- Não há docker, instalar XAMPP, LAMP ou whatever dependendo do OS
- Não há testes automáticos, nem unitários nem de integração
- Produção corre com o código de master
- Só há atualmente 1 instância a correr

## Como Contribuir
- Faz pull do código mais recente de master
- Cria uma branch local a partir de master
- Faz lá o código
- Commit
- Abre pull request para master (Título pequeno e descrição com tudo o que foi alterado/adicionado)
- Alguém vai dar review e merge do código com master

## O que contribuir
- 1º verifica que existem issues, dar prioridade a alterações que foram pedidas
- Adiciona, corrige, melhora o que bem entenderes

## Como correr localmente
- Clone do repositório
- criar ficheiro .env e copiar o conteudo de .env.example la para dentro
- Editar .env com as credenciais para a DB local
- Correr migrações: php artisan migrate
- Escrever php artisan serve no terminal dentro da pasta do projeto
- Website is now up, deve dizer o link no terminal
- Registar uma conta para ter acesso. Não deve estar nenhum serviço de email configurado por isso não vais receber código. Ver base de dados
- Dar permissões, again, base de dados

## Documentação
Não há, quer dizer, há este [relatório](https://drive.google.com/file/d/1P0AJalnBdpLy_eTr7AdMmCxPQnOaa3ex/view?usp=sharing) final que pode ajudar. O capítulo 6 é capaz de ser o mais relevante, principalmente para perceber o modelo de dados.

## Dummy Data
Não há. Questões GDPR e não só. Deves conseguir inserir manualmente no backend a partir do momento que tenhas uma conta admin registada, até lá, edita manualmente a base de dados.

