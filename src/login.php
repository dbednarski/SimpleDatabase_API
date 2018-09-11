<?php 


$metodo = $_SERVER["REQUEST_METHOD"];

switch($metodo)
{
    case 'POST':
        login();
        break;
    case 'DELETE':
        logout();
        break;
    default:
        // Invalid Request Method
        retorna_dados(array(405, "Method Not Allowed", "Método não permitido. Contate o desenvolvedor."));
        break;
}



function login() {

  $email = $_POST['email'];
  $passwd = md5($_POST['passwd']);
  
  // Previne usuário de requerer logar novamente, caso já esteja logado
  if(isset($_COOKIE['email'])) {
    setcookie("email", $email, time()+3600, "/");
    $status = array("status" => 409, "message" => "Conflict", "user_message" => "Você já está logado.");
    header('Content-Type: application/json');
    echo json_encode($status, JSON_UNESCAPED_UNICODE);
    exit;
  }

  $conexao = mysqli_connect("localhost","root","");
  $db = mysqli_select_db($conexao, "usuarios") or die(mysqli_error($conexao));

  // obtendo a passwd do banco de dados, escapando o e-mail de caracteres especiais
  $query = sprintf("SELECT passwd FROM users WHERE email = \"%s\";", mysqli_real_escape_string($conexao, $email));
  $aux = mysqli_query($conexao, $query) or die(mysqli_error($conexao));
  $passwd_db = mysqli_fetch_assoc($aux)['passwd'];

  mysqli_close($conexao);


  // Se tudo estiver correto, o usuário se logará, iniciando uma sessão
  if ($passwd === $passwd_db) {
    setcookie("email", $email, time()+3600, "/");
    $status = array("status" => 201, "message" => "Creation OK", "user_message" => "");
    header("Content-type: application/json", JSON_UNESCAPED_UNICODE);
    echo json_encode($status);
  }
  else {
    $status = array("status" => 401, "message" => "Unauthorized", "user_message" => "E-mail e/ou senha incorreta.");
    header("Content-type: application/json");
    echo json_encode($status, JSON_UNESCAPED_UNICODE);
  }
}  



function logout() {

    // Se o usuário não estiver logado, retorna erro
    if(!isset($_COOKIE['email'])) {
        $saida = array("status" => 401, "message" => "Unauthorized", "user_message" => "Usuário não está logado.");
        header("Content-type: application/json");
        echo json_encode($saida, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $email = $_COOKIE['email'];
    setcookie("email", $email, time()-3600, "/");

    $status = array("status" => 200, "message" => "OK", "user_message" => "");
    header('Content-type: application/json');
    echo json_encode($status, JSON_UNESCAPED_UNICODE);
}
 
?>
