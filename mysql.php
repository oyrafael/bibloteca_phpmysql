<?php
        
	//@RAFAEL_SCHNEIDER
	///////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	/*
		ESTE DOCUMENTO É ORIENTADO A OBJETOS, VISANDO COM QUE APENAS UMA CONEXÃO COM BANCO DE DADOS SE TORNE ATIVO, NESTE EXEMPLO
		UMA VEZ QUE QUALQUER UMA ESTANCIAR A CLASSE APENAS UMA CONEXAO SERÁ EXECUTADA, E TODAS AS INSTÃNCIAS IRÃO COMPARTILHAR A CONEXAO.
		POR ISSO FOI DEFINIDO COMO ESTÁTICO OS DADOS DA CONEXÃO E PROPRIA CONEXÃO.	
	*/
	///////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

	class ConectBD {
			private static $BDServer = "localhost";         // SERVIDOR DE BANCO DE DADOS
			private static $BDUser   =  "root";              // USUARIO BANCO DE DADOS
			private	static $BDPass   =  "1234";              // SENHA BANCO DE DADOS 
			private static $BDBase   =  "agendamento";//"estacaobd";        // BASE DE DADOS SELECIONADA
			private static $BDActive;                       // INDICA SE A CONEXAO ESTA OU NÃO ATIVA
			private static $BDCon;                          // VARIAVEL QUE GUARDA A CONEXAO
			private $BDError;                               // VARIAVEL QUE GUARDA CÓDIGO DE ERRO SE HOUVER
			//private $log;
			
			//---> MÉTODO CONSTRUTOR
			function __construct(){
				//$this->log = $l;
				if(self:: $BDActive == NULL){
					self:: $BDActive =false;
				}
				$this->Conectar();
			}
			/*
				O MÉTODO CONSTRUTOR VERIFICA SE JÁ EXISTE VALOR PARA VÁRIVEL BDActive ESSA VARÍAVEL INDICA SE JÁ EXISTE ALGUMA CONEXAO 
				ATIVA OU NÃO PARA EVITAR QUE SEJAM FEITAS VÁRIAS CONEXOES OU SOLICITAÇÃO DE DESCONEXÃO, SE FOR A PRIMEIRA VEZ ESSA 
				VARIAVEL É DETERMINADA COMO FALSE INDICANDO QUE NÃO HÁ CONEXOES ATIVAS , APÓS ISSO É FEITA JÁ A PRIMEIRA TENTATIVA DE 
				CONEXAO COM BANCO DE DADOS 
			*/
			//---> MÉTODO CONSTRUTOR
				
			//---> MÉTODO PARA CONSTRUIR A BASE DE DADOS
			public function Conectar(){
				if(!self:: $BDActive){
					self:: $BDCon = @mysqli_connect(self::$BDServer,self::$BDUser,self::$BDPass,self::$BDBase);
					$this->BDError =  mysqli_connect_errno();
					if($this->BDError != 0){
						self::$BDActive = false;
						//$this->log->Registrar("MySql " .$this->BDError . " " . $this->BDError() );
						return false;
					}
					else{
						self::$BDActive = true;
						return true;
					}
				}
				else{
					return true;
				}
				
			}
			/*
				ESTA FUNCAO NÃO É NECESSÁRIA SER ATIVADA, QUANDO A CLASSE É INSTANCIADA O PRÓPRIO MÉTODO CONSTRUTOR EXECUTA
				ESSE MÉTODO. PRIMEIRO É VERIFICADO SE HÁ OU NÃO CONEXÕES ATIVAS, SE HAVER JÁ E RETORNADO COMO TRUE INDICANDO QUE A CONEXAO 				JÁ ESTA ATIVA, CASO NÃO POSSUA ELE INICIA O PROCESSO DE CONEXÃO CASO VENHA A APRESENTAR ERRO ESTE ERRO É ARMAZENADO NA   				
				VARIÁVEL BDError, REGISTRADO UM LOG DE ERRO RETORNANDO FALSE INDICANDO QUE HOUVE UM ERRO NA CONEXAO E NÃO FOI POSSÍVEL 
				CONECTAR, CASO TUDO TENHA IDO NOS CONFORMES
			*/
			//---> MÉTODO PARA CONSTRUIR A BASE DE DADOS
			
			//---> FUNCAO DESCONECTAR BASE DE DADOS
			public function Desconectar(){
				if(self:: $BDActive){
					mysqli_close(self::$BDCon);
					return "Desconectado com sucesso" ;
				}
				else{
					return "Nao Existem Conexoes Ativas";
				}
			}
			/*
				ESTA FUNCAO REALIZA DESCONEXÃO COM A BASE DE DADOS, PARA ISSO ELE VERIFICA SE HÁ CONEXÕES ATIVAS PARA QUE SEJA REALIZADO 
				O PROCEDIMENTO RETORNANDO CONFIRMAÇÃO POR MENSAGEM, CASO NÃO TENHA CONEXOES RETORNA VIA MENSAGENS QUE NÃO POSSUI CONEXOES
				ATIVAS
			*/
			//---> FUNCAO DESCONECTAR BASE DE DADOS
			
			//---> BASE DE ERROS POSSIVEIS
			public function BDError(){
				switch($this->BDError){
					case 0    :  return  "Mysql: Nenhum erro foi detectado" ; break;
					case 1044 :  return  "Mysql: Usuario Incorreto" ; break ;
					case 2002 :  return  "Mysql: Servidor incorreto" ; break ;
					case 1045 :  return  "Mysql: Senha incorreta" ; break ;
					case 1049 :  return  "Mysql: Base de dados nao encontrada" ; break ;
					case 1146 :  return  "Mysql: Erro ao executar SQL -> Tabela não encontrada, erro de sintaxe"; break;
					case 1064 :  return  "Mysql: Erro ao executar SQL -> Erro de sintaxe "; break;
					case 1054 :  return  "Mysql: Erro ao executar SQL -> Coluna não encontrada, erro de sintaxe"; break;
					case 1136 :  return  "Mysql: Variaveis não condizem com a tipagem na inserção" ; break;
					default:     return  "Mysql: Erro Desconhecimento" ;
				}
			}
			/*
				MÉTODO RETORNA A REFERENCIA DO ERRO ARMAZENADO NA VARIAVEL BDErrror
			*/
			//---> BASE DE ERROS POSSIVEIS
			
			
			//--->EXECUTA CODIGO SQL
			function Executar($sql){
				$desc= false;  
				$result = false;
				if(!self ::$BDActive){     //TESTA SE EXISTE CONEXAO COM BD ATIVO
					$this->Conectar();  // SE NÃO TIVER CONECTA A BASE DE DADOS
					$desc = true;
				}
				if(self ::$BDActive){
					$result = mysqli_query(self :: $BDCon,$sql);  // EXECUTA COMANDO SQL
					$this->BDError =  mysqli_errno(self:: $BDCon);  // ARMAZENA SE HOUVE ALGUM ERRO
					if($this->BDError != 0){   // SE HOUVER ERRO
						//$this->log->Registrar ("MySql " .$this->BDError . " " . $this->BDError() );  // REGISTRA O LOG DE ERRO
					}
				}
				if($desc){  // COMO NÃO HÁ CONEXOES ATIVAS  E FOI FEITA UMA CONEXAO POR DEMANDA EXECUTA DESCONECTAR
					$this->Desconectar();
				}
				return $result;
			}
			/*
				ESTA FUNÇÃO EXECUTA OS COMANDOS MYSQL
			*/
			//--->EXECUTA CODIGO SQL
	
	
	}
	?>
