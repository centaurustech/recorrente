<?php
	// Classe Perfil
	
	namespace Application\Model;
	
	use Application\Model\PerfilBD;
	use Application\Model\Mensagem;
	use Zend\Session\Container;
	
	class Perfil {
	
		protected $iIdUsuario;
		protected $sUsuario;
		protected $sSenha;
		protected $sEmail;
		protected $bAtivo;
		protected $aIdsPerfis = array();
		protected $umServiceManager;
		protected $umSessionManager;
		protected $umContainer;
		protected $config;
		protected $umPerfilBD;
		protected $umController;
		/* Dados Básicos */
		protected $sNome;
		protected $sSexo;
		protected $sCPF;
		protected $sCNPJ;
		protected $dDataNascimento;
		protected $dDataCadastro;
		protected $sArquivoFoto;
		protected $aMensagems = array();
		protected $iRelacao;
		/* Dados Complementares */
		protected $aEnderecos = array();
		protected $aTelefones = array();
		protected $sTipo;
		protected $sCEP;
		protected $sEndereco;
		protected $sIdCidade;
		protected $sActivationId;
		protected $sCompanyName;
		/* Coleções */
		protected $aProjects = array(); // Coleção de projetos do usuário (indica que ele é projetista também)
		protected $aContributionsMade = array(); // Contribuições realizadas [id_contribution, id_project, id_payment, value, date]
		protected $aContributionsReceived = array(); // Contribuições recebidas [id_contribution, id_project, id_payment, value, date]
		
		/**
			* Método Construtor
			*
			* Inicializa os serviços (Service Manager, config), instancia a camada de conexão do objeto.
			* Também carrega as propriedades ID Usuario, Usuario, Senha e array com Ids dos perfis.
			* 
			* @param ServiceManager $umSM
			* @param int $iIdUsuario
			* @param string $sUsuario
			*/
		public function __construct($umSM,$iIdUsuario = null,$sUsuario = null,$sEmail = null) {
			$this->umServiceManager = $umSM;
			$this->config = $this->umServiceManager->get("config");	// Captura o config setado em global|local
			$this->umPerfilBD = new PerfilBD($this->umServiceManager->get("Zend\Db\Adapter\Adapter"));
			$this->umSessionManager = $this->umServiceManager->get('Zend\Session\SessionManager');
			$this->umContainer = new Container("xmile_xms",$this->umSessionManager); // Cria o container, usando o sessionmanager criado no Module.php
			// Se tentou instanciar por $sUsuario, significa que precisa captura seu ID antes
			if (!empty($sEmail)) {
				$iIdUsuario = $this->umPerfilBD->retornaIdPorEmail($sEmail);
			}
			if (!empty($sUsuario)) {
				$iIdUsuario = $this->umPerfilBD->retornaId($sUsuario);
			}
			if (!empty($iIdUsuario)) {
				$this->iIdUsuario = $iIdUsuario;
				$aDados = $this->umPerfilBD->dadosLogin($this->iIdUsuario);
				$this->aEnderecos = $this->umPerfilBD->dadosEnderecos($this->iIdUsuario);
				$this->aTelefones = $this->umPerfilBD->dadosTelefones($this->iIdUsuario);
				$this->sUsuario = $aDados["email"];
				$this->sSenha = $aDados["password"];
				$this->sEmail = $aDados["email"];
				$this->aIdsPerfis = $this->umPerfilBD->capturaTodosPerfis($this->iIdUsuario);
				if ($aDados["status"]=='ativo') {
					$this->bAtivo = true;
				}
				else {
					$this->bAtivo = false;
				}
			}
		}
		
		
		/**
			* Método setController()
			*
			* Passa uma instância do objeto controller para a classe.
			* 
			* @param Controller $umController
			* @return null
			*/
		public function setController($umController) {
			$this->umController = $umController;
		}


		/**
			* Método getId()
			*
			* Retorna o id usuarios
			*
			* @return int $this->iIdUsuario
			* 
			*/
		public function getId() {
			return $this->iIdUsuario;
		}


		
		/**
			* Método getIdsPerfis()
			*
			* Retorna os ids perfis associados
			*
			* @return mixed $this->aIdsPerfis
			* 
			*/
		public function getIdsPerfis() {
			return $this->aIdsPerfis;
		}


		/**
			* Método getUsuario()
			*
			* Retorna o usuario (login)
			*
			* @return int $this->sUsuario
			* 
			*/
		public function getUsuario() {
			return $this->sUsuario;
		}


		/**
			* Método getSenha()
			*
			* Retorna a senha
			*
			* @return int $this->sSenha
			* 
			*/
		public function getSenha() {
			return $this->sSenha;
		}



		/**
			* Método getNome()
			*
			* Captura o nome
			* 
			* @return null
			*/
		public function getNome() {
			if (empty($this->sNome)) {
				$this->dadosBasicos();
			}
			return $this->sNome;
		}



		/**
			* Método getEmail()
			*
			* Captura o email
			* 
			* @return null
			*/
		public function getEmail() {
			if (empty($this->sEmail)) {
				$this->dadosBasicos();
			}
			return $this->sEmail;
		}



		/**
			* Método getPrimeiroNome()
			*
			* Captura o primeiro nome apenas
			* 
			* @return null
			*/
		public function getPrimeiroNome() {
			if (empty($this->sNome)) {
				$this->dadosBasicos();
			}
			if (strpos($this->sNome," ")>0) {
				$sPrimeiroNome = substr($this->sNome,0,strpos($this->sNome," "));
			}
			else {
				$sPrimeiroNome = $this->sNome;
			}
			return $sPrimeiroNome;
		}



		/**
			* Método getSexo()
			*
			* Captura o sexo
			* 
			* @return null
			*/
		public function getSexo() {
			if (empty($this->sSexo)) {
				$this->dadosBasicos();
			}
			return $this->sSexo;
		}


		/**
			* Método getDataNascimento()
			*
			* Captura a data de nascimento
			* 
			* @return null
			*/
		public function getDataNascimento() {
			if (empty($this->dDataNascimento)) {
				$this->dadosBasicos();
			}
		  return $this->dDataNascimento;
		}


		/**
			* Método getDataCadastro()
			*
			* Captura a data de cadastro
			* 
			* @return null
			*/
		public function getDataCadastro() {
			return $this->dDataCadastro;
		}


		/**
			* Método getFoto()
			*
			* Captura a data de cadastro
			* 
			* @param string $sSize
			* @return null
			*/
		public function getFoto($sSize=null) {
			if (empty($this->sArquivoFoto)) {
    			$this->sArquivoFoto = "/users_images/".$this->iIdUsuario."/{PASTA}/profile.jpg";
			}
			$sPhotoFile = str_replace("{PASTA}",$sSize,$this->sArquivoFoto);
			if (!file_exists($_SERVER["DOCUMENT_ROOT"].$sPhotoFile)) {
	        $sPhotoFile = "/users_images/user-avatar.png";
			}
				
			return $sPhotoFile;
		}

		
		

		/**
		 * Método getTipo()
		 *
		 * Captura o tipo do perfil (PF ou PJ)
		 *
		 * @return null
		 */
		public function getTipo() {
			if (empty($this->sTipo)) {
				$this->dadosBasicos();
			}
			return $this->sTipo;
		}




		/**
		 * Método getCPF()
		 *
		 * Captura o CPF
		 *
		 * @return null
		 */
		public function getCPF() {
			if (empty($this->sCPF)) {
				$this->dadosBasicos();
			}
			return $this->sCPF;
		}




		/**
		 * Método getCNPJ()
		 *
		 * Captura o CNPJ
		 *
		 * @return null
		 */
		public function getCNPJ() {
			if (empty($this->sCNPJ)) {
				$this->dadosBasicos();
			}
			return $this->sCNPJ;
		}




		/**
		 * Método getCompanyName()
		 *
		 * Captura o CNPJ
		 *
		 * @return null
		 */
		public function getCompanyName() {
			if (empty($this->sCompanyName)) {
				$this->dadosBasicos();
			}
			return $this->sCompanyName;
		}
		



		/**
		 * Método getProjects()
		 *
		 * Captura os projetos que o usuário é proprietário
		 *
		 * @param array $aExcludeStatus
		 * @return null
		 */
		public function getProjects($aExcludeStatus = null) {
			if (empty($this->aProjects)) {
				$aProjectsIds = $this->umPerfilBD->getAllProjectsByUserInDB($this->iIdUsuario,$aExcludeStatus);
				foreach ($aProjectsIds as $iProjectId) {
				    array_push($this->aProjects,new Project($this->umServiceManager,$iProjectId));
				}
			}
			return $this->aProjects;
		}




		/**
		 * Método getAllActivedProjects()
		 *
		 * Captura os projetos que o usuário é proprietário
		 *
		 * @return null
		 */
		public function getAllActivedProjects() {
			if (empty($this->aProjects)) {
				$aProjectsIds = $this->umPerfilBD->getAllActivedProjectsByUserInDB($this->iIdUsuario);
				foreach ($aProjectsIds as $iProjectId) {
					array_push($this->oProjects,new Project($this->umServiceManager,$iProjectId));
				}
			}
			return $this->aProjects;
		}




		/**
		 * Método getAllActiveContributionsMade()
		 *
		 * Captura todas as contribuições ativas que o usuário fez em qualquer projeto, que tenha sido realizada com sucesso
		 *
		 * @param boolean $bReverse
		 * @return array
		 */
		public function getAllActiveContributionsMade($bReverse = false) {
		    $this->aContributionsMade = $this->umPerfilBD->getAllActiveContributionsMadeInDB($this->iIdUsuario,$bReverse);
		    return $this->aContributionsMade;
		}
		



		/**
		 * Método getAllContributionsMade()
		 *
		 * Captura todas as contribuições que o usuário fez em qualquer projeto, que tenha sido realizada com sucesso
		 *
		 * @param boolean $bReverse
		 * @return array
		 */
		public function getAllContributionsMade($bReverse = false) {
			$this->aContributionsMade = $this->umPerfilBD->getAllContributionsMadeInDB($this->iIdUsuario,$bReverse);
			return $this->aContributionsMade;
		}




		/**
		 * Método getAllContributionsReceived()
		 *
		 * Captura todas as contribuições recebidas até o momento nos projetos do usuário
		 *
		 * @param boolean $bReverse
		 * @return array
		 */
		public function getAllContributionsReceived($bReverse = false) {
			$this->aContributionsReceived = $this->umPerfilBD->getAllContributionsReceivedInDB($this->iIdUsuario,$bReverse);
			return $this->aContributionsReceived;
		}




		/**
		 * Método getSumReceivedContributionsAllTime()
		 *
		 * Retorna a soma das contribuições recebidas em todos os tempos de todos os projetos
		 *
		 * @return float
		 */
		public function getSumReceivedContributionsAllTime() {
			$fValue = $this->umPerfilBD->getSumReceivedContributionsAllTimeInDB($this->iIdUsuario);
			return $fValue;
		}




		/**
		 * Método getSumReceivedContributionsMonth()
		 *
		 * Retorna a soma das contribuições recebidas em um determinado mês e ano para o projeto
		 *
		 * @param int $iMonth
		 * @param int $iYear
		 * @return float
		 */
		public function getSumReceivedContributionsMonth($iMonth,$iYear) {
			$fValue = $this->umPerfilBD->getSumReceivedContributionsMonthInDB($this->iIdUsuario,$iMonth,$iYear);
			return $fValue;
		}




		/**
		 * Método getContributionsVariationLastMonth()
		 *
		 * Retorna um valor percentual, se possível, representando a variação de arrecadação do mês atual com o mês anterior
		 *
		 * @return float
		 */
		public function getContributionsVariationLastMonth() {
			$fValue = $this->umPerfilBD->getContributionsVariationLastMonthInDB($this->iIdUsuario);
			return $fValue;
		}
		



		/**
		 * Método getSumProjectedContributionsMonth()
		 *
		 * Retorna a soma das possíveis contribuições que os projetos irão gerar até um determinado mês e ano
		 *
		 * @param int $iMonth
		 * @param int $iYear
		 * @return float
		 */
		public function getSumProjectedContributionsMonth($iMonth,$iYear) {
			$fValue = $this->umPerfilBD->getSumProjectedContributionsMonthInDB($this->iIdUsuario,$iMonth,$iYear);
			return $fValue;
		}
		




		/**
			* Método getEndereco()
			*
			* Captura a data de cadastro
			* 
			* @return null
			*/
		public function getEndereco($sTipo) {
			if (empty($this->aEnderecos)) {
			  if (!empty($this->iIdUsuario)) {  
				    $this->aEnderecos[$sTipo] = $this->umPerfilBD->dadosEnderecos($this->iIdUsuario);
			  }
			  else {
			      $this->aEnderecos[$sTipo] = array();
			  }
			}
			return @$this->aEnderecos[$sTipo];
		}





		/**
		 * Método setEndereco()
		 *
		 * Captura a data de cadastro
		 *
		 * @return null
		 */
		public function setEndereco($sTipo,$sCEP,$sEndereco,$sIdCidade,$sNumero=null,$sComplemento=null,$sBairro=null) {
		  unset($this->aEnderecos[$sTipo]);
		  $this->aEnderecos[$sTipo] = array("zip"=>$sCEP,"address"=>$sEndereco,"id_city"=>$sIdCidade,"number"=>$sNumero,"complement"=>$sComplemento,"neighborhood"=>$sBairro);
			return @$this->aEnderecos[$sTipo];
		}
		




		/**
			* Método getTelefones()
			*
			* Retorna os telefones
			* 
			* @return null
			*/
		public function getTelefones() {
			if (empty($this->aTelefones)) {
				$this->aTelefones = $this->umPerfilBD->dadosTelefones($this->iIdUsuario);
			}
			return @$this->aTelefones;
		}

		
		
		

		/**
			* Método getAtivo()
			*
			* Retorna se o usuário está ativo ou não
			* 
			* @return null
			*/
		public function getAtivo() {
			if (empty($this->bAtivo)) {
				$this->dadosBasicos();
			}
			return $this->bAtivo;
		}





		/**
		 * Método setAtivo()
		 *
		 * Retorna se o usuário está ativo ou não
		 *
		 * @return null
		 */
		public function setAtivo($bAtivo) {
		    if (empty($this->bAtivo)) {
		        $this->dadosBasicos();
		    }
		    $this->bAtivo = $bAtivo;
		}
		




		
		/**
			* Método setIdPerfil()
			* 
			*/
		public function setIdPerfil($iIdPerfil) {
			
			if (!in_array($iIdPerfil,$this->aIdsPerfis)) {
			    array_push($this->aIdsPerfis,$iIdPerfil);
			}
			
		}


		/**
			* Método setUsuario()
			*
			*/
		public function setUsuario($sUsuario) {
			$this->sUsuario = $sUsuario;
		}


		/**
			* Método getNome()
			*
			*/
		public function setNome($sNome) {
			$this->sNome = $sNome;
		}



		/**
			* Método setSexo()
			*
			*/
		public function setSexo($sSexo) {
			$this->sSexo = $sSexo;
		}


		/**
			* Método setDataNascimento()
			*
			*/
		public function setDataNascimento($dDataNascimento) {
			$this->dDataNascimento = $dDataNascimento;
		}



		/**
			* Método setEmail()
			*
			*/
		public function setEmail($sEmail) {
			$this->sEmail = $sEmail;
		}



		/**
			* Método setSenha()
			*
			*/
		public function setSenha($sSenha) {
			$this->sSenha = $sSenha;
		}



		/**
		 * Método setTipo()
		 *
		 */
		public function setTipo($sTipo) {
			$this->sTipo = $sTipo;
		}



		/**
		 * Método setCPF()
		 *
		 */
		public function setCPF($sCPF) {
			$this->sCPF = $sCPF;
		}



		/**
		 * Método setCNPJ()
		 *
		 */
		public function setCNPJ($sCNPJ) {
			$this->sCNPJ = $sCNPJ;
		}



		/**
		 * Método setIdAtivacao()
		 *
		 */
		public function setIdAtivacao($sIdAtivacao) {
			$this->sActivationId = $sIdAtivacao;
		}



		/**
		 * Método setCompanyName()
		 *
		 */
		public function setCompanyName($sName) {
			$this->sCompanyName = $sName;
		}
		


		/**
			* Método adicionaEndereco()
			*
			*/
		public function adicionaEndereco($sTipoEndereco,$aValores) {
			$this->aEnderecos[$sTipoEndereco] = $aValores;
		}


		/**
			* Método adicionaTelefone()
			*
			*/
		public function adicionaTelefone($sTipo,$sTitulo,$sTelefone) {
			array_push($this->aTelefones,array("type"=>$sTipo, "title"=>$sTitulo,"number"=>$sTelefone));
		}


		/**
		 * Método adicionaTelefone()
		 *
		 */
		public function isProjectContributor($iProjectId) {
		    $bContributor = false;
		    $bContributor = $this->umPerfilBD->userIsProjectContributor($this->iIdUsuario,$iProjectId);
		    return $bContributor;
		}



		/**
			* Método dadosBasicos()
			*
			* Este método é acionado sempre que uma das propriedades de dados básicos (sNome, dDataNascimento, sSexo, dDataCadastro, sArquivoFoto)
			* forem requisitadas por método GET próprio mas estejam vazias
			* 
			* Existe uma peculiaridade em sArquivoFoto: ele vem com um string de máscara chamado {PASTA}, que deve ser substituido por uma das
			* pastas de tamanhos disponíveis: 150x150, 110x110, thumb_moldura
			*
			* @param Controller $umController
			* @return null
			*/
		private function dadosBasicos() {
		  if (!empty($this->iIdUsuario)) {
    			$aDados = $this->umPerfilBD->capturaDadosBasicos($this->iIdUsuario);
    			$this->sNome = $aDados["name"];
    			$this->sSexo = $aDados["gender"];
    			$this->dDataNascimento = strtotime($aDados["birthday"]);
    			$this->dDataCadastro = strtotime($aDados["dt_registration"]);
    			$this->sEmail = $aDados["email"];
    			$this->sTipo = $aDados["type"];
    			$this->sCPF = $aDados["cpf"];
    			$this->sCNPJ = $aDados["cnpj"];
    			$this->sCompanyName = $aDados["company_name"];
    			// Endereço
    			$this->sIdCidade = $aDados["id_city"];
    			$this->sEndereco = $aDados["address"];
    			$this->sCEP = $aDados["zip"];
    			
		  }
		}





		/**
			* Método bloqueia()
			*
			*/
		public function bloqueia() {
				if ($this->umPerfilBD->bloqueiaUsuario($this->getId())) {
					return true;
				}
				else {
					return false;
				}
		}





		/**
			* Método preparaParaRecuperacaoSenha()
			*
			*/
		public function preparaParaRecuperacaoSenha($sChave,$sEmailInformado) {
				if ($this->umPerfilBD->registraChaveRecuperacaoSenha($this->getId(),$sChave,$sEmailInformado)) {
					return true;
				}
				else {
					return false;
				}
		}





		/**
		 * Método showBadgeWebadmin()
		 * 
		 * Verifica se existem ocorrências que necessitam da atenção do usuário como webadmin
		 *
		 */
		public function showBadgeWebadmin() {
			$iQtdActions = $this->umPerfilBD->getWebAdminActions($this->iIdUsuario);
			return $iQtdActions;
		}







		/**
		 * Método loadAllData()
		 *
		 */
		public function loadAllData() {
		    // Endereços
		    $this->aEnderecos = $this->umPerfilBD->dadosEnderecos($this->iIdUsuario);
		    $this->aTelefones = $this->umPerfilBD->dadosTelefones($this->iIdUsuario);
		    // Dados básicos
		    $this->dadosBasicos();
		}
		






		/**
		 * Método gravaEndereco()
		 *
		 */
		public function gravaEnderecos() {
			$this->umPerfilBD->gravaEnderecosBanco($this->getId(),$this->aEnderecos);
		}
		
		
		
		
		
		
		
		/**
		 * Método gravaTelefones()
		 *
		 */
		public function gravaTelefones() {
			$this->umPerfilBD->gravaTelefonesBanco($this->getId(),$this->aTelefones);
		}
		




		/**
			* Método salvarDados()
			*
			*/
		public function salvarDados() {
		    $aEndereco = $this->getEndereco("residencial");
		    $aTelefones = $this->getTelefones();
		    $dDataRegistro = $this->getDataCadastro();
		    if (empty($dDataRegistro)) {
            $dDataRegistro = time();
        }
        if (empty($this->bAtivo)) {
            $sStatus = "aguardando confirmacao";
        }
        else {
            if ($this->bAtivo) {
                $sStatus = "ativo";
            }
            else {
                $sStatus = "bloqueado";
            }
        }
        $dados = array (
					"id_usuario"=>$this->iIdUsuario,
					"user"=>$this->sUsuario,
					"name"=>$this->sNome,
					"gender"=>$this->sSexo,
					"birthday"=>$this->dDataNascimento,
					"password"=>$this->sSenha,
					"id_perfil"=>$this->aIdsPerfis,
					"email"=>$this->sEmail,
				  "type"=>$this->sTipo,
				    "cpf"=>$this->sCPF,
				    "phone_home"=>$aTelefones[0],
				    "phone_business"=>$aTelefones[1],
				    "phone_cell"=>$aTelefones[2],
				    "zip"=>$this->sCEP,
				    "address"=>$this->sEndereco,
				    "id_city"=>$this->sIdCidade,
				    "dt_registration"=>$dDataRegistro,
				    "status"=>$sStatus,
				    "cnpj"=>$this->sCNPJ,
				    "activation_id"=>$this->sActivationId,
				    "company_name"=>$this->sCompanyName,
				);
//print("CPF : ".$this->sCPF."<BR>"); print("<pre>"); print_r($dados);
				$this->iIdUsuario = $this->umPerfilBD->save($dados);
				return $this->iIdUsuario;
		}





















		
	}
