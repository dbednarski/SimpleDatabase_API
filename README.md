# DESAFIO PUSHSTART

Essa API foi desenvolvida em PHP para o gerenciamento de cadastros de usuários em um banco de dados MySQL.

## Estrutura

### Banco de dados

O banco de dados *usuarios.sql* possui cinco colunas, ou **campos**:

* id : o ID único do usuário
* email : o e-mail do usuário
* name : o nome do usuário
* photo : a URL para a imagem do usuário
* passwd : a senha do usuário criptografada (neste arquivo-exemplo, a senha descriptografada de todos é **1234**)

A senha do usuário root do banco de dados é uma string nula.


### login.php

Este código permite tanto o login, quanto o logout de um usuário adequadamente registrado no banco de dados.

* login : feito através do método *POST*, passando as variáveis **email** e **passwd**. Um cookie será criado para manter a sessão por uma hora.
* logout : feito através do método *DELETE*.

A saída é em formato JSON que indica o estado do processamento e eventuais erros.
Saída: {"status":"status","message":"message","user_message":"user_message"}, onde:

* status é o código de status HTTP;
* message é a mensagem associada a esse status;
* user_message é uma mensagem em português que pode ser usada para o usuário. No caso de sucesso (status 200 e 201), user_message assume uma string vazia.


### cadastro.php

Este código é somente acessível para usuários logados e os permite visualizar seu cadastro e fazer modificações no banco de dados. Cada vez que for chamado, o cookie será atualizado.

* consulta ao cadastro : feito através do método *GET* sem parâmetros.
* modificação do campo **photo** : feito através do método *POST* para que o usuário faça upload de uma imagem. O parâmetro passado deve ser o caminho para a imagem no computador cliente.
* modificação dos demais campos : feito através do método *PUT*, passando os parâmetros (campos e valores a serem atualizados) em formato JSON. No caso da mudança da senha, é necessário passar três parâmetros: **passwd** = senha atual; **newpasswd1** = senha nova; **newpasswd2** = senha nova redigitada.

No método *PUT* não necessariamente todos parâmetros devem ser passados na entrada, mas apenas aqueles que se quiser. Um campo passado com valor de string vazia não será alterado.

A saída é em formato JSON e indica todos os campos do usuário (exceto senha), e estado do processamento e eventuais erros, com os mesmos três campos de saída do *login.php*.
Saída: {"id":"id","email":"email","name":"name","photo":"photo","status":"status","message":"message","user_message":"user_message"}



## Requisitos para a instalação e testes

1. Para servidor web: MySQL, PHP (>4.1.3), curl
2. Para servidor local: XAMPP, curl



## Instalando

### Em um servidor web

1. Para instalar, primeiramente é necessário importar o banco de dados *usuarios.sql* para o servidor. Considerando o usuário dentro do diretório raiz deste pacote, deve dar os seguintes comandos através do terminal:

```
mysql -u root -e "create database usuarios;"
sudo cat db/usuarios.sql | mysql -u root -p usuarios
```

2. Em seguida, deve copiar o conteúdo dentro de *src/* no local adequado dentro do diretório **www**. 


### Em um servidor local

1. Para instalar, primeiramente é necessário importar o banco de dados *usuarios.sql* para o servidor. Considerando o usuário dentro do diretório raiz deste pacote, deve dar os seguintes comandos através do terminal:

```
/opt/lampp/mysql -u root -e "create database usuarios;"
sudo cat db/usuarios.sql | /opt/lampp/mysql -u root -p usuarios
```

2. Em seguida, deve copiar o conteúdo dentro de *src/* para o diretório do localhost:

```
sudo cp src/* -fr /opt/lampp/htdocs
sudo chmod -R a+rx /opt/lampp/htdocs
``` 


## Testando

Para testar a instalação foi desenvolvido um script .sh no subdiretório *teste/* usando o comando *curl*. Para executá-lo é necessário passar como parâmetro o caminho no servidor para os códigos php.

```
cd testes
./teste.sh CAMINHO_NO_SERVIDOR
``` 

No caso de uma instalação feita no servidor local usando o XAMPP:

```
cd testes
./teste.sh "http://localhost"
``` 

As impressões devem ser idênticas às registradas no arquivo teste/teste.txt



## Autor

    Daniel Bednarski Ramos - [Página](https://www.astro.iag.usp.br/~bednarski)


