<?php
namespace Webadmin\Controller;

use Webadmin\Model\SegurancaWebAdmin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Session\Config\StandardConfig;
use Zend\Session\Container;

use Webadmin\Model\WebAdminBD;
use Application\Model\Project;
use Application\Model\Formulario;

use Zend\Mail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime;


//use Zend\Http\Client\Adapter\Curl;
//use Zend\Http\Request;
//use Zend\Http\Client;

	use Application\Model\Perfil;

class WebadminController extends AbstractActionController {

	protected $umaTable;
	protected $umServiceManager;
	protected $umSessionManager;
	protected $umContainer;
	protected $umaSeguranca;
	protected $umRenderer;
	protected $config;

	// Métodos relacionados a saídas de view
	
	private function init() {
		$this->umServiceManager = $this->getServiceLocator();
		$this->config = $this->umServiceManager->get("config");	// Captura o config setado em global|local
		$this->umSessionManager = $this->umServiceManager->get('Zend\Session\SessionManager');
		$this->umContainer = new Container("cfc_webadmin",$this->umSessionManager); // Cria o container, usando o sessionmanager criado no Module.php
		$this->umaSeguranca = new SegurancaWebAdmin($this->umServiceManager,$this->umRenderer); // Instancia o gerenciador de segurança
    $this->umRenderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
	}

	/* registra os  Getters and Setters */
	// Retorna (GET) umContainer
	public function getContainer() {
		return $this->umContainer;
	}

	/* registra os  Getters and Setters */
	// Retorna (GET) umRenderer
	public function getRenderer() {
		return $this->umRenderer;
	}
	
	
	
	private function sessaoAtiva() {
	    $this->umaSeguranca->sessaoAtiva($this);
	}
	
	
	
	
	
	/* Página inicial */
  public function indexAction() {
  	// Init
  	$this->init();
  	$this->sessaoAtiva();
			// Layout  	
			$this->layout("layout/webadmin");
			$umPerfil = new Perfil($this->umServiceManager,null,$this->umContainer->sUsuarioAdminLogado);
			$this->layout()->sLoginUsuario = ucfirst($umPerfil->getNome());	// Seta uma variável no layout diretamente.
			$this->layout()->sTempoSessaoTela = $this->config["session"]["expira-tela"];
			// Adapter
			$dbAdapter = $this->umServiceManager->get("Zend\Db\Adapter\Adapter");
	
			// View de saída
	  	$umViewModel = new ViewModel();
	  	$umViewModel->setVariable("sValor",$this->umContainer->sUsuarioAdminLogado);
	  	return $umViewModel;
  }


  
  
  
  
  
  
	/* Tela de login */
  public function loginAction() {
  	// Init
  	$this->init();
		$this->layout('layout/webadmin-login');
		$config = $this->umServiceManager->get("config");	// Captura o config setado em global|local
		// Captura dados passados por get (na URL)
		$sIdErro = $this->params()->fromRoute('id', 0);
		if (!empty($sIdErro)) {
			$b = $this->config['mensagens'][$sIdErro];
		}
		else {
			$sMensagemErro = "";
		}
		// View de saída
  	$umViewModel = new ViewModel();
    if (!isset($sMensagemErro)) {
        $sMensagemErro = "";
    };
  	$umViewModel->setVariable("sMensagemErro",$sMensagemErro);
  	return $umViewModel;
	}
	
	
	
	
	
	
	
	/* Recebe a submissão do formulário de login */
	public function validaLoginAction() {
		// Init
		$this->init();
    $request = $this->getRequest();
    if ($request->isPost()) {
			$sUsuario = $request->getPost('usuario');
			$sSenha = $request->getPost('senha');
			if ($this->umaSeguranca->validaLogin($sUsuario,$sSenha)) {
				// Cria variável de sessão
				$this->umContainer->sUsuarioAdminLogado = $sUsuario;
				// Direciona para página inicial
				return $this->redirect()->toURL($this->umRenderer->basePath('/wbadmin'));
			}
			else {
				return $this->redirect()->toURL($this->umRenderer->basePath('/wbadmin/login/invalido'));
			}
		}
		else {
			// Direciona para tela de login, com mensagem de erro
			return $this->redirect()->toURL($this->umRenderer->basePath('/wbadmin/login/form-invalido'));
		}
	}
	
	
	
	
	
	
	
	/* Realiza o logout */
	public function logoutAction() {
		// Init
		$this->init();
		// Encerra a sessão para o usuário logado
		$this->umContainer->offsetUnset("sUsuarioAdminLogado");
		$this->umSessionManager->destroy();
		$sIdErro = $this->params()->fromRoute('id', 0);
		// Direciona para página inicial
		return $this->redirect()->toURL($this->umRenderer->basePath('/wbadmin/login/' . $sIdErro));
	}


  
    
    
    
    
 	/* 
 	
 		projetos - Moderação de projetos enviados pelo portal
 	
 	*/
  public function projetosModeracaoAction() {
  	// Init
  	$this->init();
  	$sMensagemErro = "";
  	
  	// Protege o acesso
  	$this->sessaoAtiva();

    // template
    $this->layout('layout/webadmin');
    
    // Teve algum erro?
    $sCodigoErro = $this->params()->fromRoute('id', null);
    if (!empty($sCodigoErro)) {
        switch ($sCodigoErro) {
        	case "erro-1"  :   $sMensagemErro = "Informe um código de projeto";
        	                   break;
        }
    }

    // variaveis do topo
    $umPerfil = new Perfil($this->umServiceManager,null,$this->umContainer->sUsuarioAdminLogado);
    $this->layout()->sLoginUsuario = ucfirst($umPerfil->getNome());	// Seta uma variável no layout diretamente.
    $this->layout()->sTempoSessaoTela = $this->config["session"]["expira-tela"];

    $config = $this->umServiceManager->get("config");	// Captura o config setado em global|local

    
    // opcoes do select perfil de acesso
    $oWebAdminBD = new WebAdminBD($this->umServiceManager->get("Zend\Db\Adapter\Adapter"));
    $aIdsProjetos = $oWebAdminBD->retornaTodosProjetosNaoPublicados();

    //View de saída
  	$umViewModel = new ViewModel();
  	$umViewModel->setVariable("oSm",$this->umServiceManager);
  	$umViewModel->setVariable("aIdsProjetos",$aIdsProjetos);
  	$umViewModel->setVariable("sMensagemErro",$sMensagemErro);
  	 
  	return $umViewModel;
	}







	/*
	
	projetos - Excluir projeto escolhido (tela de confirmação)
	
	*/
	public function projetosExcluirAction() {
		// Init
		$this->init();
		$sMensagemErro = "";
		 
		// Protege o acesso
		$this->sessaoAtiva();
	
		// template
		$this->layout('layout/webadmin');
	
		// Teve algum erro?
		$iProjectId = $this->params()->fromRoute('id', null);
		if (empty($iProjectId)) {
        print("Erro: URL mal formatada.");
        exit;
		}
	
		// Instâncias
		$oWebAdminBD = new WebAdminBD($this->umServiceManager->get("Zend\Db\Adapter\Adapter"));
		$umPerfil = new Perfil($this->umServiceManager,null,$this->umContainer->sUsuarioAdminLogado);
		$oProject = new Project($this->umServiceManager,$iProjectId);
		
		// variaveis do topo
		$this->layout()->sLoginUsuario = ucfirst($umPerfil->getNome());	// Seta uma variável no layout diretamente.
		$this->layout()->sTempoSessaoTela = $this->config["session"]["expira-tela"];
	
		$config = $this->umServiceManager->get("config");	// Captura o config setado em global|local
	
	
		
    //View de saída
  	$umViewModel = new ViewModel();
  	$umViewModel->setVariable("aSm",$this->umServiceManager);
  	$umViewModel->setVariable("oProject",$oProject);
  	$umViewModel->setVariable("sMensagemErro",$sMensagemErro);
  	 
  	return $umViewModel;
	}









	/*
	
	projetos - Excluir projeto escolhido (tela de confirmação)
	
	*/
	public function projetosExclusaoConfirmadaAction() {
		// Init
		$this->init();
		$sMensagemErro = "";
			
		// Protege o acesso
		$this->sessaoAtiva();
		
		$iProjectId = $this->params()->fromRoute('id', null);
		$oProject = new Project($this->umServiceManager,$iProjectId);
		$aProjectData = $oProject->getAllProperties();
		$aProjectData["main_phone"] = null;
		$aProjectData["other_phone"] = null;
		$aProjectData["company_type"] = null;
		$aProjectData["categories"] = null;
		$aProjectData["goal_money"] = null;
		$aProjectData["link_title"] = null;
		
    $aProjectData["id_status"] = 9;
    // Prepara para gravação
    $oProject = new Project($this->umServiceManager,$iProjectId,$aProjectData);
    $oProject->saveData();
    return $this->redirect()->toURL($this->umRenderer->basePath('/wbadmin/projetos-moderacao'));
    exit;   
	}
	



	/*
	
	projetos - Detalhes para edição
	
	*/
	public function projetosDetalhesAction() {
		// Init
		$this->init();
		// Protege o acesso
		$this->sessaoAtiva();
	
		// template
		$this->layout('layout/webadmin');
		
		$iProjectId = $this->params()->fromRoute('id', null);

		if (!empty($iProjectId)) {
    		// variaveis do topo
    		$umPerfil = new Perfil($this->umServiceManager,null,$this->umContainer->sUsuarioAdminLogado);
    		$this->layout()->sLoginUsuario = ucfirst($umPerfil->getNome());	// Seta uma variável no layout diretamente.
    		$this->layout()->sTempoSessaoTela = $this->config["session"]["expira-tela"];
    	
    		$config = $this->umServiceManager->get("config");	// Captura o config setado em global|local
    		
    		$umProjeto = new Project($this->umServiceManager,$iProjectId);
    		$umAutor = new Perfil($this->umServiceManager,$umProjeto->getProperty("id_user"));
    		$umFormulario = new Formulario($this->umServiceManager);
    		
    		$aCompanyTypes = $umFormulario->getCompanyTypes();
    		$aCategories = $umFormulario->getCategories();
    	
    	
    		//View de saída
    		$umViewModel = new ViewModel();
    		// Seta o template conforme o status
    		$iIdStatus = $umProjeto->getProperty("id_status");
    		switch ($iIdStatus) {
    			case 1   :   
    			case 2   :   
    			case 3   :   $umViewModel->setTemplate("webadmin/pre-projeto");
    			             break;
    		}
    		$umViewModel->setVariable("aSm",$this->umServiceManager);
    		$umViewModel->setVariable("aCompanyTypes",$aCompanyTypes);
    		$umViewModel->setVariable("aCategories",$aCategories);
    		$umViewModel->setVariable("umProjeto",$umProjeto);
    		$umViewModel->setVariable("umAutor",$umAutor);
    		$umViewModel->setVariable("umPerfil",$umPerfil);
    		
    		return $umViewModel;
		}
		else {
		    return $this->redirect()->toURL($this->umRenderer->basePath('/wbadmin/projetos-moderacao/erro-1'));
		}
	}







	/*
	
	projetos - Detalhes para edição
	
	*/
	public function projetosAtualizaDadosAction() {
		// Init
		$this->init();
		// Protege o acesso
		$this->sessaoAtiva();
		$request = $this->getRequest();
		if ($request->isPost()) {
		    $aProjectData = array();
		    $aProjectData["id_user"] = $this->umContainer->iIdUsuarioSiteLogado;
		    $aProjectData["title"] = $request->getPost("nome_projeto");
		    $aProjectData["organization"] = $request->getPost("organizacao");
		    $aProjectData["email"] = $request->getPost("email");
		    $aProjectData["main_phone"] = $request->getPost("telefone_principal");
		    $aProjectData["other_phone"] = $request->getPost("telefone_secundario");
		    $aProjectData["company_type"] = $request->getPost("tipo_juridico");
		    $aProjectData["how_find_us"] = $request->getPost("como_chegou_benfeitoria");
		    $aProjectData["about_you"] = $request->getPost("sobre_voce");
		    $aProjectData["project_abstract"] = $request->getPost("resumo_projeto");
		    $aProjectData["operating_region"] = $request->getPost("regiao_atuacao");
		    $aProjectData["categories"] = $request->getPost("temas");
		    $aProjectData["fundation_year"] = $request->getPost("ano_fundacao");
		    $aProjectData["employees_number"] = $request->getPost("num_funcionarios");
		    $aProjectData["volunteers_number"] = $request->getPost("num_voluntarios");
		    $aProjectData["people_impacted_number"] = $request->getPost("num_pessoas_impactadas");
		    $aProjectData["about_project"] = $request->getPost("mais_sobre");
		    $aProjectData["how_long"] = $request->getPost("ha_quanto_tempo");
		    $aProjectData["average_monthly_spending"] = $request->getPost("gasto_mensal");
		    $aProjectData["resources_from"] = $request->getPost("recursos_externos");
		    $aProjectData["goal_money"] = $request->getPost("meta_valor");
		    $aProjectData["goal_objective_for_money"] = $request->getPost("meta_objetivos_valor");
		    $aProjectData["goal_reward"] = $request->getPost("meta_recompensa");
		    $aProjectData["marketing"] = $request->getPost("como_sera_arrecadacao");
		    $aProjectData["link_title"] = $request->getPost("link_titulo");
		    $aProjectData["link_url"] = $request->getPost("link_url");
		    $aProjectData["dt_registration"] = time();
		    $aProjectData["dt_update"] = null;
		    $sOperation = $request->getPost("operacao");
		    $iProjectId = $request->getPost("project_id");

//print($sOperation); exit;
		    switch ($sOperation) {
		    	case "gravar"            :   $aProjectData["id_status"] = $request->getPost("id_status");
                                    	 // Prepara para gravação
                                    	 $oProject = new Project($this->umServiceManager,$iProjectId,$aProjectData);
                                    	 $oProject->saveData();
                                    	 return $this->redirect()->toURL($this->umRenderer->basePath('/wbadmin/projetos-detalhes/' . $iProjectId));
                            		    	 exit;   
		    	case "gravar_publicar"   :   $aProjectData["id_status"] = 5;
                                    	 // Prepara para gravação
                                    	 $oProject = new Project($this->umServiceManager,$iProjectId,$aProjectData);
                                    	 $oProject->saveData();
                                    	 return $this->redirect()->toURL($this->umRenderer->basePath('/wbadmin/projetos-detalhes/' . $iProjectId));
                            		    	 exit;   
		    }
		    
		}
		
	}



	
	
	public function carregaMensagemPadraoAction() {
	    // Init
	    $this->init();
	    $sMensagemErro = "";
	    	
	    // Protege o acesso
	    $this->sessaoAtiva();
	    
	    // Teve algum erro?
	    $sTipoMensagem = $this->params()->fromRoute('id', null);
	    
	    // Instâncias
	    $oWebAdminBD = new WebAdminBD($this->umServiceManager->get("Zend\Db\Adapter\Adapter"));
	    
	    // Conteudo da mensagem padrao
	    $sMessage = $oWebAdminBD->getDefaultMessageByType($sTipoMensagem);
		  $oUser = new Perfil($this->umServiceManager,null,$this->umContainer->sUsuarioAdminLogado);
	    	    
	    //View de saída
	    $umViewModel = new ViewModel();
	    $umViewModel->setTerminal(true);
	    $umViewModel->setTemplate("webadmin/mensagem-padrao");
	    $umViewModel->setVariable("sMessage",nl2br(str_replace("{NOME}",$oUser->getNome(),$sMessage)));
	    
	    return $umViewModel;
	}





 	/* 
 		valida novo usuario
 	*/
  public function validaUsuarioAction() {

			// Init, ver com o Andre como desabilitar o view para este metodo
  		$this->init();

	    $request = $this->getRequest();



	    $saida = 0;

	    if ($request->isPost()) {
					$usuario = $request->getPost('usuario');
			    $oManutencaoBD = new WebAdminBD($this->umServiceManager->get("Zend\Db\Adapter\Adapter"));
					$temp = $oManutencaoBD->localizaUsuario($usuario);
			    if ($temp >= 1) {
				    	$saida = 1;
			    };
			};

			//$result = new ViewModel();
	    //$result->setTerminal(true);


	  	echo $temp; 
	  	exit;
  }


  
    /*
     * Método formEnviaMensagemAction
     * 
     * Encaminha um email contendo o link para leitura da mensagem
     */
  public function formEnviaMensagemAction() {
  		$this->init();

	    $request = $this->getRequest();
	    if ($request->isPost()) {
	        $sType = $request->getPost("type_message");
	        $sMessage = $request->getPost("message");
	        $iUserIdFrom = $request->getPost("id_from");
	        $oUserFrom = new Perfil($this->umServiceManager,$iUserIdFrom);
	        $iUserIdTo = $request->getPost("id_to");
	        $oUserTo = new Perfil($this->umServiceManager,$iUserIdTo);
	        $iProjectId = $request->getPost("project_id");
	        $oProject = new Project($this->umServiceManager,$iProjectId);
	        
	        // Instancia
	        $oWebAdminBD = new WebAdminBD($this->umServiceManager->get("Zend\Db\Adapter\Adapter"));
	        
          // Encaminha um email
						$options = new SmtpOptions( array(
																			"name" => "mandrillapp",
																			"host" => "smtp.mandrillapp.com",
																			"port" => 587,
																			"connection_class" => "plain",
																			"connection_config" => array( "username" => "andre.piacentini@gmail.com",
																			"password" => "LrWNuu9ZlJObxRWsT-J63Q","ssl" => "tls" )
																			) );
	                  
          // Cria a mensagem
          $sHashKey = uniqid("mesg");
          switch ($sType) {
          	case "duvidas"   : // Ação de dúvidas gerada pela benfeitoria
          	                   $sSubject = "Temos uma dúvida quanto a sua campanha";
          	                   break;
          	case "mudancas"   : // Ação de dúvidas gerada pela benfeitoria
          	                   $sSubject = "Estamos sugerindo uma alteração em sua campanha";
          	                   break;
          }
          $iMessageId = $oWebAdminBD->createMessageIntoDB($iUserIdFrom,$iUserIdTo,null,$iProjectId,$sSubject,$sMessage,$sHashKey);
          
          $sURLMensagem = $_SERVER["HTTP_HOST"].$this->config["path_logico"]."/usuario/lerMensagem/".$sHashKey;
          
          switch ($sType) {
          	case "duvidas"   : // Ação de dúvidas gerada pela benfeitoria
          	                   $body = "Olá ".$oUserTo->getPrimeiroNome()."!<br/><br/>Você recebeu uma nova mensagem referente a campanha <b>".$oProject->getProperty("title")."</b>. Para ter acesso ao conteúdo da mesma, siga o link:<br/><br/><a href=\"".$sURLMensagem."\">".$sURLMensagem."</a>";
          	                   $iProjectIdStatus = 2;
          	                   break;
          	case "mudancas"  : // Ação de dúvidas gerada pela benfeitoria
          	                   $body = "Olá ".$oUserTo->getPrimeiroNome()."!<br/><br/>Você recebeu uma nova mensagem referente ao seu projeto <b>".$oProject->getProperty("title")."</b>. Para ter acesso ao conteúdo da mesma, siga o link:<br/><br/><a href=\"".$sURLMensagem."\">".$sURLMensagem."</a>";
          	                   $iProjectIdStatus = 3;
          	                   break;
          }
           
          
          
          $htmlPart = new MimePart($body);
          $htmlPart->type = "text/html";
           
          $body = new MimeMessage();
          $body->setParts(array($htmlPart));
          
          $mail = new Mail\Message();
          $mail->setBody($body);
	        $mail->setFrom('recorrente@benfeitoria.com','Recorrente Benfeitoria');
	        $mail->addReplyTo('recorrente@benfeitoria.com','Recorrente Benfeitoria');
          $mail->addTo($oUserTo->getEmail(), $oUserTo->getNome());
          $mail->setSubject($sSubject);
          foreach(array('Subject', 'From', 'To') as $_h){
              $mail->getHeaders()->get($_h)->setEncoding('UTF-8');
          } 
          $transport = new SmtpTransport();
          $transport->setOptions( $options );
          $transport->send($mail);
	        $sRetorno = "ok";
	        // Seta o status
	        $oProject->changeStatus($iProjectIdStatus);
	    }
	    else {
	        $sRetorno = "error";
	    }
	    // Prepara a saída JSON
	    $response = $this->getResponse();
	    $response->setContent(\Zend\Json\Json::encode(array('retorno' => $sRetorno)));
	    return $response;
	     
  }










}
