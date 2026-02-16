

Status,Significado
pendente,"Reserva feita, mas o hóspede ainda não chegou."
checkin,Hóspede está fisicamente no local (Ativo).
checkout,Hóspede já saiu e liberou a cama.
cancelado,A reserva foi anulada.

1. O Ciclo de Atualização (Terminal do Cursor)
Dentro da pasta do seu projeto (ex: site-mvc ou api-app), execute:

git add .

O que faz: Prepara todos os arquivos modificados para o envio. Se você deletou arquivos e quer que eles sumam do GitHub também, use git add -A.

git commit -m "Sua mensagem aqui"

O que faz: Grava uma versão oficial das alterações no histórico do seu computador. Importante: Sem o commit, o comando seguinte não enviará nada.

git push origin main

O que faz: "Empurra" as alterações do seu PC para o site do GitHub.

2. Comandos de Segurança
Antes de enviar, é recomendável usar estes comandos para evitar erros como o de enviar para o repositório errado:

git status: Mostra quais arquivos foram alterados e se você esqueceu de dar o add ou commit.

git remote -v: Confirma para qual link o projeto está sendo enviado (garante que a api-app não vá para o site-mvc).

git pull origin main: Antes de começar a trabalhar, use este para baixar o que você fez em outro computador (como o de casa ou o do CTI).