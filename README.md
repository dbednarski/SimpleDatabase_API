# PUSHSTART API

Essa API foi desenvolvida em PHP para o gerenciamento de cadastros de usuários em um banco de dados MySQL.




## Estrutura

* `src/`: diretório com os códigos fonte .php.
    * |----`scr/login.php`
    * |----`src/cadastro.php`
    * |----`src/.htaccess`
* `sql/`: diretório com o backup do banco de dados.
    * |----`sql/usuarios.sql`
* `testes/`: diretório com os arquivos para o teste de instalação.
    * |----`testes/teste.sh`
    * |----`testes/teste.txt`
    * |----`testes/tmp.jpeg`


### Banco de dados

O banco de dados `db/usuarios.sql` possui cinco colunas, ou **campos**:

* **id** : o ID único do usuário
* **email** : o e-mail do usuário
* **name** : o nome do usuário
* **photo** : a URL para a imagem do usuário
* **passwd** : a senha do usuário criptografada (neste arquivo-exemplo, a senha descriptografada de todos é **1234**)

**A senha do usuário root do banco de dados é uma string nula.**


### login.php

O código `src/login.php` permite tanto o login, quanto o logout de um usuário adequadamente registrado no banco de dados.

* login : executado através do método *POST*, passando as variáveis **email** e **passwd**. Um cookie será criado para manter a sessão por uma hora.
* logout : executado usando o método *DELETE*.

A saída é em formato JSON que indica o estado do processamento e eventuais erros.

Saída:
```
{
  "status":"status",
  "message":"message",
  "user_message":"user_message"
}
```

* **status** é o código de status HTTP;
* **message** é a mensagem associada a esse status;
* **user_message** é uma mensagem em português que pode ser retornada ao usuário relatando algum problema. No caso de sucesso (status 200 e 201), **user_message** assume uma string vazia.


### cadastro.php

O código `src/cadastro.php` é somente acessível para um usuário logado e o permite visualizar seu cadastro e fazer modificações no banco de dados. O cookie é atualizado cada vez que chamado.

* Consulta ao cadastro : executado através do método *GET* (sem parâmetros).
* Modificação do campo **photo** : executado através do método *POST* para que o usuário faça upload de uma imagem. O parâmetro passado deve ser o caminho para a imagem no computador cliente.
* Modificação dos demais campos : executado através do método *PUT*, passando os parâmetros em formato JSON (campos e valores a serem atualizados).
    * No caso do usuário requerer mudança da senha, são necessários três parâmetros de entrada: **passwd** = senha atual; **newpasswd1** = senha nova; **newpasswd2** = senha nova redigitada.
    * Não necessariamente todos parâmetros devem ser passados na entrada, mas apenas aqueles que forem convenientes ao desenvolvedor.
    * Qualquer campo passado cujo valor seja uma string vazia não será alterado no banco de dados.

A saída é em formato JSON e retorna todos os valores do cadastro do usuário (exceto senha), além do estado do processamento e eventuais erros com os mesmos três campos de saída do `src/login.php`.

Saída:
```
{
  "id":"id",
  "email":"email",
  "name":"name",
  "photo":"photo",
  "status":"status",
  "message":"message",
  "user_message":"user_message"
}
```

No caso de erro de acesso ao banco de dados ou usuário não logado, a saída será:

```
{
  "status":"status",
  "message":"message",
  "user_message":"user_message"
}
```



## Requisitos para a instalação e testes

1. Para servidor web: MySQL, PHP (>4.1.3), curl
2. Para servidor local: [XAMPP](https://www.apachefriends.org/pt_br/index.html), curl

Na sequência as instruções de instalação serão dadas para distribuições Linux. No entanto, é possível instalar a API em Mac e no Windows sem muitos problemas.

Para realizar o teste da instalação, é necessário que o terminal Bash esteja instalado no sistema. O Windows, a partir da versão 10, está apto nativamente a rodar shell scripts, apenas sendo necessário alterar suas configurações ([leia aqui](https://www.howtogeek.com/249966/how-to-install-and-use-the-linux-bash-shell-on-windows-10/) para mais informações). Usuários de versões mais antigas do Windows podem instalar programas como [Cygwin](http://www.cygwin.com/) para rodá-lo.




## Instalando

### Em um servidor web

1. Primeiramente é necessário importar o banco de dados `db/usuarios.sql` para o servidor. Considerando que o usuário esteja dentro do diretório raiz deste pacote, deve seguir com os seguintes comandos no terminal:

```
$ mysql -u root -e "create database usuarios;"
$ sudo cat db/usuarios.sql | mysql -u root -p usuarios
```

Quando a senha for pedida, apenas aperte a tecla ENTER.


2. Em seguida, **TODO** o conteúdo dentro de `src/` deve ser copiado para o local adequado no interior do diretório **`www/`**. Nesse mesmo local, criar um diretório `img/`. Garantir que haja acesso geral para a leitura desse conteúdo, e permissão para gravação dentro do diretório `img/`.


### Em um servidor local

1. Primeiramente é necessário importar o banco de dados `db/usuarios.sql` para o servidor local criado através do XAMMP. Considerando que o usuário esteja dentro do diretório raiz deste pacote, deve seguir com os seguintes comandos no terminal:

```
$ /opt/lampp/bin/mysql -u root -e "create database usuarios;"
$ cat db/usuarios.sql | /opt/lampp/bin/mysql -u root -p usuarios
```

Quando a senha for pedida, apenas aperte a tecla ENTER.


2. Em seguida, o conteúdo dentro de `src/` deve ser copiado para o diretório do localhost:

```
$ sudo cp src/* -fr /opt/lampp/htdocs
$ sudo mkdir /opt/lampp/htdocs/img
$ sudo chmod -R a+rx /opt/lampp/htdocs
$ sudo chmod a+w /opt/lampp/htdocs/img
``` 



## Testando

Um script .sh foi desenvolvido para testar a instalação, como também para mostrar alguns exemplos de chamadas dos arquivos .php. O script faz a conexão com o servidor através do comando *curl*. Para executá-lo é necessário passar como parâmetro o caminho no servidor onde foram copiados os códigos .php. De maneira geral:

```
$ cd testes
$ ./teste.sh CAMINHO_NO_SERVIDOR
``` 

No caso da API ter sido instalada no servidor local, fazendo-se uso do XAMPP:

```
$ cd testes
$ ./teste.sh "http://localhost"
``` 

A instalação pode ser considerada de sucesso se as impressões durante a execução do script `teste.sh` forem idênticas às registradas no arquivo `testes/teste.txt`. Excetua-se os caminhos para o servidor.




## Autor

Daniel Bednarski Ramos - [Página](https://www.astro.iag.usp.br/~bednarski)

daniel.bednarski.ramos@gmail.com




