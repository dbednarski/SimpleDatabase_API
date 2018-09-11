<?php 

// Se o usuário não estiver logado, retorna erro
if(!isset($_COOKIE['email'])) {
    $saida = array("status" => 401, "message" => "Unauthorized", "user_message" => "Faça o login antes.");
    header("Content-type: application/json");
    echo json_encode($saida, JSON_UNESCAPED_UNICODE);
    exit;
}

// Conecta com o banco de dados
$conexao = mysqli_connect("localhost","root","");
$db = mysqli_select_db($conexao, "usuarios") or die(mysqli_error($conexao));
if(mysqli_errno($conexao) != 0){
    $saida = array("status" => 500, "message" => "Internal Server Error", "user_message" => "Problemas em acessar o banco de dados. Tente de novo mais tarde. Se o problema persistir, contate o administrador.");
    header("Content-type: application/json");
    echo json_encode($saida, JSON_UNESCAPED_UNICODE);
    exit;
}

$email = $_COOKIE['email'];
setcookie("email", $email, time()+3600, "/");
$metodo = $_SERVER["REQUEST_METHOD"];


switch($metodo)
{
    case 'GET':
        // Retorna todos os dados do banco de dados em formato JSON
        retorna_dados();
        break;
    case 'POST':
        // Altera endereço da imagem do cadastro
        altera_foto();
        break;
    case 'PUT':
        // Atualiza campos a partir da entrada em formato JSON
        $entrada_json = file_get_contents("php://input", FILE_TEXT);
        $entrada = json_decode($entrada_json, true);

        if(array_key_exists("photo", $entrada) && $entrada['photo'] != ""){
            retorna_dados(array(405, "Method Not Allowed", "Método não permitido. Contate o desenvolvedor."));
        }
        altera_dados($entrada);
        break;
    default:
        // Outros métodos de entrada são inválidos
        retorna_dados(array(405, "Method Not Allowed", "Método não permitido. Contate o desenvolvedor."));
        break;
}



function retorna_dados($erros = array(200, "OK", "")) {
    
    global $conexao;
    global $email;

    $query = sprintf("SELECT id, email, name, photo FROM users WHERE email = \"%s\";", mysqli_real_escape_string($conexao, $email));
    $aux = mysqli_query($conexao, $query) or die(mysqli_error($conexao));
    $dados = mysqli_fetch_assoc($aux);
    mysqli_close($conexao);    
    
    $status = array("status" => $erros[0], "message" => $erros[1], "user_message" => $erros[2]);
    header("Content-type: application/json");
    echo json_encode(array_merge($dados,$status), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}



function altera_dados($dados){

    global $conexao;
    global $email;

    $email_es = mysqli_real_escape_string($conexao, $email);

    // Altera nome
    if(array_key_exists("name", $dados) && $dados['name'] != ""){
        $name = $dados['name'];
        $name_es = mysqli_real_escape_string($conexao, $name);
        $query = sprintf("UPDATE users SET name=\"%s\" WHERE email = \"%s\";", $name_es, $email_es);
        $aux = mysqli_query($conexao, $query) or die(mysqli_error($conexao));
    }

    // Altera email
    if(array_key_exists("email", $dados) && $dados['email'] != ""){
        $newemail = $dados['email'];
        $newemail_es = mysqli_real_escape_string($conexao, $newemail);
        $query = sprintf("UPDATE users SET email=\"%s\" WHERE email = \"%s\";", $newemail_es, $email_es);
        $aux = mysqli_query($conexao, $query) or die(mysqli_error($conexao));

        setcookie("email", $email, time()-3600, "/");        
        setcookie("email", $newemail, time()+3600, "/");
        $email = $newemail;
        $email_es = $newemail_es;
    }

    // Altera senha
    if(array_key_exists("passwd", $dados) && array_key_exists("newpasswd1", $dados) && array_key_exists("newpasswd2", $dados)) {
        $passwd = md5($dados['passwd']);
        $newpasswd1 = md5($dados['newpasswd1']);
        $newpasswd2 = md5($dados['newpasswd2']);
        $blank = md5("");

        if ($passwd != $blank && ($newpasswd1 === $blank || $newpasswd2 === $blank)){
            retorna_dados(array(400, "Bad Request", "Digite uma nova senha válida."));
            return;
        }
        elseif ($passwd === $blank && ($newpasswd1 != $blank || $newpasswd2 != $blank)) {
            retorna_dados(array(400, "Bad Request", "Digite a senha atual."));
            return;
        }
        elseif ($passwd != $blank && $newpasswd1 != $blank && $newpasswd2 != $blank) {
            $query = sprintf("SELECT passwd FROM users WHERE email = \"%s\";", $email_es);
            $aux = mysqli_query($conexao, $query) or die(mysqli_error($conexao));
            $passwd_db = mysqli_fetch_assoc($aux)['passwd'];
            
            if ($passwd != $passwd_db) {
                retorna_dados(array(401, "Unauthorized", "Senha incorreta."));
                return;
            }
            elseif ($newpasswd1 != $newpasswd2) {
                retorna_dados(array(400, "Bad Request", "As duas senhas novas digitadas são diferentes."));
                return;
            }
            else{
                $query = sprintf("UPDATE users SET passwd=\"%s\" WHERE email = \"%s\";", $newpasswd1, $email_es);
                $aux = mysqli_query($conexao, $query) or die(mysqli_error($conexao));
            }
        }
    }

    retorna_dados();
}



function altera_foto() {
    
    global $conexao;
    global $email;

    $email_es = mysqli_real_escape_string($conexao, $email);
    
    // Verifica formato
    if ($_FILES["photo"]["type"] != "image/gif"
        && $_FILES["photo"]["type"] != "image/jpeg"
        && $_FILES["photo"]["type"] != "image/JPG"
        && $_FILES["photo"]["type"] != "image/png"
        && $_FILES["photo"]["type"] != "image/pjpeg"){
            
        retorna_dados(array(415, "Unsupported Media Type", "Formato de arquivo não suportado."));
        return;
    }

    // Verifica tamanho    
    if ($_FILES["photo"]["size"] > 2000000) {
        retorna_dados(array(413, "Payload Too Large", "O arquivo deve ter menos de 2MB."));
        return;
    }

    // Verifica processamento
    if ($_FILES["photo"]["error"] != 0) {
        retorna_dados(array(400, "Bad Request", "Erro ao processar imagem."));
        return;
    }


    $query = sprintf("SELECT id FROM users WHERE email = \"%s\";", $email_es);
    $aux = mysqli_query($conexao, $query) or die(mysqli_error($conexao));
    $id = mysqli_fetch_assoc($aux)['id'];
    
 	$pic = $_FILES["photo"]["name"];
    $aux = explode(".",$pic);
	$extens = $aux['1'];
    $url = "img/". $id.".".$extens;
    $url_es = mysqli_real_escape_string($conexao, $url);
    
    // Remove a imagem do servidor, caso já exista.
    if (file_exists($url)) {
		unlink($url);
    }
	move_uploaded_file($_FILES["photo"]["tmp_name"], $url);

    $query = sprintf("UPDATE users SET photo=\"%s\" WHERE email = \"%s\";", $url_es, $email_es);
    $aux = mysqli_query($conexao, $query) or die(mysqli_error($conexao));
    
    retorna_dados();
    
}


?>
