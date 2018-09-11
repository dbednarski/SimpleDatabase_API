# PUSHSTART API

Essa API foi desenvolvida em PHP para o gerenciamento de cadastros de usuários em um banco de dados MySQL.

## Estrutura

### Banco de dados

O banco de dados *db/usuarios.sql* possui cinco colunas, ou **campos**:

* id : o ID único do usuário
* email : o e-mail do usuário
* name : o nome do usuário
* photo : a URL para a imagem do usuário
* passwd : a senha do usuário criptografada (neste arquivo-exemplo, a senha descriptografada de todos é **1234**)

A senha do usuário root do banco de dados é uma string nula.


### login.php

O código *src/login.php* permite tanto o login, quanto o logout de um usuário adequadamente registrado no banco de dados.

* login : executado através do método *POST*, passando as variáveis **email** e **passwd**. Um cookie será criado para manter a sessão por uma hora.
* logout : executado usando o método *DELETE*.

A saída é em formato JSON que indica o estado do processamento e eventuais erros.
Saída: {"status":"status","message":"message","user_message":"user_message"}, onde:

* *status* é o código de status HTTP;
* *message* é a mensagem associada a esse status;
* *user_message* é uma mensagem em português que pode ser retornada ao usuário relatando algum problema. No caso de sucesso (status 200 e 201), *user_message* assume uma string vazia.


### cadastro.php

O código *src/cadastro.php* é somente acessível para usuários logados e os permite visualizar seu cadastro e fazer modificações no banco de dados. O cookie é atualizado cada vez que chamado.

* consulta ao cadastro : executado através do método *GET* (sem parâmetros).
* modificação do campo **photo** : executado através do método *POST* para que o usuário faça upload de uma imagem. O parâmetro passado deve ser o caminho para a imagem no computador cliente.
* modificação dos demais campos : executado através do método *PUT*, passando os parâmetros (campos e valores a serem atualizados) em formato JSON. No caso da mudança da senha, é necessário passar três parâmetros: **passwd** = senha atual; **newpasswd1** = senha nova; **newpasswd2** = senha nova redigitada.

No método *PUT* não necessariamente todos parâmetros devem ser passados na entrada, mas apenas aqueles que for conveniente ao desenvolvedor. Um campo passado com valor de string vazia não será alterado.

A saída é em formato JSON e retorna todos os valores do cadastro do usuário (exceto senha), além do estado do processamento e eventuais erros com os mesmos três campos de saída do *login.php*.

Saída: {"id":"id","email":"email","name":"name","photo":"photo","status":"status","message":"message","user_message":"user_message"}



## Requisitos para a instalação e testes

1. Para servidor web: MySQL, PHP (>4.1.3), curl
2. Para servidor local: XAMPP, curl



## Instalando

### Em um servidor web

1. Para instalar, primeiramente é necessário importar o banco de dados *usuarios.sql* para o servidor. Considerando que o usuário esteja dentro do diretório raiz deste pacote, deve seguir com os seguintes comandos no terminal:

```
mysql -u root -e "create database usuarios;"
sudo cat db/usuarios.sql | mysql -u root -p usuarios
```

2. Em seguida, deve copiar **TODO** o conteúdo dentro de *src/* para o local adequado no interior do diretório **www**.


### Em um servidor local

1. Para instalar, primeiramente é necessário importar o banco de dados *usuarios.sql* para o servidor. Considerando que o usuário esteja dentro do diretório raiz deste pacote, deve seguir com os seguintes comandos no terminal:

```
/opt/lampp/mysql -u root -e "create database usuarios;"
sudo cat db/usuarios.sql | /opt/lampp/mysql -u root -p usuarios
```

2. Em seguida, deve copiar **TODO** o conteúdo dentro de *src/* para o diretório do localhost:

```
sudo cp src/* -fr /opt/lampp/htdocs
sudo chmod -R a+rx /opt/lampp/htdocs
``` 


## Testando

Um script .sh foi desenvolvido para testar a instalação, como também para mostrar alguns exemplos de chamadas dos arquivos .php. O script faz a conexão com o servidor através do comando *curl*. Para executá-lo é necessário passar como parâmetro o caminho no servidor onde foram copiados os códigos .php. De maneira geral:

```
cd testes
./teste.sh CAMINHO_NO_SERVIDOR
``` 

No caso de uma instalação da API feita no servidor local usando o XAMPP:

```
cd testes
./teste.sh "http://localhost"
``` 

Se as impressões após o teste forem idênticas às registradas no arquivo testes/teste.txt, a instalação foi um sucesso



## Autor

Daniel Bednarski Ramos - [Página](https://www.astro.iag.usp.br/~bednarski)

daniel.bednarski.ramos@gmail.com




