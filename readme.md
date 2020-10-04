![enter image description here](https://scontent.fopo2-2.fna.fbcdn.net/v/t1.0-9/71145732_2263110943795074_5523891523969613824_o.png?_nc_cat=110&_nc_sid=e3f864&_nc_ohc=MvSXRRmt03wAX-RzpDy&_nc_ht=scontent.fopo2-2.fna&oh=7cc6d38e083b4b82b57e366f5b3938d8&oe=5F91C456)
Este repositório contém todo o código que corre no website do Domingo às Dez. É agora OpenSource para permitir uma manutenção do código por todos os que queiram ajudar. O código é bastante antigo e vai contra algumas boas práticas e padrões de desenvolvimento, mas funciona e é preciso mantê-lo. Porquê OpenSource?
- O site precisa de ser mantido
- Quem o manter fica com contribuições públicas num projeto OpenSource, o que é sempre bom para o currículo
- Win win situation

## Em primeiro lugar
- Não há docker, instalar XAMPP, LAMP ou whatever dependendo do OS
- Não há testes automáticos, nem unitários nem de integração (YOLO)
- Produção corre com o código de master
- Só há atualmente 1 instância a correr

## Como Contribuir
- Faz fork do repositório
- Desenvolve o código nesse fork
- Commit com mensagens sugestivas
- Faz push
- Abre pull request para este repositório a partir do teu fork com título pequeno e descrição com tudo o que foi alterado
- Alguém vai dar review e merge do código com master, ou pedir alterações

## O que contribuir
- 1º verifica que existem issues, dar prioridade a alterações que foram pedidas
- Adiciona, corrige, melhora o que bem entenderes

## Como correr localmente
- Clone do repositório
- Criar ficheiro .env e copiar o conteudo de .env.example la para dentro
- Editar .env com as credenciais para a DB local
- Correr as migrações correndo o comando `php artisan migrate`
- Escrever `php artisan serve` no terminal dentro da pasta do projeto
- Website is now up, deve dizer o link no terminal
- Registar uma conta para ter acesso. Não deve estar nenhum serviço de email configurado por isso não vais receber código. Ver base de dados
- Dar permissões, again, base de dados

## Documentação
Não há, quer dizer, há este [relatório](https://drive.google.com/file/d/1P0AJalnBdpLy_eTr7AdMmCxPQnOaa3ex/view?usp=sharing) final que pode ajudar. O capítulo 6 é capaz de ser o mais relevante, principalmente para perceber o modelo de dados.

## Dummy Data
Não há. Questões GDPR e não só. Deves conseguir inserir manualmente no backend a partir do momento que tenhas uma conta admin registada, até lá, edita manualmente a base de dados.

