#!/bin/bash
#
#   Script para testar a API Pushstart
#   
#   *Necessário ter instalado o curl*
#   
#
#   Maneira de chamar este script:
#   ./test.sh CAMINHO_NO_SERVIDOR_ONDE_ESTÁ_A_API
#
#


# Verificando se o número de parâmetros passados está correto com o esperado
if [ "$#" -ne 1 ]; then
    echo "ERRO: O script necessita de um parâmetro apenas indicando endereço para o servidor onde a API foi instalada."
    exit 1
else
    server="$1"
fi


# Testando se o servidor existe e está acessível
ping -c 1 $server > /dev/null; access="$?"
if [ "$access" -ne "0" ]; then
    echo "ERRO: O servidor passado como parâmetro não existe ou não está acessível."
    exit 1
fi

#localpath=$(pwd)


#########
echo -e "\n==============================================\n"

echo -en "1) Logando através do método POST do código login.php o seguinte usuário:\n\nemail: daniel@gmail.com\nsenha: 1234\n\nComando: curl -X POST -c cookie.txt -F \"email=daniel@gmail.com\" -F \"passwd=1234\"  $server/login.php\nSaída: "
curl -X POST -c cookie.txt -F "email=daniel@gmail.com" -F "passwd=1234"  $server/login.php
echo -e "\n\n"

echo -e "Um arquivo cookie.txt foi guardado com o seguinte conteúdo:\n"
cat cookie.txt

echo -e "\n\n==============================================\n"


echo -en "2) Consultando os dados deste usuário através do método GET do código cadastro.php.\n\nComando: curl -X GET  -b cookie.txt -c cookie.txt $server/cadastro.php\nSaída: "
curl -X GET  -b cookie.txt -c cookie.txt $server/cadastro.php

echo -e "\n\n==============================================\n"


echo -en "3) Alterando o campo *name* do usuário através do método PUT do código cadastro.php.\n\nComando: curl -X PUT -H \"Content-Type: application/json\" -d '{\"email\":\"\",\"name\":\"Claudio\",\"passwd\":\"\",\"newpasswd1\":\"\",\"newpasswd2\":\"\"}' -b cookie.txt -c cookie.txt $server/cadastro.php\nSaída: "
curl -X PUT -H "Content-Type: application/json" -d '{"email":"","name":"Claudio","passwd":"","newpasswd1":"","newpasswd2":""}' -b cookie.txt -c cookie.txt $server/cadastro.php

echo -e "\n\n==============================================\n"


echo -en "4) Alterando o campo *name* e *email* através do método PUT do código cadastro.php (sem passar as senhas em branco).\n\nComando: curl -X PUT -H \"Content-Type: application/json\" -d '{\"email\":\"paulo@gmail.com\",\"name\":\"Paulo\"}' -b cookie.txt -c cookie.txt $server/cadastro.php\nSaída: "
curl -X PUT -H "Content-Type: application/json" -d '{"email":"paulo@gmail.com","name":"Paulo"}' -b cookie.txt -c cookie.txt $server/cadastro.php
echo -e "\n\n"

echo -e "O arquivo cookie.txt foi atualizado com o novo email do login:\n"
cat cookie.txt

echo -e "\n\n==============================================\n"


echo -en "5) Alterando a senha no banco de dados através do método PUT do código cadastro.php.\n\nSenha antiga: 1234\nSenha nova: 12345\nRedigitando senha nova: 12345\n\nComando: curl -X PUT -H \"Content-Type: application/json\" -d '{\"email\":\"\",\"name\":\"\",\"passwd\":\"1234\",\"newpasswd1\":\"12345\",\"newpasswd2\":\"12345\"}' -b cookie.txt -c cookie.txt $server/cadastro.php\nSaída: "
curl -X PUT -H "Content-Type: application/json" -d '{"email":"","name":"","passwd":"1234","newpasswd1":"12345","newpasswd2":"12345"}' -b cookie.txt -c cookie.txt $server/cadastro.php

echo -e "\n\n==============================================\n"


echo -e "6) Testando erros com a mudança de senha."

echo -en "  6.1) Testando sem colocar senha nova.\n\n  Senha antiga: 12345\n  Senha nova: \n  Redigitando senha nova: \n\n  Comando: curl -X PUT -H \"Content-Type: application/json\" -d '{\"email\":\"\",\"name\":\"\",\"passwd\":\"12345\",\"newpasswd1\":\"\",\"newpasswd2\":\"\"}' -b cookie.txt -c cookie.txt $server/cadastro.php\n  Saída: "
curl -X PUT -H "Content-Type: application/json" -d '{"email":"","name":"","passwd":"12345","newpasswd1":"","newpasswd2":""}' -b cookie.txt -c cookie.txt $server/cadastro.php

echo -e "\n\n  ----------------------------------------------\n"

echo -en "  6.2) Testando com senha nova diferente nos dois campos.\n\n  Senha antiga: 12345\n  Senha nova: 1234\n  Redigitando senha nova: 4321\n\n  Comando: curl -X PUT -H \"Content-Type: application/json\" -d '{\"email\":\"\",\"name\":\"\",\"passwd\":\"12345\",\"newpasswd1\":\"1234\",\"newpasswd2\":\"4321\"}' -b cookie.txt -c cookie.txt $server/cadastro.php\n  Saída: "
curl -X PUT -H "Content-Type: application/json" -d '{"email":"","name":"","passwd":"12345","newpasswd1":"1234","newpasswd2":"4321"}' -b cookie.txt -c cookie.txt $server/cadastro.php

echo -e "\n\n  ----------------------------------------------\n"

echo -en "  6.3) Testando com senha antiga incorreta.\n\n  Senha antiga: 1111\n  Senha nova: 123\n  Redigitando senha nova: 123\n\n  Comando: curl -X PUT -H \"Content-Type: application/json\" -d '{\"email\":\"\",\"name\":\"\",\"passwd\":\"1111\",\"newpasswd1\":\"123\",\"newpasswd2\":\"123\"}' -b cookie.txt -c cookie.txt $server/cadastro.php\n  Saída: "
curl -X PUT -H "Content-Type: application/json" -d '{"email":"","name":"","passwd":"1111","newpasswd1":"123","newpasswd2":"123"}' -b cookie.txt -c cookie.txt $server/cadastro.php

echo -e "\n\n==============================================\n"

echo -en "7) Retornando para as configurações antigas.\n\nComando: curl -X PUT -H \"Content-Type: application/json\" -d '{\"email\":\"daniel@gmail.com\",\"name\":\"Daniel\",\"passwd\":\"12345\",\"newpasswd1\":\"1234\",\"newpasswd2\":\"1234\"}' -b cookie.txt -c cookie.txt $server/cadastro.php\nSaída: "
curl -X PUT -H "Content-Type: application/json" -d '{"email":"daniel@gmail.com","name":"Daniel","passwd":"12345","newpasswd1":"1234","newpasswd2":"1234"}' -b cookie.txt -c cookie.txt $server/cadastro.php

echo -e "\n\n==============================================\n"


echo -en "8) Alterando o campo *photo* através do método POST do código cadastro.php:\n\nComando: curl -X POST -H \"Content-Type: multipart/form-data\" -F \"photo=@tmp.jpeg\" -b cookie.txt -c cookie.txt $server/cadastro.php\nSaída: "
curl -X POST -H "Content-Type: multipart/form-data" -F "photo=@tmp.jpeg" -b cookie.txt -c cookie.txt $server/cadastro.php

echo -e "\n\n==============================================\n"


echo -en "9) Fazendo logout através do método DELETE do código login.php:\n\nComando: curl -X DELETE -b cookie.txt -c cookie.txt $server/login.php\nSaída: "
curl -X DELETE -b cookie.txt -c cookie.txt $server/login.php


echo -e "\n\n==============================================\n"

echo -e "10) Testando acessos estando deslogado"


echo -en "  10.1) Testando método GET no código cadastro.php quando deslogado\n\n  Comando: curl -X GET  -b cookie.txt -c cookie.txt $server/cadastro.php\n  Saída: "
curl -X GET  -b cookie.txt -c cookie.txt $server/cadastro.php

echo -e "\n\n" "----------------------------------------------\n"

echo -en "  10.2) Testando método PUT no código cadastro.php quando deslogado\n\n  Comando: curl -X PUT -H \"Content-Type: application/json\" -d '{\"email\":\"\",\"name\":\"Claudio\",\"passwd\":\"\",\"newpasswd1\":\"\",\"newpasswd2\":\"\"}' -b cookie.txt -c cookie.txt $server/cadastro.php\n  Saída: "
curl -X PUT -H "Content-Type: application/json" -d '{"email":"","name":"Claudio","passwd":"","newpasswd1":"","newpasswd2":""}' -b cookie.txt -c cookie.txt $server/cadastro.php

echo -e "\n\n" "----------------------------------------------\n"

echo -en "  10.3) Testando método POST no código cadastro.php quando deslogado\n\n  Comando: curl -X POST -H \"Content-Type: multipart/form-data\" -F \"photo=@$tmp.jpeg\" -b cookie.txt -c cookie.txt $server/cadastro.php\n  Saída: "
curl -X POST -H "Content-Type: multipart/form-data" -F "photo=@tmp.jpeg" -b cookie.txt -c cookie.txt $server/cadastro.php

echo -e "\n\n" "----------------------------------------------\n"

echo -en "  10.4) Testando fazer logout através do método DELETE no código login.php quando deslogado\n\n  Comando: curl -X DELETE -b cookie.txt -c cookie.txt $server/login.php\n  Saída: "
curl -X DELETE -b cookie.txt -c cookie.txt $server/login.php

echo -e "\n\n==============================================\n"

#########

exit 0
