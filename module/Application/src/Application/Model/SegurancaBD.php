<?php
	namespace Application\Model;
	
	/**
		* Classe SegurancaBD
		*
		* Responsável por operações em banco de dados. 
		* 
		* Sempre usar ANSI SQL 
		*/
	class SegurancaBD {
		protected $dbAdapter;
		
		/**
			* Método Construtor
			*
			* Recebe uma instância de dbAdapter e armazena localmente para utilização dos métodos.
			* 
			* @param dbAdapter $dbAdapter
			*/
		public function __construct(\Zend\Db\Adapter\Adapter $dbAdapter) {
			$this->dbAdapter = $dbAdapter;
		}
		
		
		
		
		/**
			* Método consultaDisponibilidadeUsuario()
			*
			* Retorna true/false para existencia de usuário
			* 
			* @return boolean
			*/
		public function consultaDisponibilidadeUsuario($sUsuario) {
			$bRetorno = true;
			$sQuery = "select id from users where email='".$sUsuario."'";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				if ($umResultSet->count()>0) {
					$bRetorno = false;
				}
				else {
					$bRetorno = true;
				}
			}
			return $bRetorno;
		}
		
		
		
		
		/**
			* Método capturaDadosAssociadosChaveIndicacao()
			*
			* Retorna true/false para existencia de usuário
			* 
			* @return boolean
			*/
		public function capturaDadosAssociadosChaveIndicacao($sChave) {
			$sQuery = "select id_usuario,email from indicacao_emails where chave_seguranca='".$sChave."'";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				if ($umResultSet->count()>0) {
					$linha = $umResultSet->next();
					return array("id_usuario"=>$linha["id_usuario"],"email"=>$linha["email"]);
				}
				else {
					return false;
				}
			}
		}		
		
		
		
		
		/**
			* Método capturaDadosAssociadosChaveRecuperacaoSenha()
			*
			* Retorna true/false para existencia de usuário
			* 
			* @return boolean
			*/
		public function capturaDadosAssociadosChaveRecuperacaoSenha($sChave) {
			$sQuery = "select id_user from users_password_recovery where hashkey='".$sChave."' and used is null";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				if ($umResultSet->count()>0) {
					$linha = $umResultSet->next();
					return $linha["id_user"];
				}
				else {
					return false;
				}
			}
		}		
		
		
		
		
		/**
			* Método inutilizaChaveRecuperacaoSenha()
			*
			* Retorna true/false quanto a ação de inutilização da chave de acesso
			* 
			* @return boolean
			*/
		public function inutilizaChaveRecuperacaoSenha($sChave) {
			$sQuery = "update users_password_recovery set used=1,used_date='".date("Y-m-d H:i:s")."' where hashkey='".$sChave."'";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				return true;
			}
			else {
				return false;
			}
		}		
		
		
		
		
		/**
			* Método bonificaUsuarioQueIndicou()
			*
			* Como o usuário que recebeu a indicação fez o cadastro, bonifica o usuário que indicou
			* 
			* @return boolean
			*/
		public function bonificaUsuarioQueIndicou($iIdUsuarioQueIndicou,$sEmailNovaConta,$iIdUsuarioNovaConta) {
			$sQuery = "update indicacao_emails set id_usuario_criado='".$iIdUsuarioNovaConta."',dt_usuario_criado='".date("Y-m-d H:i:s")."' where email='".$sEmailNovaConta."' and id_usuario='".$iIdUsuarioQueIndicou."'";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				return true;
			}
			else {
				return false;
			}
		}
		
		
		/**
			* Método verificaUsuario()
			*
			* Retorna true/false para existencia de usuário
			* 
			* @return boolean
			*/
		public function insereDadosLog($iIdUsuario,$sModuloOrigem,$sDescricao,$sIP,$sDataRegistro,$sURL,$sPost) {
			$sQuery = "insert into log (id,id_user,source,description,url,register_date,ip,post_data) values(null,'" . $iIdUsuario . "','" . $sModuloOrigem . "','" . $sDescricao . "','" . $sURL . "','" . $sDataRegistro . "','" . $sIP . "','" . $sPost . "')";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				return $this->dbAdapter->getDriver()->getLastGeneratedValue();
			}
			else {
				return false;
			}
		}

		
		


		/**
		 * Método getUserIdByValidationCode()
		 *
		 * Retorna o id do usuário conforme o código de validação
		 *
		 * @param string $sCode
		 * @return int
		 */
		public function getUserIdByValidationCode($sCode) {
		    $sRetorno = false;
		    $sQuery = "select id from users where activation_id='".mb_strtolower($sCode)."'";
		    if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
		      $line = $umResultSet->next();
		      $sRetorno = $line["id"];
		    }
		    return $sRetorno;
		}
		
		
	}