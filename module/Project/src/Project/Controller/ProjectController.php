<?php
namespace Project\Controller;

    use Zend\Mvc\Controller\AbstractActionController;
    use Zend\View\Model\ViewModel;
    use Zend\Session\Container;
    use Zend\Mail;
    use Zend\Mail\Transport\Smtp as SmtpTransport;
    use Zend\Mail\Transport\SmtpOptions;
    use Zend\Mime\Message as MimeMessage;
    use Zend\Mime\Part as MimePart;
    use Zend\Mime\Mime;
    use Application\Model\Seguranca;
    use Application\Model\Perfil;
    use Application\Model\Formulario;
    use Application\Model\Project;
    use Application\Model\ProjectList;
    use Application\Model\Contribution;
    use Application\Model\City;
    
class ProjectController extends AbstractActionController {

	protected $oServiceManager;
	protected $oSessionManager;
	protected $oContainer;
	protected $oSeguranca;
	protected $oRenderer;
	protected $config;

	// Métodos relacionados a saídas de view
	
	private function init() {
    	$this->oServiceManager = $this->getServiceLocator();
    	$this->config = $this->oServiceManager->get("config");	// Captura o config setado em global|local
    	$this->oSessionManager = $this->oServiceManager->get('Zend\Session\SessionManager');
    	$this->oContainer = new Container("cfc",$this->oSessionManager); // Cria o container, usando o sessionmanager criado no Module.php
    	$this->oSeguranca = new Seguranca($this->oServiceManager); // Instancia o gerenciador de segurança
    	$this->oRenderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
	}

	
	
	
	/* registra os  Getters and Setters */
	// Retorna (GET) oContainer
	public function getContainer() {
		  return $this->oContainer;
	}

	
	
	
	/* registra os  Getters and Setters */
	// Retorna (GET) oRenderer
	public function getRenderer() {
		  return $this->oRenderer;
	}



	/*
	 * Método mostra os detalhes do projeto
	*/
	public function detalhesAction()
	{
		$this->init();
		$this->layout("v1/layout/moldura");
		$this->layout()->setVariable("iIdUsuarioLogadoSite",$this->oContainer->iIdUsuarioSiteLogado);
		$this->layout()->titleType = "APPEND";
			
		// Código do projeto
		$iProjectId = $this->params()->fromRoute('id', '');
		
		// Vars
		$bProjectContributor = false;
	
		if (!empty($iProjectId)) {
			$oUser = new Perfil($this->oServiceManager,$this->oContainer->iIdUsuarioSiteLogado);
			$oProject = new Project($this->oServiceManager,$iProjectId);
      $iStatus = $oProject->getProperty("id_status");
      
      if (($iStatus==6) || (in_array(3,$oUser->getIdsPerfis())) || ($oProject->getProperty("id_user")==$oUser->getId()) ) {
    			$oProject->increaseViewCount();
    			$aContributors = $oProject->getAllContributors();
    			$oProjectOwner = new Perfil($this->oServiceManager,$oProject->getProperty("id_user"));
    			$oForm = new Formulario($this->oServiceManager);
    			$umViewModel = new ViewModel();
    			$umViewModel->setTemplate("v1/projeto/detalhes");
    			$umViewModel->setVariable("oProject", $oProject);
    			$umViewModel->setVariable("oProjectOwner", $oProjectOwner);
    			$umViewModel->setVariable("oForm", $oForm);
    			$umViewModel->setVariable("iIdUsuarioLogado",$this->oContainer->iIdUsuarioSiteLogado);
    			$fTotalAmount = $oProject->getTotalAmountCollected();
    			$umViewModel->setVariable("fTotalAmount",$fTotalAmount);
    			$umViewModel->setVariable("iCountContributor",count($aContributors));
    			$aActualGoal = $oProject->getActualGoal();
    			$umViewModel->setVariable("aActualGoal", $aActualGoal);
    			$fProgress = round($fTotalAmount*100/$aActualGoal["reference"]);
    			$umViewModel->setVariable("fProgress", $fProgress);
    			// Usuário logado já contribuiu com o projeto?
    			$bProjectContributor = $oUser->isProjectContributor($iProjectId);
    			$umViewModel->setVariable("bProjectContributor", $bProjectContributor);
    			$umViewModel->setVariable("iIdUsuarioLogado", $oUser->getId());
    			return $umViewModel;
      }
      else {
          return $this->redirect()->toURL($this->oRenderer->basePath('/info/sem-acesso'));
      }
		}
		else {
			return $this->redirect()->toURL($this->oRenderer->basePath('/info/sem-codigo'));
		}
	}



	public function assinantesAction() {
		$this->init();
		$iProject = $this->params()->fromRoute('id', 0);
		$oProject = new Project($this->oServiceManager,$iProject);
		$umViewModel = new ViewModel();
		$umViewModel->setTemplate("projeto/assinantes");
		$umViewModel->setVariable("oProject", $oProject);
		$umViewModel->setTerminal(true);
		return $umViewModel;
	}

	
	
	



	public function cancelaContribuicaoDiretoAction() {
		$this->init();
		$this->layout("v1/layout/moldura");
		$this->layout()->setVariable("iIdUsuarioLogadoSite",$this->oContainer->iIdUsuarioSiteLogado);
		
		$sInfos = $this->params()->fromRoute('id', 0);
	    if (strpos($sInfos,"-")) {
	        $aTemp = explode("-", $sInfos);
	        $iProjectId = $aTemp[0];
	        $iUserIdRequest = $aTemp[1];
	        $iContributionId = $aTemp[2];
	    }
	    $oProject = new Project($this->oServiceManager,$iProjectId);
	    $oContribution = new Contribution($this->oServiceManager,$iContributionId);
	    if ($this->oContainer->iIdUsuarioSiteLogado==$iUserIdRequest) {
	        // 1o - cancela na MOIP
	        //$this->cancelMoipSignature();
	        $sURLRequest = $this->config["moip"]["url"]."subscriptions/".$oContribution->getId()."/cancel";
	        $aReturnValues = $this->sendMoipInstruction($sURLRequest, "PUT");
	        $jsonResponse = $aReturnValues["return"];
	        $jsonError = $aReturnValues["error"];
	        $aResponse = json_decode($jsonResponse,true);
	        //print_r($aResponse);
	        // 2o - cancela no sistema
	        $oContribution->cancelByUserRequest();
	        // 3o - Envia um email para o projetista e para a benfeitoria
	        // 4o - Cria o viewmodel
          $umViewModel = new ViewModel();
          $umViewModel->setTemplate("v1/projeto/contribuicao-cancelada-por-usuario");
          $umViewModel->setVariable("oProject", $oProject);
          $umViewModel->setVariable("oContribution", $oContribution);
          return $umViewModel;
	    }
	    else {
	        // Problemas de formação de URL
	        return $this->redirect()->toURL($this->oRenderer->basePath('/usuario/pagamentos'));
	         
	    }
	}
	
	
	



	/* Processo de contribuição : login (PASSO 0) */
	public function ajaxContribuicaoP0Action() {
	    $sEmail = null;
	    $this->init();
	    $iProjectId = $this->params()->fromRoute('id', 0);
	    if (strpos($iProjectId,"_")!==false) {
	        $aTemp = explode("_",$iProjectId);
	        $iProjectId = $aTemp[0];
	        $iUserId = $aTemp[1];
	        $oUser = new Perfil($this->oServiceManager,$iUserId);
	        $sEmail = $oUser->getEmail();
	    }
	    $umViewModel = new ViewModel();
	    $umViewModel->setTemplate("v1/projeto/ajax/contribuicao-login");
	    $umViewModel->setVariable("iProjectId",$iProjectId);
	    $umViewModel->setVariable("sEmail",$sEmail);
	    $umViewModel->setTerminal(true);
	    return $umViewModel;
	}
	
	
	
	/* Processo de contribuição : login (PASSO INTERMEDIÁRIO - NOVO CADASTRO PF) */
	public function ajaxContribuicaoNovoCadastroPFAction() {
	    $this->init();
	    $iProjectId = $this->params()->fromRoute('id', 0);
	    $umForm = new Formulario($this->oServiceManager);
	
	    $umViewModel = new ViewModel();
	    $umViewModel->setTemplate("v1/projeto/ajax/contribuicao-cadastro-pf");
	
	    $aCountries = $umForm->getCountries(true);
	    $sHTMLCountries = "";
	    foreach ($aCountries as $oCountry) {
	        $sHTMLCountries .= "<option value=\"".$oCountry["id"]."\">".$oCountry["name"]."</option>\n";
	    }
	    $umViewModel->setVariable("sHTMLCountries", $sHTMLCountries);
	    $umViewModel->setVariable("iProjectId",$iProjectId);
	    $umViewModel->setTerminal(true);
	    return $umViewModel;
	}
	
	
	
	/* Processo de contribuição : login (PASSO INTERMEDIÁRIO - NOVO CADASTRO PJ) */
	public function ajaxContribuicaoNovoCadastroPJAction() {
	    $this->init();
	    $iProjectId = $this->params()->fromRoute('id', 0);
	    $umViewModel = new ViewModel();
	    $umViewModel->setTemplate("v1/projeto/ajax/contribuicao-cadastro-pj");
	    $umViewModel->setVariable("iProjectId",$iProjectId);
	    $umViewModel->setTerminal(true);
	    return $umViewModel;
	}
	
	
	/* Processo de contribuição : ativação do cadastro (PASSO INTERMEDIARIO - VALIDACAO DO CADASTRO FEITO) */
	public function ajaxContribuicaoValidacaoCadastroAction() {
	    $this->init();
	    $iProjectId = $this->params()->fromRoute('id', 0);
	    $oProject = new Project($this->oServiceManager,$iProjectId);
	    $umViewModel = new ViewModel();
	    $umViewModel->setTemplate("v1/projeto/ajax/contribuicao-validacao");
	    $umViewModel->setVariable("oProject", $oProject);
	    $umViewModel->setVariable("iProjectId", $iProjectId);
	    $umViewModel->setTerminal(true);
	    return $umViewModel;
	}
	
	
	/* Processo de contribuição : escolha do valor (PASSO 1) */
	public function ajaxContribuicaoP1Action() {
	    $this->init();
	    $iIdContributionOld = null;
	    $iProjectId = $this->params()->fromRoute('id', 0);
	    if (strpos($iProjectId,"-")!==false) {
	        $aTemp = explode("-",$iProjectId);
	        $iProjectId = $aTemp[0];
	        $iIdContributionOld = $aTemp[1];
	    }
	    $oProject = new Project($this->oServiceManager,$iProjectId);
	    $umViewModel = new ViewModel();
	    $umViewModel->setTemplate("v1/projeto/ajax/contribuicao-escolhe-valor");
	    $umViewModel->setVariable("oProject", $oProject);
	    $umViewModel->setVariable("iIdContributionOld", $iIdContributionOld);
	    $umViewModel->setTerminal(true);
	    return $umViewModel;
	}
	
	
	/* Processo de contribuição : atualização do endereço de cobrança (PASSO 2) */
	public function ajaxContribuicaoP2Action() {
	    $this->init();
	    $sInfos = $this->params()->fromRoute('id', 0);
	    if (strpos($sInfos,"-")) {
	        $aTemp = explode("-", $sInfos);
	        $iProjectId = $aTemp[0];
	        $iContributionId = $aTemp[1];
	        $iIdContributionOld = @$aTemp[2];
	    }
	    $oProject = new Project($this->oServiceManager,$iProjectId);
	    $oUser = new Perfil($this->oServiceManager,$this->oContainer->iIdUsuarioSiteLogado);
	    $oContribution = new Contribution($this->oServiceManager,$iContributionId);
      $oContribution->setProperty("status", "passo 2 - aguardando preenchimento do endereço");
      $oContribution->saveData();
	    $umForm = new Formulario($this->oServiceManager);
	    $umViewModel = new ViewModel();
	    $umViewModel->setTemplate("v1/projeto/ajax/contribuicao-atualiza-endereco");
	    // Captura dados do endereço do usuário
	    $aEndereco = $oUser->getEndereco("residencial");
	    $iCityId = $aEndereco["id_city"];
	    // Captura o Estado
	    $oCity = new City($this->oServiceManager,$iCityId);
	    $aCountries = $umForm->getCountries(true);
	    $sHTMLCountries = "";
	    foreach ($aCountries as $oCountry) {
	        $sAdd = "";
	        if ($oCountry["id"]==$oCity->getCountry()) {
	            $sAdd = " selected";
	        }
	        $sHTMLCountries .= "<option value=\"".$oCountry["id"]."\"".$sAdd.">".$oCountry["name"]."</option>\n";
	    }
	    $umViewModel->setVariable("sHTMLCountries", $sHTMLCountries);
	    $aStates = $umForm->getCountryStates($oCity->getCountry());
	    $sHTMLStates = "";
	    foreach ($aStates as $oState) {
	        $sAdd = "";
	        if ($oState["name"]==$oCity->getState()) {
	            $sAdd = " selected";
	        }
	        $sHTMLStates .= "<option value=\"".$oState["name"]."\"".$sAdd.">".$oState["name"]."</option>\n";
	    }
	    $umViewModel->setVariable("sHTMLStates", $sHTMLStates);
	    $aCities = $umForm->getCitiesByState($oCity->getState());
	    $sHTMLCities = "";
	    foreach ($aCities as $aCity) {
	        $sAdd = "";
	        if ($aCity["id"]==$oCity->getCityId()) {
	            $sAdd = " selected";
	        }
	        $sHTMLCities .= "<option value=\"".$aCity["id"]."\"".$sAdd.">".$aCity["name"]."</option>\n";
	    }
	    $umViewModel->setVariable("sHTMLCities", $sHTMLCities);
	    $umViewModel->setVariable("oProject", $oProject);
	    $umViewModel->setVariable("oContribution", $oContribution);
	    $umViewModel->setVariable("iIdContributionOld", $iIdContributionOld);
	    $umViewModel->setVariable("oUser",  $oUser);
	    $umViewModel->setTerminal(true);
	    return $umViewModel;
	}
	
	
	
	/* Processo de contribuição : escolha da forma de pagamento (PASSO 3) */
	public function ajaxContribuicaoP3Action() {
	    $this->init();
	    $sInfos = $this->params()->fromRoute('id', 0);
	    if (strpos($sInfos,"-")) {
	        $aTemp = explode("-", $sInfos);
	        $iProjectId = $aTemp[0];
	        $iContributionId = $aTemp[1];
	        $iIdContributionOld = @$aTemp[2];
	    }
	    $oProject = new Project($this->oServiceManager,$iProjectId);
      // Registra o novo status do processo de contribuição
	    $oContribution = new Contribution($this->oServiceManager,$iContributionId);
      $oContribution->setProperty("status", "passo 3 - aguardando preenchimento dos dados de pagamento");
      $oContribution->saveData();
	    $umViewModel = new ViewModel();
	    $umViewModel->setTemplate("v1/projeto/ajax/contribuicao-forma-pgto");
	    $umViewModel->setVariable("oProject", $oProject);
	    $umViewModel->setVariable("oContribution", $oContribution);
	    $umViewModel->setVariable("iIdContributionOld", $iIdContributionOld);
	    $umViewModel->setVariable("oUser",  new Perfil($this->oServiceManager,$this->oContainer->iIdUsuarioSiteLogado));
	    $umViewModel->setTerminal(true);
	    return $umViewModel;
	}
	
	
	
	/* Processo de contribuição : resumo final (PASSO 4) */
	public function ajaxContribuicaoP4Action() {
	    $this->init();
	    $sInfos = $this->params()->fromRoute('id', 0);
	    if (strpos($sInfos,"-")) {
	        $aTemp = explode("-", $sInfos);
	        $iProjectId = $aTemp[0];
	        $iContributionId = $aTemp[1];
	    }
	    $oProject = new Project($this->oServiceManager,$iProjectId);
	    $oContribution = new Contribution($this->oServiceManager,$iContributionId);
	    $umViewModel = new ViewModel();
	    $umViewModel->setTemplate("v1/projeto/ajax/contribuicao-resumo-final");
	    $umViewModel->setVariable("oProject", $oProject);
	    $umViewModel->setVariable("oContribution", $oContribution);
	    $umViewModel->setTerminal(true);
	    return $umViewModel;
	}
	
	
	
	/* Processo de contribuição : resumo final (PASSO 4) */
	public function ajaxCancelaContribuicaoAction() {
	    $this->init();
	    $sInfos = $this->params()->fromRoute('id', 0);
	    if (strpos($sInfos,"-")) {
	        $aTemp = explode("-", $sInfos);
	        $iProjectId = $aTemp[0];
	        $iUserIdRequest = $aTemp[1];
	        $iContributionId = $aTemp[2];
	    }
	    $oProject = new Project($this->oServiceManager,$iProjectId);
	    $oContribution = new Contribution($this->oServiceManager,$iContributionId);
	    if ($this->oContainer->iIdUsuarioSiteLogado==$iUserIdRequest) {
	        // 1o - cancela na MOIP
	        $sURLRequest = $this->config["moip"]["url"]."subscriptions/".$oContribution->getId()."/cancel";
          $aReturnValues = $this->sendMoipInstruction($sURLRequest, "PUT");
          $jsonResponse = $aReturnValues["return"];
          $jsonError = $aReturnValues["error"];
          $aResponse = json_decode($jsonResponse,true);
//print_r($aResponse);          
	        // 2o - cancela no sistema
	        $oContribution->cancelByUserRequest();
	        // 3o - Envia um email para o projetista e para a benfeitoria
	    }
	    $umViewModel = new ViewModel();
	    $umViewModel->setTemplate("v1/projeto/ajax/contribuicao-cancelada-por-usuario");
	    $umViewModel->setVariable("oProject", $oProject);
	    $umViewModel->setVariable("oContribution", $oContribution);
	    $umViewModel->setTerminal(true);
	    return $umViewModel;
	}
	
	
	
	/* Inicia o processo de contribuição, criando um registro no sistema */
	public function ajaxIniciaContribuicaoAction() {
	    $this->init();
	    $request = $this->getRequest();
	    $iContributionId = null;
	    $sStatus = "nothing";
	    if ($request->isPost()) {
	        $iContributionOption = $request->getPost("contributionOption");
	        $iIdContributionOld = $request->getPost("contributionOld");
	        $aData["id_user"] = $this->oContainer->iIdUsuarioSiteLogado;
	        $aData["id_project"] = $request->getPost("projectId");
	        $aData["value_contribution"] = $request->getPost("contributionValue");
	        $aData["status"] = "aguardando a escolha do valor de contribuição";
	        $aData["start_value"] = 1;
	        // Cria a contribuicao
	        $oContribution = new Contribution($this->oServiceManager,null,$aData);
	        $oContribution->saveData();
	        $iContributionId = $oContribution->getId();
	        if (is_numeric($iContributionId)) {
	            $sStatus = "ok";
	        }
	    }
	    // Se existir uma contribuição sendo substituida, muda o status dela
	    if (!empty($iIdContributionOld)) {
	        if (is_numeric($iIdContributionOld)) {
	            $oContributionOld = new Contribution($this->oServiceManager,$iIdContributionOld);
	            $oContributionOld->setProperty("status", "substituida");
	            $oContributionOld->saveData();
	            // CANCELA NA MOIP A OCORRENCIA ANTIGA
	        }
	    }
	     
	    // Prepara a saída JSON
	    $response = $this->getResponse();
	    $response->setContent(\Zend\Json\Json::encode(array('retorno' => $sStatus, 'id_contribuicao' => $iContributionId)));
	    return $response;
	}
	
	
	
	/* Registra a atualização de endereço do usuário (Passo 2) */
	public function ajaxAtualizaEnderecoAction() {
	    $this->init();
	    $request = $this->getRequest();
	    $iContributionId = null;
	    $sStatus = "nothing";
	    $request = $this->getRequest();
	    if ($request->isPost()) {
	        $sRazaoSocial = null;
	        $sCNPJ = null;
	        $sCPF = null;
	        $iIdUsuario = $request->getPost("user_id");
	        $sTipo = $request->getPost("type");
	        $sDataNascimento = $request->getPost("birthday");
	        $aTemp = explode("/",$sDataNascimento);
	        $sDataNascimento = $aTemp[2]."-".$aTemp[1]."-".$aTemp[0];
	        $sIdPais = $request->getPost("country_id");
	        $sEndereco = $request->getPost("address");
	        $sNumero = $request->getPost("number");
	        $sComplemento = $request->getPost("complement");
	        $sBairro = $request->getPost("neighborhood");
	        $sCEP = $request->getPost("zipcode");
	        if ($sIdPais==5) {
	            $sEstado = $request->getPost("state");
	            $sCidade = $request->getPost("city_id");
	        }
	        else {
	            $sEstado = $request->getPost("new_state");
	            $sCidade = $request->getPost("new_city");
	        }
	        if ($sTipo=="PF") {
	            $sCPF = str_replace(array(".","-"),array("",""),$request->getPost("cpf"));
	        }
	        else {
	            $sCPF = str_replace(array(".","-"),array("",""),$request->getPost("cpf"));
	            $sCNPJ = $request->getPost("cnpj");
	            $sRazaoSocial = $request->getPost("company_name");
	        }
	        // Carrega o usuario
	        $umPerfil = new Perfil($this->oServiceManager,$iIdUsuario);
	        $umPerfil->setNome($umPerfil->getNome()); // Não pode mudar
	        $umPerfil->setSexo($umPerfil->getSexo()); // Não pode mudar
	        $umPerfil->setTipo($sTipo);
	        $umPerfil->setDataNascimento($sDataNascimento);
	        $umPerfil->setCompanyName($sRazaoSocial);
	        $umPerfil->setCPF($sCPF);
	        $umPerfil->setCNPJ($sCNPJ);
	
	        // Adiciona o endereco
	        $umPerfil->setEndereco("residencial",$sCEP,$sEndereco,$sCidade,$sNumero,$sComplemento,$sBairro);
	
	        // Adiciona os telefones
	        $umPerfil->adicionaTelefone("residencial", "home", null);
	        $umPerfil->adicionaTelefone("comercial", "business", null);
	        $umPerfil->adicionaTelefone("celular", "cell", null);
	        // Atualiza o cadastro no BD
	        $umPerfil->salvarDados();
	        $umPerfil->gravaEnderecos();
	        $sStatus = "ok";
	        
	        // Registra o novo status do processo de contribuição
	        //$oContribution = new Contribution($this->oServiceManager,$iContributionId);
	        //$oContribution->setProperty("status", "aguardando dados da forma de pagamento");
	        //$oContribution->saveData();
	         
	    }
	    // Prepara a saída JSON
	    $response = $this->getResponse();
	    $response->setContent(\Zend\Json\Json::encode(array('retorno' => $sStatus, 'id_contribuicao' => $iContributionId)));
	    return $response;
	}
	
	
	
	
	
	
	/* Faz contato com o gateway de pagamento da MOIP */
	public function ajaxGatewayPagamentoAction() {
	    $this->init();
	    $request = $this->getRequest();
	    $iContributionId = null;
	    $sStatus = "nothing";
	    $sMensagemErro = "";
	    if ($request->isPost()) {
	        $iContributionId = $request->getPost("contributionId");
	        $iIdContributionOld = $request->getPost("contributionOld");
	        $oContribution = new Contribution($this->oServiceManager,$iContributionId);
	        $iProjectId = $oContribution->getProperty("id_project");
	        $oProject = new Project($this->oServiceManager,$iProjectId);
	        // print("CONTRIBUTION ID: ".$iContributionId);
	        // print("ID USER: ".$oContribution->getProperty("id_user")); exit;
	        // Atribui os valores do cartão ao método de pagamento
	        $oContribution->setProperty("type", $request->getPost("type"));
	        $oContribution->setProperty("cc_flag", $request->getPost("cc_flag"));
	        $oContribution->setProperty("cc_number", $request->getPost("cc_number"));
	        $oContribution->setProperty("cc_holder_name", $request->getPost("cc_holder_name"));
	        $oContribution->setProperty("cc_expiration_date", $request->getPost("cc_expiration_date"));
	        $oContribution->setProperty("cc_security_number", $request->getPost("cc_security_number"));
	        $oContribution->setProperty("status", "dados do cartão atualizados. Aguardando comunicação com MOIP.");
	        // Atualiza a contribuição com os dados do cartão
	        $oContribution->saveData();

	        // Faz contato com o gateway de pagamento
	        
	        $oUser = new Perfil($this->oServiceManager,$oContribution->getProperty("id_user"));
	        // Dados complementares do usuário
	        $aTelefones = $oUser->getTelefones();
	        $sTelefoneResidencial = $aTelefones[0]["number"];
	        if (!empty($sTelefoneResidencial)) {
    	        if (strpos($sTelefoneResidencial," ")!==false) {
    	            $aTemp = explode(" ",$sTelefoneResidencial);
    	            $sTelefoneDDD = $aTemp[0];
    	            $sTelefoneNumber = $aTemp[1];
    	        }
    	        else {
    	            $sTelefoneDDD = "000";
    	        }
	        }
	        else {
	            $sTelefoneDDD = "000";
	            $sTelefoneNumber = "00000000";
	        }
	        $aEnderecos = $oUser->getEndereco("residencial");
	        $oCity = new City($this->oServiceManager,$aEnderecos["id_city"]);
	        $sMoipPlanId = "plano01";
	        
	        // Acrescenta identificador (prefixo) no código da assinatura
	        $env = getenv('APPLICATION_ENV');
	        $sCodePrefix = "";
// 	        switch ($env) {
// 	            case "andre"				:	// Local no mac
// 	                $sCodePrefix = "local-andre-";
// 	                break;
// 	            case "development"	:	// Digital Ocean (server andrepiacentini.com.br)
// 	                $sCodePrefix = "dev-";
//                   break;
// 	            case "production"		:	// Digital Ocean Producao
// 	            case "producao"		  :
// 	                $sCodePrefix = "prod-";
// 	                break;
// 	        }
	        $sCodePrefix = $this->config["moip"]["prefix_code"];
	        $sPaymentGatewayCode = $sCodePrefix.$oContribution->getId();
	        // Data de vencimento cartão - tratamento (MM/YY)
	        $iExpirationMonth =  substr($oContribution->getProperty("cc_expiration_date"),0,2);
	        $iExpirationYear = substr($oContribution->getProperty("cc_expiration_date"),3);
	        if (strlen($iExpirationYear)>2) {
	            $iExpirationYear = substr($oContribution->getProperty("cc_expiration_date"),5,2);
	        }
	        
	        $aPostParameters = array(
	            "code" => $sPaymentGatewayCode,
	            "amount" => number_format($oContribution->getProperty("value_contribution"),2,"",""),
	            "plan" => array(
	                "code" => $sMoipPlanId,
	            ),
	            "customer" => array(
	                "code" => $oUser->getId(),
	                "email" => $oUser->getEmail(),
	                "fullname" => $oUser->getNome(),
	                "cpf" => $oUser->getCPF(),
	                "phone_number" => $sTelefoneNumber,
	                "phone_area_code" => $sTelefoneDDD,
	                "birthdate_day" => date("d",strtotime($oUser->getDataNascimento())),
	                "birthdate_month" => date("m",strtotime($oUser->getDataNascimento())),
	                "birthdate_year" => date("Y",strtotime($oUser->getDataNascimento())),
	                "address" => array(
	                    "street" => substr($aEnderecos["address"],0,59),
	                    "number" => $aEnderecos["number"],
	                    "complement" => $aEnderecos["complement"],
	                    "district" => $aEnderecos["neighborhood"],
	                    "city" => $oCity->getCityName(),
	                    "state" => $oCity->getState(),
	                    "country" => substr($oCity->getCountryName(),0,3),
	                    "zipcode" => str_replace("-","",$aEnderecos["zip"]),
	                ),
	                "billing_info" => array(
	                    "credit_card" => array(
	                        "holder_name" => $oContribution->getProperty("cc_holder_name"),
	                        "number" => $oContribution->getProperty("cc_number"),
	                        "expiration_month" => $iExpirationMonth,
	                        "expiration_year" => $iExpirationYear,
	                    ),
	                ),
	            ),
	        );

	        
	        // --------------------------------------------------------------------------------------------------------
	        $sHTMLDescription = "";
        // 1o - verifica se o cliente existe
        $sURLRequest = $this->config["moip"]["url"]."customers/".($oUser->getId());
        $aReturnValues = $this->sendMoipInstruction($sURLRequest, "GET");
        $jsonResponse = $aReturnValues["return"];
        $jsonError = $aReturnValues["error"];
        $aResponse = json_decode($jsonResponse,true);
        if (is_array($aResponse)) {
            $sURLRequest = $this->config["moip"]["url"]."subscriptions?new_customer=false";
        }
        else {
            $sURLRequest = $this->config["moip"]["url"]."subscriptions?new_customer=true";
        }
        
        // 2o - cria assinatura (pode ser que crie o cliente também)
        $aReturnValues = $this->sendMoipInstruction($sURLRequest, "POST", json_encode($aPostParameters));
        $jsonResponse = $aReturnValues["return"];
        $jsonError = $aReturnValues["error"];
        $aResponse = json_decode($jsonResponse,true);

//print_r($aResponse); exit;        
        

	        switch (@$aResponse["status"]) {
	            case "TRIAL":
	            case "CREATED":
	            case "ACTIVE":
	                if (mb_strtolower(@$aResponse["invoice"]["status"]["description"])=="atrasada") {
	                    // HTML de Saida
	                    $sHTMLDescription = "<br/><strong>A sua cobrança está atrasada devido a algum problema com seu cartão de crédito. Deixaremos você avisado.";
	                    $sHTMLDescription .= "</strong>";
    	                $oContribution->setProperty("status", "cobrança atrasada - invoice ID: ".@$aResponse["invoice"]["id"]);
	                    $oContribution->saveData();
	                    // Caso tenha sido uma alteração de informações da contribuição (valor, endereço, forma pgto, etc), a contribuição ativa será desabilitada e a que acabou de ser criada será marcada como ativa
	                    if (!empty($iIdContributionOld)) {
	                        if (is_numeric($iIdContributionOld)) {
	                            $oContributionOld = new Contribution($this->oServiceManager,$iIdContributionOld);
	                            $oContributionOld->setProperty("status", "substituida");
	                            $oContributionOld->saveData();
	                            // CANCELA NA MOIP A OCORRENCIA ANTIGA
	                        }
	                    }
	                    $sStatus = "error-delay";
	                    break;
                  }
                  else {
    	                // Atualiza o status da transação
    	                $oContribution->setProperty("status", "cobrado e recorrencia autorizada");
    	                // Inutiliza o número do cartão de crédito
    	                $sCCNumber = $oContribution->getProperty("cc_number");
    	                $sCCNumber = "**** **** **** ".substr($sCCNumber,-4);
    	                $oContribution->setProperty("cc_number", $sCCNumber);
    	                $oContribution->setProperty("payment_gateway_code", $sPaymentGatewayCode);
    	                $oContribution->saveData();
    	                // Caso tenha sido uma alteração de informações da contribuição (valor, endereço, forma pgto, etc), a contribuição ativa será desabilitada e a que acabou de ser criada será marcada como ativa
    	                if (!empty($iIdContributionOld)) {
    	                    if (is_numeric($iIdContributionOld)) {
    	                        $oContributionOld = new Contribution($this->oServiceManager,$iIdContributionOld);
    	                        $oContributionOld->setProperty("status", "substituida");
    	                        $oContributionOld->saveData();
    	                        // CANCELA NA MOIP A OCORRENCIA ANTIGA
    	                    }
    	                }
    	                $sStatus = "ok";
    	                break;
                  }
	            case "OVERDUE":
	                // Atualiza o status da transação
	                $oContribution->setProperty("status", "atrasada - fluxo de retentativa iniciado");
	                // Inutiliza o número do cartão de crédito
	                $sCCNumber = $oContribution->getProperty("cc_number");
	                $sCCNumber = "**** **** **** ".substr($sCCNumber,-4);
	                $oContribution->setProperty("cc_number", $sCCNumber);
	                $oContribution->setProperty("payment_gateway_code", $sPaymentGatewayCode);
	                $oContribution->saveData();
	                // Caso tenha sido uma alteração de informações da contribuição (valor, endereço, forma pgto, etc), a contribuição ativa será desabilitada e a que acabou de ser criada será marcada como ativa
	                if (!empty($iIdContributionOld)) {
	                    if (is_numeric($iIdContributionOld)) {
	                        $oContributionOld = new Contribution($this->oServiceManager,$iIdContributionOld);
	                        $oContributionOld->setProperty("status", "substituida");
	                        $oContributionOld->saveData();
	                        // CANCELA NA MOIP A OCORRENCIA ANTIGA
	                    }
	                }
	                $sStatus = "ok";
	                break;
              default:   // Error
	                // Atualiza o status da transação
	                $sDescription = $aResponse["message"].". Relação de problemas encontrados: ";
	                foreach ($aResponse["errors"] as $aError) {
	                    $sDescription .= "[".$aError["code"]."] - ".$aError["description"].",";
	                }
	                $sDescription = substr($sDescription,0,-1);
	                // HTML de Saida
	                $sHTMLDescription = "<br/><strong>".$aResponse["message"]."</strong><br/>Relação de problemas encontrados:<br/><strong>";
	                foreach ($aResponse["errors"] as $aError) {
	                    $sHTMLDescription .= " - ".utf8_decode($aError["description"])." [".$aError["code"]."]<br/>";
	                }
	                $sHTMLDescription .= "</strong>";
	                $oContribution->setProperty("status", "erro [".@$aResponse["message"]."] - ".$sDescription);
	                $oContribution->saveData();
	                // Caso tenha sido uma alteração de informações da contribuição (valor, endereço, forma pgto, etc), a contribuição ativa será desabilitada e a que acabou de ser criada será marcada como ativa
	                if (!empty($iIdContributionOld)) {
	                    if (is_numeric($iIdContributionOld)) {
	                        $oContributionOld = new Contribution($this->oServiceManager,$iIdContributionOld);
	                        $oContributionOld->setProperty("status", "substituida");
	                        $oContributionOld->saveData();
	                        // CANCELA NA MOIP A OCORRENCIA ANTIGA
	                    }
	                }
	                $sStatus = "error";
	                break;
     	      }
	    }
	    // Envia email se foi com sucesso
	    if ($sStatus=="ok") {
	        $sNome = $oUser->getNome();
	        $sEmail = $oUser->getEmail();
	        // Encaminha um email
						$options = new SmtpOptions( array(
																			"name" => "mandrillapp",
																			"host" => "smtp.mandrillapp.com",
																			"port" => 587,
																			"connection_class" => "plain",
																			"connection_config" => array( "username" => "andre.piacentini@gmail.com",
																			"password" => "LrWNuu9ZlJObxRWsT-J63Q","ssl" => "tls" )
																			) );
	        	        
	            $body = "
                Olá ".$sNome.",<br />
                <br />
                A partir de agora você está contribuindo com a campanha ".$oProject->getProperty("title").". Agradecemos e admiramos a sua participação, pois além de contribuir para que essa iniciativa seja sustentável e contínua, você está fazendo parte de uma rede que gera e compartilha valor! :)<br />
                <br />
                Sempre que a sua contribuição mensal for confirmada você será notificado, afinal queremos sempre te deixar informado sobre cada passo dado.<br />
                <br />
                Para acompanhar a campanha é só entrar no <a href=\"".$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"]."/projeto/detalhes/".$oProject->getId()."\">".$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"]."/projeto/detalhes/".$oProject->getId()."</a>. E, se possível, não esqueça de divulgá-la, afinal acreditamos em relações ganha-ganha e, quanto mais assinantes, maior o impacto positivo!<br />
                <br />
                Caso você deseje alterar o valor da sua contribuição mensal ou até mesmo cancelar sua assinatura, entre em <a href=\"".$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"]."/usuario/pagamentos\">".$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"]."/usuario/pagamentos</a>.<br />
                 <br />
                Obrigado!  <br />
                Equipe Recorrente<br />
	            ";
	        
	        $htmlPart = new MimePart($body);
	        $htmlPart->type = "text/html";
	         
	        $body = new MimeMessage();
	        $body->setParts(array($htmlPart));
	        
	        $mail = new Mail\Message();
	        $mail->setBody($body);
	        $mail->setFrom('recorrente@benfeitoria.com','Recorrente Benfeitoria');
	        $mail->addReplyTo('recorrente@benfeitoria.com','Recorrente Benfeitoria');
	        $mail->addTo($sEmail, $sNome);
	        $mail->setSubject("Obrigado por apoiar uma campanha Recorrente");
	         
	        $transport = new SmtpTransport();
	        $transport->setOptions( $options );
	        $transport->send($mail);
	         
	    }
	    // Prepara a saída JSON
	    $response = $this->getResponse();
	    $response->setContent(\Zend\Json\Json::encode(array('retorno' => $sStatus, 'mensagem_erro' => $sHTMLDescription)));
	    return $response;
	}

	
	

	// Envia instruções para a moip
	private function sendMoipInstruction($url, $metodo, $dados = null) {
	    $sKey = $this->config["moip"]["key"];
	    $sHash = $this->config["moip"]["hash"];
	    $header[] = "Request Method: $metodo";
	    $header[] = "Authorization: Basic " . base64_encode($sKey.':'.$sHash);
//	    $header[] = "Authorization: 95857a59fbecbca096b43e5cfae4bd08";
	    $header[] = "Content-Type: application/json";
	    $header[] = "Accept: application/json";
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL,$url);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($curl, CURLOPT_USERPWD, $sKey.':'.$sHash);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0");
	    if (mb_strtolower($metodo)=="post") {
	        curl_setopt($curl, CURLOPT_POST, true);
	        if (!empty($dados)) {
	            curl_setopt($curl, CURLOPT_POSTFIELDS, $dados);
	        }
	    }
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    $ret = curl_exec($curl);
	    $err = curl_error($curl);
	    curl_close($curl);
	    return array('return'=>$ret,'error'=>$err);
	}
	
}
