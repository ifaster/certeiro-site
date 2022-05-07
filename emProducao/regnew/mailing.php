<?php
    #error_reporting(0);
	require_once("./genlib.php");
	#echo 'bastiao';

class Exemplo
{
	// Guarda uma instância da classe
    static private $instance;
   
    // Um construtor privado
    private function __construct() 
    {
		$host  = '108.179.254.179';//'mysql.ifaster.com.br';
		$user  = 'ifaster';
		$pass  = '83387654';
		$banco = 'ifaster_06';
        $link = mysql_connect($host, $user, $pass);
        if (!$link) {
            die('Não foi possível conectar: ' . mysql_error()."<br><br>");
        }

        $db_selected = mysql_select_db($banco, $link);
        if (!$db_selected) {
            die ('Não foi possível selecionar : ' . mysql_error());
        }
    }

    // O método singleton 
    static public function singleton() 
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }
   
    // Método exemplo
    public function consultar($sql)
    {
        $result = mysql_query($sql);
        if (!$result) {
			#echo 'erro ao executar sql'; 
			$erro = mysql_error($link);
			if ($erro != "") {
			    $result = '54'.$erro;
			} else {
				$result = '55';
			}
            return $result;
        }else{
			$result = '55';  
			#echo 'OKOKOKOK' ; 
			return $result;
		}
    }
}

	//return constants
	$NO_ERROR	= '50';
	$CONEXAO    = '51';
	$TABELA		= '52';
	$SQL_ERROR	= '53';
	$EXEC_ERROR	= '54';
	$EXEC_OK	= '55';

	$conection 	= Exemplo::singleton();
	$result 	= '';//encryptstr('NO DATA POSTED');
	$email 		= $_POST['email'];
	$url 		= $_POST['url'];
	$sql    	= 'insert into MAILING (MAIL_URL, MAIL_EMAIL) VALUES ("'.$url.'", "'.$email.'")';
	$retorno 	= $conection->consultar($sql);
	#echo 'retorno execução:'.$retorno;
	//echo $retorno;
?>
<style type="text/css">
.titulo {
	background-color: #666666;
	border: 1px solid #666666;
	color: #FFFFFF;
	width: 762px;
	margin-top: 10px;
	font-size: 14px;
	border-radius: 5px 5px 0 0;
	padding: 1em;
	font-family: "ubuntu Light", "HattoriHanzoLight", Calibri, sans-serif;
	outline: medium none;
}
.corpo {
	border: 1px solid #666666;
	border-top: 0;
	border-radius: 0 0 5px 5px;
	font-family: "ubuntu Light", "HattoriHanzoLight", Calibri, sans-serif;
	outline: medium none;
	padding-left: 40px;
	padding-right: 40px;
	font-size: 14px;
	width: 710px;
	padding-top: 5px;
	padding-bottom: 5px;
}
.n_cliente {
	font-weight: bold;
	color: #039;
	margin-right: 5px;
	margin-left: 5px;
}
.e_cliente {
	color: #039;
	font-weight: bold;
	margin-right: 5px;
	margin-left: 5px;
}
</style>

<div class="titulo">Seu cadrastro foi feito com sucesso...</div>
<div class="corpo"><p>Obrigado por registrar-se,   <span class="n_cliente">  Cliente  </span> Um e-mail foi enviado pra <span class="e_cliente"><?php echo $email?></span> com detalhes sobre como acessar sua conta para efetuar o download.
  <p>Você recebera um e-mail na sua caixa de entrada, se nao visualizar o mesmo verifique sua caixa de spam, para retornar a pagina anterior clique <a href="https://www.facebook.com/itecinfo" target="_parent">aqui</a>.</div>
