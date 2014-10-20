<?php
	namespace Application\Model;
	
	use Application\Model\SegurancaBD;
	use Zend\Db\ResultSet\ResultSet;
	use Zend\Session\Config\StandardConfig;
	use Zend\Session\Container;
	
	class Seguranca {
		private $dbAdapter;
		private $umConteiner;
		private $umSessionManager;
		protected $umaSegurancaBD;
	
		public function __construct($sm) {
			$this->dbAdapter = $sm->get("Zend\Db\Adapter\Adapter");
			$this->umSessionManager = $sm->get('Zend\Session\SessionManager');
			$this->umContainer = new Container("xmile_xms",$this->umSessionManager);
		}
	
	
	
		
		
		/**
			* Método verificaUsuario()
			*
			* Retorna true/false para existencia de usuário
			* 
			* @return boolean
			*/
		public function verificaUsuario($sUsuario) {
			if (!isset($this->umaSegurancaBD)) {
				$this->umaSegurancaBD = new SegurancaBD($this->dbAdapter);
			}
			if ($this->umaSegurancaBD->consultaDisponibilidadeUsuario($sUsuario)) {
				return true;
			}
			else {
				return false;
			}
		}
	
		
		
		/* Controle de usuários na sessão */
		public function validaLogin($sUsuario,$sSenha) {
			$sQuery = "select id from users where email='" . $sUsuario . "' and password='" . md5($sSenha) . "' and status='ativo'";
			$umStandment = $this->dbAdapter->query($sQuery);
			$umResultSet = $umStandment->execute();
			if ($umResultSet->count()>0) {
			    $linha = $umResultSet->next();
				return $linha["id"];
			}
			else {
				return false;
			}
		}



		/* 
		 * Verifica se um código informado é existente 
		 * 
		 * @param string $sCode
		 * @return int $iUserId
		 * */
		public function validaCodigoCadastro($sCode) {
			if (!isset($this->umaSegurancaBD)) {
				$this->umaSegurancaBD = new SegurancaBD($this->dbAdapter);
			}
			$iUserId = $this->umaSegurancaBD->getUserIdByValidationCode($sCode);
			return $iUserId;
		}
		




		
		/* Valida a sessão para ver se está ativa (usuário está logado) */
		public function sessaoAtiva($umObj) {
		    $umContainer = $umObj->getContainer();
		    //print($umContainer->iIdUsuarioSiteLogado); exit;
			if (isset($umContainer->iIdUsuarioSiteLogado)) {
				if ($umContainer->iIdUsuarioSiteLogado!="") {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		
		
		
		
		
		
		/* Verifica chave de indicação se é válida */
		public function verificaChaveIndicacao($sChave) {
			if (!isset($this->umaSegurancaBD)) {
				$this->umaSegurancaBD = new SegurancaBD($this->dbAdapter);
			}
			$aDados = $this->umaSegurancaBD->capturaDadosAssociadosChaveIndicacao($sChave);
			return $aDados;
		}
		
		
		
		
		
		
		/* Verifica chave de recuperação senha se é válida */
		public function verificaChaveRecuperacaoSenha($sChave) {
			if (!isset($this->umaSegurancaBD)) {
				$this->umaSegurancaBD = new SegurancaBD($this->dbAdapter);
			}
			$iIdUsuario = $this->umaSegurancaBD->capturaDadosAssociadosChaveRecuperacaoSenha($sChave);
			return $iIdUsuario;
		}
		
		
		
		
		
		
		/* Inutiliza a chave de recuperação senha */
		public function inutilizaChaveRecuperacaoSenha($sChave) {
			if (!isset($this->umaSegurancaBD)) {
				$this->umaSegurancaBD = new SegurancaBD($this->dbAdapter);
			}
			$iIdUsuario = $this->umaSegurancaBD->inutilizaChaveRecuperacaoSenha($sChave);
			return $iIdUsuario;
		}
		
		
		
		
		
		public function confirmaAssociacaoIndicacao($iIdUsuarioQueIndicou,$sEmailNovaConta,$iIdUsuarioNovaConta) {
			if (!isset($this->umaSegurancaBD)) {
				$this->umaSegurancaBD = new SegurancaBD($this->dbAdapter);
			}
			$this->umaSegurancaBD->bonificaUsuarioQueIndicou($iIdUsuarioQueIndicou,$sEmailNovaConta,$iIdUsuarioNovaConta);
			return true;
		}
		
		
		
		
		/* Registra dados no log */
		public function registraLog($sModuloOrigem,$sDescricao) {
			if (!isset($this->umaSegurancaBD)) {
				$this->umaSegurancaBD = new SegurancaBD($this->dbAdapter);
			}
			// Teve dados de POST?
			$sStringPost = "";
			if (isset($_POST)) {
				if (count($_POST)>0) {
					$aKeys = array_keys($_POST);
					for ($i=0;$i<count($aKeys);$i++) {
						$sStringPost .= $aKeys[$i]."=".$_POST[$aKeys[$i]]."&";
					}
				}
			}
			$iIdRegistro = $this->umaSegurancaBD->insereDadosLog($this->umContainer->iIdUsuarioSiteLogado,$sModuloOrigem,$sDescricao,$_SERVER["REMOTE_ADDR"],date("Y-m-d H:i:s"),$_SERVER["REQUEST_URI"],$sStringPost);
			return $iIdRegistro;
		}
		
		
	}
