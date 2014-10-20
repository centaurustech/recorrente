<?php
	namespace Webadmin\Model;
	
	/**
		* Classe ManutencaoBD
		*
		* Responsável por operações em banco de dados. 
		* 
		* Sempre usar ANSI SQL 
		*/
	class WebAdminBD {
	
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
			* Método retornaTodosProjetos()
			*
			* Captura o ID dos projetos que ainda não foram moderados
			* 
			* @return $resultset
			*/
		public function retornaTodosProjetos() {
		  $aIdsProjetos = array();
			$sQuery = "select id from projects where 1 order by title";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($linha = $umResultSet->next()) {
				    array_push($aIdsProjetos,$linha["id"]);
				}
				return $aIdsProjetos;
			} else {
				return false;
			};
		}


		/**
			* Método retornaTodasAssinaturasDoProjeto()
			*
			* Captura o ID das assinaturas do projeto
			* 
			* @return $resultset
			*/
		public function retornaTodasAssinaturasDoProjeto($id_projeto) {
		  $aIdsAssinaturas = array();
			$sQuery = "select payment_gateway_code from contributions where id_project='$id_projeto' order by dt_contribution";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($linha = $umResultSet->next()) {
				    array_push($aIdsAssinaturas,$linha["payment_gateway_code"]);
				}
				return $aIdsAssinaturas;
			} else {
				return false;
			};
		}

		/**
			* Método retornaTodosProjetosNaoModerados()
			*
			* Captura o ID dos projetos que ainda não foram moderados
			* 
			* @return $resultset
			*/
		public function retornaTodosProjetosNaoPublicados() {
		  $aIdsProjetos = array();
			$sQuery = "select id from projects where id_status=1 order by dt_update desc,dt_registration desc";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($linha = $umResultSet->next()) {
				    array_push($aIdsProjetos,$linha["id"]);
				}
				return $aIdsProjetos;
			} else {
				return false;
			};
		}


		/**
			* Método localizaUsuario($usuario)
			*
			* retorna quantidade de usuarios existem com o username passado
			* 
			* @return int
			
			*/
		public function localizaUsuario($usuario) {
				if (preg_match('/^[a-za-zA-Z\d_]{4,28}$/i', $usuario)) { 

						$q = "select count(id) as quantidade from users where upper(email) = '".strtoupper(trim($usuario))."'";
						if ($umResultSet = $this->dbAdapter->query($q)->execute()) {
								$l = $umResultSet->next();
								if ($l["quantidade"]>0) {
										$temp = 'Usuário já existe.';
								} else {
										$temp = 0;
								};
						} else {
								$temp = "Erro ao verificar usuário";
						};
				} else {
						$temp = "Nome de usuário inválido.";						
				};
				return $temp;
		}










		public function retornaEpisodios() {
			$sQuery = "select id_temporada as temporada, numero as numero from episodios group by temporada, numero order by temporada, numero";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				return $umResultSet;
			} else {
				return false;
			};
		}









        
	
		/**
			* Método dadosLogin()
			*
			* Captura da base os dados de login e senha do id usuario informado.
			* 
			* @param int $iIdUsuario
			* @return mixed
			*/
		public function dadosLogin($iIdUsuario) {
			$sQuery = "select email,password from users where id=" . $iIdUsuario;
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				$linha = $umResultSet->next();
				return $linha;
			}
			else {
				return false;
			}
		}
	
	
		
		
		/**
			* Método capturaTodosPerfis()
			*
			* Captura da base os dados todos os perfis associados.
			* 
			* @param int $iIdUsuario
			* @return mixed
			*/
		public function capturaTodosPerfis($iIdUsuario) {
			$aTemp = array();
			$sQuery = "select id_profile from users_profiles where id_user=" . $iIdUsuario;
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($linha = $umResultSet->next()) {
					array_push($aTemp,$linha["id_perfil"]);
				}
				return $aTemp;
			}
		}
		
		
		
		/**
			* Método capturaDadosBasicos()
			*
			* Captura da base os dados os campos nome, sexo, data_nascimento e data_cadastro do usuário
			* 
			* @param int $iIdUsuario
			* @return mixed
			*/
		public function capturaDadosBasicos($iIdUsuario) {
			$sQuery = "select * from users where id=" . $iIdUsuario;
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				$linha = $umResultSet->next();
				return $linha;
			}
			else {
				return false;
			}
		}



		/**
		 * Método getDefaultMessageByType()
		 *
		 * Captura a mensagem padrão conforme o tipo
		 *
		 * @param string $sType
		 * @return string
		 */
		public function getDefaultMessageByType($sType) {
			$sQuery = "select message from messages_defaults where type='" . $sType . "'";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				$linha = $umResultSet->next();
				return $linha["message"];
			}
			else {
				return false;
			}
		}



		/**
		 * Método createMessageIntoDB()
		 *
		 * Cria uma mensagem
		 *
		 * @param string $sType
		 * @return string
		 */
		public function createMessageIntoDB($iUserIdFrom,$iUserIdTo,$iUserIdReplyTo = null,$iProjectId = null,$sSubject,$sMessage,$sHashKey) {
			$sQuery = "insert into messages (id_user_from,id_user_to,id_user_replied,id_project,subject,content,unique_hash) values(".$iUserIdFrom.",".$iUserIdTo.",'".$iUserIdReplyTo."','".$iProjectId."','".$sSubject."','".$sMessage."','".$sHashKey."')";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				return $this->dbAdapter->getDriver()->getLastGeneratedValue();
			}
			else {
				return false;
			}
		}
		
		
	
	}
