<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Mail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime;
use Zend\Http\Client;
use Zend\Http\Request;
use Application\Model\Seguranca;
use Application\Model\Perfil;
use Application\Model\Formulario;
use Application\Model\Project;
use Application\Model\ProjectList;
use Application\Model\Contribution;
use Application\Model\City;

class IndexController extends AbstractActionController
{

    protected $umServiceManager;
    protected $config;
    protected $umSessionManager;
    protected $umContainer;
    protected $umaSeguranca;
    protected $umRenderer;
    
    /* Função que instancia objetos comuns utilizados pelo objeto atual */
    private function init() {
    	$this->umServiceManager = $this->getServiceLocator();
    	$this->config = $this->umServiceManager->get("config");	// Captura o config setado em global|local
    	$this->umSessionManager = $this->umServiceManager->get('Zend\Session\SessionManager');
    	$this->umContainer = new Container("cfc",$this->umSessionManager); // Cria o container, usando o sessionmanager criado no Module.php
    	$this->umaSeguranca = new Seguranca($this->umServiceManager); // Instancia o gerenciador de segurança
    	$this->umRenderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
    }
    
    /* registra os  Getters and Setters */
    // Retorna (GET) umContainer
    public function getContainer() {
        return $this->umContainer;
    }
    

    /* ################### CHAMADAS DE ACTIONS ############################################ */
    
    
    
    
    public function indexAction()
    {
        $this->init();
        //$this->layout("layout/moldura");
        $this->layout("v1/layout/moldura");
        $this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
        //$this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
        
        $oProjectList = new ProjectList($this->umServiceManager);
        $aProjects = $oProjectList->getProjectsByStatus(6,null,null,6);
        
        $umViewModel = new ViewModel();
        $umViewModel->setTemplate("v1/home");
        $umViewModel->setVariable("aProjects", $aProjects);
        
        return $umViewModel;
    }



    /*
     * Método que evoca a interface de login
    */
    public function loginAction()
    {
    	$this->init();
    	$this->layout("v1/layout/moldura");
    
    	$this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);

    	$sAction = $this->params()->fromRoute('id', '');
    	 
    	$umViewModel = new ViewModel();
    	$umViewModel->setTemplate("v1/login");
    	switch ($sAction) {
    	    case "invalido" : $sMessage = "Usuário ou senha incorretos";
    	                      break;
    	    case "rec-sucesso" : $sMessage = "Sua senha foi alterada com sucesso. Efetue seu login abaixo.";
    	                      break;
          default         : $sMessage = "";
                            
                            if (strpos($sAction,"=")!==false) {
                                $aTemp = explode("=",$sAction); 
                                $sURLRedirect = base64_decode($aTemp[1]);
                                $umViewModel->setVariable("sURLRedirect",$sURLRedirect);
                            }
                            break;
    	}
    	$umViewModel->setVariable("sMessage",$sMessage);
    
    
    	return $umViewModel;
    }
    
    
    
    /*
     * Método que evoca a interface de seleção de tipo de cadastro (PF ou PJ)
     */
    public function cadastroAction()
    {
        $this->init();
        $this->layout("v1/layout/moldura");
        
        $this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
        
        $umViewModel = new ViewModel();
        $umViewModel->setTemplate("v1/cadastro");
                
        return $umViewModel;
    }

    
    
    /*
     * Método que evoca o formulário de pessoa física
     */
    public function cadastroPFAction()
    {
        $this->init();
        $this->layout("v1/layout/moldura");
        
        $this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
        
        $umForm = new Formulario($this->umServiceManager);
        
        $umViewModel = new ViewModel();
        $umViewModel->setTemplate("v1/cadastro-pf");
        
        $aCountries = $umForm->getCountries(true);
        $sHTMLCountries = "";
        foreach ($aCountries as $oCountry) {
            $sHTMLCountries .= "<option value=\"".$oCountry["id"]."\">".$oCountry["name"]."</option>\n";
        } 
        $umViewModel->setVariable("sHTMLCountries", $sHTMLCountries);
        return $umViewModel;
    }



    /*
     * Método que evoca o formulário de pessoa juridica
    */
    public function cadastroPJAction()
    {
    	$this->init();
    	$this->layout("v1/layout/moldura");
    
    	$this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
    
    	$umForm = new Formulario($this->umServiceManager);
    
    	$umViewModel = new ViewModel();
    	$umViewModel->setTemplate("v1/cadastro-pj");
    	 
    	$aCountries = $umForm->getCountries(true);
    	$sHTMLCountries = "";
    	foreach ($aCountries as $oCountry) {
    		$sHTMLCountries .= "<option value=\"".$oCountry["id"]."\">".$oCountry["name"]."</option>\n";
    	}
    	$umViewModel->setVariable("sHTMLCountries", $sHTMLCountries);
    	return $umViewModel;
    }



    /*
     * Método que evoca o formulário de cadastro de projeto (arrecade)
    */
    public function arrecadeAction()
    {
    	$this->init();
    	if ($this->umaSeguranca->sessaoAtiva($this)) {
        	$this->layout("v1/layout/moldura");
        
        	$this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
        	
        	$umForm = new Formulario($this->umServiceManager);
        	// Dados no form
        	$aCompanyTypes = $umForm->getCompanyTypes(); 
        	$aCategories = $umForm->getCategories();
        	 
        	$umViewModel = new ViewModel();
        	$umViewModel->setTemplate("v1/cadastra-projeto");
        	$umViewModel->setVariable("aCompanyTypes",$aCompanyTypes);
        	$umViewModel->setVariable("aCategories",$aCategories);
        	$umViewModel->setVariable("oUser",new Perfil ($this->umServiceManager,$this->umContainer->iIdUsuarioSiteLogado));
        	return $umViewModel;
    	}
    	else {
    	    // Encaminha para a tela de login
    	    return $this->redirect()->toURL($this->umRenderer->basePath('/login/u='.base64_encode($this->umRenderer->basePath('/arrecade'))));
    	}
    }



    /*
     * Tela explicativa de como funciona o CFC
    */
    public function comoFuncionaAction()
    {
    	$this->init();
    	$this->layout("v1/layout/moldura");
    
    	$this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
    	 
    	$umForm = new Formulario($this->umServiceManager);
    
    
    	$umViewModel = new ViewModel();
    	$umViewModel->setTemplate("v1/como-funciona");
    	return $umViewModel;
    }
    


    /*
     * Método que evoca a lista de projetos com filtro
    */
    public function campanhasAction()
    {
        $this->init();
    		$this->layout("v1/layout/moldura");
    
    		$this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
    		
        $iCategoryId = null;
    		$aKeyWords = null;
    		$iCategorySelected = null;
    		
    		$sAction = $this->params()->fromRoute('id', '');
    		if (strpos($sAction,"-")!==false) {
    		    $aTemp = explode("-",$sAction);
    		    $sAction = $aTemp[0];
    		    $iCategoryId = $aTemp[1];
    		    $iCategorySelected = $iCategoryId;
    		    if ($iCategoryId==13) {
    		        $iCategoryId = null;
    		    }
    		}
    		if (is_null($iCategoryId)) {
    		    $iCategorySelected = 13;
    		}
    		switch ($sAction) {
    			case "palavra"       :   $request = $this->getRequest();
                                       if ($request->isPost()) {
                                           $sKeyWords = $request->getPost("key_words");
                                           if (strpos($sKeyWords," ")!==false) {
                                               $aKeyWords = explode(" ",$sKeyWords);
                                           }
                                           else {
                                               $aKeyWords[0] = $sKeyWords;
                                           }
                                       }
    		}
    		
    		$oForm = new Formulario($this->umServiceManager);
    		$oProjectList = new ProjectList($this->umServiceManager);
        $aProjects = $oProjectList->getProjectsByStatus(6,$aKeyWords,$iCategoryId);
    		    
    		$umViewModel = new ViewModel();
    		$umViewModel->setTemplate("v1/projetos");
    		$umViewModel->setVariable("oProjects", $aProjects);
    		$umViewModel->setVariable("oCategorieList", $oForm->getCategories());
    		$umViewModel->setVariable("iCategorySelected", $iCategorySelected);
    		return $umViewModel;
    }



    /*
     * Tela da FAQ
     */
    public function faqAction()
    {
        $this->init();
        $this->layout("v1/layout/moldura");
    
        $this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
    
        $umForm = new Formulario($this->umServiceManager);
    
    
        $umViewModel = new ViewModel();
        $umViewModel->setTemplate("v1/faq");
        return $umViewModel;
    }

    public function crowdfundingXRecorrenteAction()
    {
        $this->init();
        $this->layout("v1/layout/moldura");
    
        $this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
    
        $umForm = new Formulario($this->umServiceManager);
    
        $umViewModel = new ViewModel();
        $umViewModel->setTemplate("v1/crowdfunding-x-recorrente");
        return $umViewModel;
    }


    public function contatoAction()
    {
        $this->init();
        $this->layout("v1/layout/moldura");
    
        $this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
    
        $umForm = new Formulario($this->umServiceManager);
    
        $umViewModel = new ViewModel();
        $umViewModel->setTemplate("v1/contato");
        return $umViewModel;
    }

    public function faleConoscoAction()
    {
        return $this->contatoAction();
    }

    /*
     * Tela da FAQ
     */
    public function termosUsoAction()
    {
        $this->init();
        $this->layout("v1/layout/moldura");
    
        $this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
    
        $umForm = new Formulario($this->umServiceManager);
    
    
        $umViewModel = new ViewModel();
        $umViewModel->setTemplate("v1/termos-uso");
        return $umViewModel;
    }
    


    /*
     * Método que chama a tela de retorno para o usuário
    */
    public function infoAction()
    {
    	$this->init();
    	$this->layout("v1/layout/moldura");

    	$sIdMensagem = $this->params()->fromRoute('id', '');
    	 
      $this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
    	
      switch ($sIdMensagem) {
      	case "sucesso-pf"   :   $sTituloMensagem = "Sucesso!";
      	                        $sMensagem = "O cadastro foi realizado com sucesso. Verifique seu e-mail para ativá-lo.";
      	                        break;
        case "conta-ativada":   $sTituloMensagem = "Que legal!";
      	                        $sMensagem = "Agora a sua conta está pronta para ser usada em nosso portal. Descubra projetos interessantes e contribua agora! Faça seu login acima.";
      	                        break;
      	case "sem-codigo"   :   $sTituloMensagem = "Ops...";
      	                        $sMensagem = "Para ativar uma conta você precisa usar um código válido, que consta no link em seu email.";
      	                        break;
        case "erro-ativacao":   $sTituloMensagem = "Ops...";
      	                        $sMensagem = "Algo estranho aconteceu ao tentar ativar sua conta. Tente novamente.";
      	                        break;
      	                         
      	case "sucesso-projeto":   $sTituloMensagem = "Sucesso!";
      	                        $sMensagem = "Seu projeto foi cadastrado com sucesso e será avaliado. Em breve entraremos em contato. Obrigado!";
      	                        break;
      	case "sucesso-atualizacao":   $sTituloMensagem = "Sucesso!";
      	                        $sMensagem = "Os dados de seu cadastro foram atualizados.";
      	                        break;
      	case "sem-acesso"   :   $sTituloMensagem = "Ops...";
      	                        $sMensagem = "Você não tem acesso a esse conteúdo nesse momento! Provavelmente tem alguém preparando alguma surpresa por aqui. Fique ligado e volte mais tarde!";
      	                        break;
        default             :   $sTituloMensagem = "";
                                $sMensagem = "";
                                break;
      }
    
    	$umViewModel = new ViewModel();
    	$umViewModel->setTemplate("v1/info");
    	$umViewModel->setVariable("sMensagem", $sMensagem);
    	$umViewModel->setVariable("sTituloMensagem", $sTituloMensagem);
    	return $umViewModel;
    }
    

    
    /* ################### TRATAMENTO DE REQUISIÇÕES AJAX ############################################ */
    
    
    

    /*
     * Método para verificar se o email já foi cadastrado
     */
    public function ajaxVerificaEmailAction() {
    	$this->init();
      $sRetorno = "livre";
    	$request = $this->getRequest();
	  	if ($request->isPost()) {
	  		 $sEmail = $request->getPost("email");
    	   $umForm = new Formulario($this->umServiceManager);
	  		 if ($umForm->emailExists($sEmail)) {
    	       $sRetorno = "existe";
    	   }
	  	}
	  	// Prepara a saída JSON
	    $response = $this->getResponse();
	  	$response->setContent(\Zend\Json\Json::encode(array('retorno' => $sRetorno)));
	  	return $response;
    }
    

    
    public function ajaxListaEstadosAction() {
        $sHTMLStates = "<option value=\"\">Escolha o estado ------></option>\n";
        $this->init();
        $iIdCountry = $this->params()->fromRoute('id', 0);
        $umForm = new Formulario($this->umServiceManager);
        $aStates = $umForm->getCountryStates($iIdCountry);
        foreach ($aStates as $oState) {
            $sHTMLStates .= "<option value=\"".$oState["name"]."\">".$oState["name"]."</option>\n";
        } 
        $umViewModel = new ViewModel();
        $umViewModel->setTemplate("geral/ajax/lista-estados");
        $umViewModel->setTerminal(true);
        $umViewModel->setVariable("sHTMLListaEstados",$sHTMLStates);
        return $umViewModel;
    }



    public function ajaxListaCidadesDoEstadoAction() {
    	$sHTMLCitites = "<option value=\"\">Escolha a cidade ------></option>\n";
    	$this->init();
    	$sState = $this->params()->fromRoute('id', 0);
    	$umForm = new Formulario($this->umServiceManager);
    	$aCities = $umForm->getCitiesByState($sState);
    	foreach ($aCities as $oCity) {
    		$sHTMLCitites .= "<option value=\"".$oCity["id"]."\">".$oCity["name"]."</option>\n";
    	}
    	$umViewModel = new ViewModel();
    	$umViewModel->setTemplate("geral/ajax/lista-cidades");
    	$umViewModel->setTerminal(true);
    	$umViewModel->setVariable("sHTMLListaCidades",$sHTMLCitites);
    	return $umViewModel;
    }
    
    
    


    public function ajaxHeaderLoggedAction() {
    	$this->init();
    	$umViewModel = new ViewModel();
    	$umViewModel->setTemplate("geral/ajax/header-logged");
    	$umViewModel->setVariable("umUsuario", new Perfil($this->umServiceManager,$this->umContainer->iIdUsuarioSiteLogado) );
    	$umViewModel->setTerminal(true);
    	return $umViewModel;
    }



    public function ajaxHeaderLoginAction() {
    	$this->init();
    	$umViewModel = new ViewModel();
    	$umViewModel->setTemplate("geral/ajax/header-login");
    	$umViewModel->setTerminal(true);
    	return $umViewModel;
    }





    public function ajaxV1HeaderLoggedAction() {
    	$this->init();
    	$sAction = $this->params()->fromRoute('id', '');
    	$umViewModel = new ViewModel();
    	$umViewModel->setTemplate("v1/ajax/header-logged");
    	if (strpos($sAction,"=")!==false) {
        $aTemp = explode("=",$sAction); 
        $sURLRedirect = base64_decode($aTemp[1]);
        $umViewModel->setVariable("sURLRedirect",$sURLRedirect);
      }
    	$umViewModel->setVariable("umUsuario", new Perfil($this->umServiceManager,$this->umContainer->iIdUsuarioSiteLogado) );
    	$umViewModel->setTerminal(true);
    	return $umViewModel;
    }
    
    
    
    public function ajaxV1HeaderLoginAction() {
    	$this->init();
    	$sAction = $this->params()->fromRoute('id', '');
    	$umViewModel = new ViewModel();
    	$umViewModel->setTemplate("v1/ajax/header-login");
    	if (strpos($sAction,"=")!==false) {
        $aTemp = explode("=",$sAction); 
        $sURLRedirect = base64_decode($aTemp[1]);
        $umViewModel->setVariable("sURLRedirect",$sURLRedirect);
      }
    	$umViewModel->setTerminal(true);
    	return $umViewModel;
    }
    


    public function ajaxMensagemAction() {
    	$this->init();
    	$sMessageId = $this->params()->fromRoute('id', '');
    	switch ($sMessageId) {
    		case "sucesso"                    :   $sMessage = "Sua contribuição foi registrada com sucesso!";
    		                                      break;
    		case "erro-cria-contribuicao"     :   $sMessage = "Problemas ao criar a contribuição no sistema. Tente novamente.";
    		                                      break;
    		case "erro-atualiza-endereco"     :   $sMessage = "Problemas ao registrar sua atualização de endereço no sistema. Tente novamente.";
    		                                      break;
    	}
    	$umViewModel = new ViewModel();
    	$umViewModel->setTemplate("geral/ajax/message");
    	$umViewModel->setVariable("sMessage", $sMessage);
    	$umViewModel->setTerminal(true);
    	return $umViewModel;
    }
    
    
    
    /* ################### PROCESSAMENTO DO FORMS, SEM VIEWMODEL ############################################ */
    
    
    /*
     * Método que cria o cadastro, conforme o tipo
     */
    public function criaCadastroAction()  {
        $this->init();
        $sAjax = "";
    	  $request = $this->getRequest();
	  	  if ($request->isPost()) {
	  	      $sAjax = $request->getPost("ajax");
	  	      $sImageData = $request->getPost("image_data");
	  	      $sRazaoSocial = null;
	  	      $sCNPJ = null;
	  	      $sTipo = $request->getPost("tipo");
	  	      if ($sTipo=="PF") {
    	  	      $sEmail = $request->getPost("email");
    	  	      $sSenha = $request->getPost("senha1");
    	  	      $sNome = $request->getPost("nome");
                $sSexo = $request->getPost("sexo");
                $sIdPais = $request->getPost("pais");
                if ($sIdPais==5) {
                    $sEstado = $request->getPost("estado");
                    $sCidade = $request->getPost("cidade");
                }
                else {
                    $sEstado = $request->getPost("novo_estado");
                    $sCidade = $request->getPost("nova_cidade");
                }
	  	      }
	  	      else {
	  	          $sEmail = $request->getPost("email");
	  	          $sSenha = $request->getPost("senha1");
	  	          $sNome = $request->getPost("nome");
	  	          $sSexo = $request->getPost("sexo");
	  	          $sIdPais = $request->getPost("pais");
	  	          if ($sIdPais==5) {
	  	          	$sEstado = $request->getPost("estado");
	  	          	$sCidade = $request->getPost("cidade");
	  	          }
	  	          else {
	  	          	$sEstado = $request->getPost("novo_estado");
	  	          	$sCidade = $request->getPost("nova_cidade");
	  	          }
	  	          $sCNPJ = $request->getPost("cnpj");
	  	          $sRazaoSocial = $request->getPost("razao_social");
	  	      }
            // Cria o perfil
            $umPerfil = new Perfil($this->umServiceManager);
            $umPerfil->setEmail($sEmail);
            $umPerfil->setSenha(md5($sSenha));
            $umPerfil->setNome($sNome);
            $umPerfil->setSexo($sSexo);
            $umPerfil->setTipo($sTipo);
            $umPerfil->setCompanyName($sRazaoSocial);
            $umPerfil->setCNPJ($sCNPJ);
            
            // Escolhe o tipo de usuário
            $umPerfil->setIdPerfil(1); // Registra como Benfeitor
            
            // Adiciona o endereco
		        $umPerfil->setEndereco("residencial",null,null,$sCidade);
            // Adiciona os telefones
		        $umPerfil->adicionaTelefone("residencial", "home", null);
		        $umPerfil->adicionaTelefone("comercial", "business", null);
		        $umPerfil->adicionaTelefone("celular", "cell", null);
		        // Gera o ID único de ativação
		        $sIdUnicoAtivacao = uniqid();
		        $umPerfil->setIdAtivacao($sIdUnicoAtivacao);
		        // Ativa direto ###################################### PEDIDO DO TEO ######################################
		        $umPerfil->setAtivo(true);
		        // Grava no BD
		        $umPerfil->salvarDados();
		        $umPerfil->gravaEnderecos();
		        
		        // Atualiza imagem
		        if (!empty($sImageData)) {
		            $iIdUsuario = $umPerfil->getId();
		            $sArquivoSaida = "/users_images/".$iIdUsuario."/";
		            if (!file_exists($_SERVER["DOCUMENT_ROOT"].$sArquivoSaida)) {
		                // Cria o diretorio
		                mkdir($_SERVER["DOCUMENT_ROOT"].$sArquivoSaida,0777);
		                mkdir($_SERVER["DOCUMENT_ROOT"].$sArquivoSaida."medium/",0777);
		                mkdir($_SERVER["DOCUMENT_ROOT"].$sArquivoSaida."small/",0777);
		            }
		            // Cria o arquivo médio
		            $sArquivoSaida .= "medium/profile.jpg";
		            $ifp = fopen($_SERVER["DOCUMENT_ROOT"].$sArquivoSaida, "wb");
		            $data = explode(',', $sImageData);
		            fwrite($ifp, base64_decode($data[1]));
		            fclose($ifp);
		            // Cria o arquivo pequeno
		            $sArquivoSaida = str_replace("/medium/","/small/",$sArquivoSaida);
		            $ifp = fopen($_SERVER["DOCUMENT_ROOT"].$sArquivoSaida, "wb");
		            $data = explode(',', $sImageData);
		            fwrite($ifp, base64_decode($data[1]));
		            fclose($ifp);
		        }
		        
            // Conforme a situação, escolhe qual o tipo de email enviar    		        
		        if ($umPerfil->getAtivo()) {
		            $this->sendWelcomeEmail($umPerfil->getId());
		        }
		        else {
    		        // Encaminha um email
    		        $options = new SmtpOptions( array(
																			"name" => "mandrillapp",
																			"host" => "smtp.mandrillapp.com",
																			"port" => 587,
																			"connection_class" => "plain",
																			"connection_config" => array( "username" => "andre.piacentini@gmail.com",
																			"password" => "LrWNuu9ZlJObxRWsT-J63Q","ssl" => "tls" )
    		                		        ) );
    		        $sURLAtivacao = $_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"];
    		        
    		        if (empty($sAjax)) {
    		              // Email Normal
    		              $body = "
                        Olá ".$sNome.",<br/>
                        <br/>
                        Seu cadastro está quase concluído!<br/>
                        Acesse o link abaixo para ativá-lo!<br/>
                        <br/>
                        Endereço: <a href=\"http://".$sURLAtivacao."/ativaCadastro/".$sIdUnicoAtivacao."\">http://".$sURLAtivacao."/ativaCadastro/".$sIdUnicoAtivacao."</a><br/>
                        <br/>
                        Obrigado e seja bem-vindo!<br/>
                        Equipe Recorrente
    	                ";
    		              
    //		          $body = "Olá ".$sNome."!<br/><br/>Seu cadastro está quase concluído. Basta apenas acessar o link abaixo para ativá-lo:<br/><br/><a href=\"".$sURLAtivacao."/ativaCadastro/".$sIdUnicoAtivacao."\">Clique aqui para ativar sua conta agora!</a>";
    		        }
    		        else {
    //		            $body = "Olá ".$sNome."!<br/><br/>Seu cadastro está quase concluído. Copie o código de ativação abaixo e informe no local adequado para continuar.<br/><br/>Código de cadastro: ".$sIdUnicoAtivacao;
    		              $body = "
                        Olá ".$sNome.",<br/>
                        <br/>
                        Seu cadastro está quase concluído!<br/>
                        Copie o código de ativação abaixo e informe no site!<br/>
                        <br/>
                        Código: ".$sIdUnicoAtivacao."<br/>
                        <br/>
                        Obrigado e seja bem-vindo!<br/>
                        Equipe Recorrente
    	                ";
    		        }
    		        $htmlPart = new MimePart($body);
    		        $htmlPart->type = "text/html";
    		        	
    		        //	$attachment = new MimePart(fopen('/var/www/site/v2/module/Application/src/Application/Controller/teste.png', 'r'));
    		        //  $attachment->type = 'image/png';
    		        //  $attachment->encoding    = Mime::ENCODING_BASE64;
    		        //  $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
    		        	
    		        $body = new MimeMessage();
    		        $body->setParts(array($htmlPart));
    		        
    		        $mail = new Mail\Message();
    		        $mail->setBody($body);
                $mail->setFrom('recorrente@benfeitoria.com','Recorrente Benfeitoria');
                $mail->addReplyTo('recorrente@benfeitoria.com','Recorrente Benfeitoria');
                $mail->addTo($sEmail, $sNome);
    		        $mail->setSubject("Código de validação de Email");
    		        	
    		        $transport = new SmtpTransport();
    		        $transport->setOptions( $options );
    		        $transport->send($mail);
		        
		        }
	  	  
	  	  }
	  	  if (empty($sAjax)) {
	  	      if (!$umPerfil->getAtivo()) {
	  	        $url = $this->umRenderer->basePath('/info/sucesso-pf');
	  	      }
	  	      else {
  	          $url = $this->umRenderer->basePath('/info/conta-ativada');
    	      }
    	  	  return $this->redirect()->toURL($url);
	  	  }
	  	  else {
	  	      // Prepara a saída JSON
	  	      $response = $this->getResponse();
	  	      if ($umPerfil->getAtivo()) {
	  	          $sAtivo = "active";
	  	      }
	  	      else {
	  	          $sAtivo = "waiting";
	  	      }
	  	      $response->setContent(\Zend\Json\Json::encode(array('retorno' => 'ok','status_user' => $sAtivo,'user_id' => $umPerfil->getId())));
	  	      return $response;
	  	  }
    }

    
    
    

    /*
     * Método que para atualizar um cadastro existente
    */
    public function atualizaCadastroAction()  {
    	$this->init();
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$sRazaoSocial = null;
    		$sCNPJ = null;
    		$sCPF = null;
    		$sImageData = $request->getPost("image_data");
    		$sTipo = $request->getPost("tipo");
    		$iIdUsuario = $request->getPost("id_usuario");
        $sSenha = $request->getPost("senha1");
        $sNome = $request->getPost("nome");
        $sSexo = $request->getPost("sexo");
        $sDataNascimento = $request->getPost("data_nascimento");
        $aTemp = explode("/",$sDataNascimento);
        $sDataNascimento = $aTemp[2]."-".$aTemp[1]."-".$aTemp[0];
        $sIdPais = $request->getPost("pais");
        $sEndereco = $request->getPost("endereco_rua");
        $sEnderecoNumero = $request->getPost("endereco_numero");
        $sEnderecoComplemento = $request->getPost("endereco_complemento");
        $sEnderecoBairro = $request->getPost("endereco_bairro");
        $sCEP = $request->getPost("cep");
        if ($sIdPais==5) {
        	$sEstado = $request->getPost("estado");
        	$sCidade = $request->getPost("cidade");
        }
        else {
        	$sEstado = $request->getPost("novo_estado");
        	$sCidade = $request->getPost("nova_cidade");
        }
    		if ($sTipo=="PF") {
    		    $sCPF = $request->getPost("cpf");
    		}
    		else {
    			$sCNPJ = $request->getPost("cnpj");
    			$sRazaoSocial = $request->getPost("razao_social");
    		}
    		// Cria/Atualiza as imagens do usuário
    		if (!empty($sImageData)) {
        		$sArquivoSaida = "/users_images/".$iIdUsuario."/";
        		if (!file_exists($_SERVER["DOCUMENT_ROOT"].$sArquivoSaida)) {
        		    // Cria o diretorio
        		    mkdir($_SERVER["DOCUMENT_ROOT"].$sArquivoSaida,0777);
        		    mkdir($_SERVER["DOCUMENT_ROOT"].$sArquivoSaida."medium/",0777);
        		    mkdir($_SERVER["DOCUMENT_ROOT"].$sArquivoSaida."small/",0777);
        		}
        		// Cria o arquivo médio
        		$sArquivoSaida .= "medium/profile.jpg"; 
        		$ifp = fopen($_SERVER["DOCUMENT_ROOT"].$sArquivoSaida, "wb");
        		$data = explode(',', $sImageData);
        		fwrite($ifp, base64_decode($data[1]));
        		fclose($ifp);
        		// Cria o arquivo pequeno
        		$sArquivoSaida = str_replace("/medium/","/small/",$sArquivoSaida); 
        		$ifp = fopen($_SERVER["DOCUMENT_ROOT"].$sArquivoSaida, "wb");
        		$data = explode(',', $sImageData);
        		fwrite($ifp, base64_decode($data[1]));
        		fclose($ifp);
    		}
    		// Carrega o usuario
    		$umPerfil = new Perfil($this->umServiceManager,$iIdUsuario);
    		if (!empty($sSenha)) {
    		  $umPerfil->setSenha(md5($sSenha));
    		}
    		$umPerfil->setNome($sNome);
    		$umPerfil->setSexo($sSexo);
    		$umPerfil->setTipo($sTipo);
    		$umPerfil->setDataNascimento($sDataNascimento);
    		$umPerfil->setCompanyName($sRazaoSocial);
    		$umPerfil->setCPF($sCPF);
    		$umPerfil->setCNPJ($sCNPJ);
    
    		// Adiciona o endereco
    		$umPerfil->setEndereco("residencial",$sCEP,$sEndereco,$sCidade,$sEnderecoNumero,$sEnderecoComplemento,$sEnderecoBairro);
    		// Adiciona os telefones
    		$umPerfil->adicionaTelefone("residencial", "home", null);
    		$umPerfil->adicionaTelefone("comercial", "business", null);
    		$umPerfil->adicionaTelefone("celular", "cell", null);
    		// Atualiza o cadastro no BD
    		$umPerfil->salvarDados();
    		$umPerfil->gravaEnderecos();
    
    	}
    	$url = $this->umRenderer->basePath('/info/sucesso-atualizacao');
    	return $this->redirect()->toURL($url);
    }




    /*
     * Método que valida o código do cadastro que o usuário recebu por email
    */
    public function validaCodigoCadastroAction()  {
    	$this->init();
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		 
        	$sCodigo = $request->getPost("code");
        	if (!empty($sCodigo)) {
        	    $iUserId = $this->umaSeguranca->validaCodigoCadastro($sCodigo);
        	    if ( ($iUserId!==false) && (!empty($iUserId)) ) {
        	        $sStatus = "ok";
        	        $umForm = new Formulario($this->umServiceManager);
        	        $umForm->activeAccount($sCodigo);
        	        $this->sendWelcomeEmail($iUserId);
        	    }
        	    else {
        	        $sStatus = "error";
        	        $sEmail = "";
        	        $iUserId = "";
        	    }
              // Prepara a saída JSON
              $response = $this->getResponse();
              $response->setContent(\Zend\Json\Json::encode(array('retorno' => $sStatus, 'user_id' => $iUserId)));
              return $response;
        	}
    	}
    }
    



    /*
     * Método que ativa o cadastro a partir do link do email
    */
    public function ativaCadastroAction()  {
    	$this->init();
    	$sCodigo = $this->params()->fromRoute('id', '');
    	if (!empty($sCodigo)) {
    	    $iUserId = $this->umaSeguranca->validaCodigoCadastro($sCodigo);
    	    $umForm = new Formulario($this->umServiceManager);
    	    $bRetorno = $umForm->activeAccount($sCodigo);
    	    $this->sendWelcomeEmail($iUserId);
    	    if ($bRetorno) {
    	        $url = $this->umRenderer->basePath('/info/conta-ativada');
    	    }
    	    else {
    	        $url = $this->umRenderer->basePath('/info/erro-ativacao');
    	    }
    	}
    	else {
    	    $url = $this->umRenderer->basePath('/info/sem-codigo');
    	}
      return $this->redirect()->toURL($url);
    }

    
    
    
    
    
    
    /*
     * Envia mensagem por email de boas vindas
     */
    private function sendWelcomeEmail($iUserId) {
        $oUser = new Perfil($this->umServiceManager,$iUserId);
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
        
            // Email Normal
            $body = "
		                Seja bem vindo(a) ao Recorrente, uma nova rede para dar visibilidade e gerar valor a iniciativas benfeitoras!<br/>
                    <br/>
                    Se você já faz parte de uma e gostaria de aumentar o seu impacto, buscando sustentabilidade a partir de uma rede de apoiadores altamente engajados, entre em http://recorrente.benfeitoria.com/arrecade e envie sua proposta pra gente!<br/>
                    <br/>
                    Ou então, se por enquanto você quer apenas participar e agregar valor à esta rede, confira as campanhas que já estão em nossa plataforma no link http://recorrente.benfeitoria.com/asCampanhas e torne-se um assinante daquela que você mais curtir!<br/>
                    <br/>
                    Qualquer dúvida, sugestão ou crítica, pode falar com a gente pelo email recorrente@benfeitoria.com!</br>
                    <br/>
                    Um grande abraço,<br/>
                    Equipe Recorrente<br/>
		              ";
         
        
        
        $htmlPart = new MimePart($body);
        $htmlPart->type = "text/html";
         
        //	$attachment = new MimePart(fopen('/var/www/site/v2/module/Application/src/Application/Controller/teste.png', 'r'));
        //  $attachment->type = 'image/png';
        //  $attachment->encoding    = Mime::ENCODING_BASE64;
        //  $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
         
        $body = new MimeMessage();
        $body->setParts(array($htmlPart));
        
        $mail = new Mail\Message();
        $mail->setBody($body);
        $mail->setFrom('recorrente@benfeitoria.com','Recorrente Benfeitoria');
        $mail->addReplyTo('recorrente@benfeitoria.com','Recorrente Benfeitoria');
        $mail->addTo($sEmail, $sNome);
        $mail->setSubject("Bem vind@ ao Recorrente");
         
        $transport = new SmtpTransport();
        $transport->setOptions( $options );
        $transport->send($mail);
        
    }

    
    
    public function criaNovoProjetoAction() {
        $this->init();
        $request = $this->getRequest();
        if ($request->isPost()) {
            // Captura todos os campos
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
            $aProjectData["goal1_money"] = $request->getPost("meta1_valor");
            $aProjectData["goal1_objective_for_money"] = $request->getPost("meta1_objetivos_valor");
            $aProjectData["goal1_reward"] = $request->getPost("meta1_recompensa");
            $aProjectData["goal2_money"] = $request->getPost("meta2_valor");
            $aProjectData["goal2_objective_for_money"] = $request->getPost("meta2_objetivos_valor");
            $aProjectData["goal2_reward"] = $request->getPost("meta2_recompensa");
            $aProjectData["goal3_money"] = $request->getPost("meta3_valor");
            $aProjectData["goal3_objective_for_money"] = $request->getPost("meta3_objetivos_valor");
            $aProjectData["goal3_reward"] = $request->getPost("meta3_recompensa");
            $aProjectData["marketing"] = $request->getPost("como_sera_arrecadacao");
            $aProjectData["link_title"] = $request->getPost("link_titulo");
            $aProjectData["link_url"] = $request->getPost("link_url");
            $aProjectData["dt_registration"] = time();
            $aProjectData["dt_update"] = null;
            $aProjectData["id_status"] = 1;
            
            // Cria o projeto para ser gravado
            $oProject = new Project($this->umServiceManager,null,$aProjectData);
            $oProject->saveData();
            
            // Registra o usuário agora como projetista
            $umPerfil = new Perfil($this->umServiceManager,$this->umContainer->iIdUsuarioSiteLogado);
            $umPerfil->setIdPerfil(2); // Registra como Projetista
        		// Carrega o usuario
        		$umPerfil->setNome($umPerfil->getNome()); // Não pode mudar
        		$umPerfil->setSexo($umPerfil->getSexo()); // Não pode mudar
        		$umPerfil->setTipo($umPerfil->getTipo());
        		$umPerfil->setDataNascimento(date("Y-m-d",$umPerfil->getDataNascimento()));
        		$umPerfil->setCompanyName($umPerfil->getCompanyName());
        		$umPerfil->setCPF($umPerfil->getCPF());
        		$umPerfil->setCNPJ($umPerfil->getCNPJ());
        		$aEndereco = $umPerfil->getEndereco("residencial");
        		$umPerfil->setEndereco("residencial",$aEndereco["zip"],$aEndereco["address"],$aEndereco["id_city"]);
        		// Atualiza o cadastro no BD
        		$umPerfil->salvarDados();
        		$umPerfil->gravaEnderecos();
            $umPerfil->salvarDados();
            

        }
        $url = $this->umRenderer->basePath('/info/sucesso-projeto');
        return $this->redirect()->toURL($url);
    }



    /* ################### AMBIENTE DE TESTES (DEVE SER EXCLUIDO PARA PRODUCAO) ############################################ */
    
    public function testeMoipAction() {
        $this->init();
//         $loginParameters = array(
//         	'login'       => 'A067GJDY6HA7T2U45ZWTRKT2QR314PCC',
//           'password'    => 'VBQRH4JQFJ1EJQKYSB9QUM8E38P2YWLFYXS7G5TR',
//         );

        
//         $sURLRequest = "https://sandbox.moip.com.br/assinaturas/v1/subscriptions/125/suspend";
//         $aReturnValues = $this->sendMoipInstruction($sURLRequest, "PUT");
//         $jsonResponse = $aReturnValues["return"];
//         $jsonError = $aReturnValues["error"];
//         $aResponse = json_decode($jsonResponse,true);
//         print_r($aResponse);
//         exit;
        
        
        $iContributionId = 86;
        $oContribution = new Contribution($this->umServiceManager,$iContributionId);
        $oUser = new Perfil($this->umServiceManager,$oContribution->getProperty("id_user"));
        // Dados complementares do usuário
        $aTelefones = $oUser->getTelefones();
        $sTelefoneResidencial = $aTelefones[0]["number"];
        if (strpos($sTelefoneResidencial," ")!==false) {
            $aTemp = explode(" ",$sTelefoneResidencial);
            $sTelefoneDDD = $aTemp[0];
            $sTelefoneNumber = $aTemp[1];
        }
        else {
            $sTelefoneDDD = "000";
        }
        $aEnderecos = $oUser->getEndereco("residencial");
        $oCity = new City($this->umServiceManager,$aEnderecos["id_city"]);
        $sMoipPlanId = "plano01";
        
        $aPostParameters = array(
        	"code" => "prod-".$oContribution->getId(),
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
        	        "street" => $aEnderecos["address"],
        	        "number" => "0",
        	        "complement" => "unknown",
        	        "district" => "unknown",
        	        "city" => $oCity->getCityName(),
        	        "state" => $oCity->getState(),
        	        "country" => substr($oCity->getCountryName(),0,3),
        	        "zipcode" => str_replace("-","",$aEnderecos["zip"]),
        	    ),
        	    "billing_info" => array(
        	        "credit_card" => array(
        	            "holder_name" => $oContribution->getProperty("cc_holder_name"),
        	            "number" => str_replace(" ","",$oContribution->getProperty("cc_number")),
        	            "expiration_month" => substr($oContribution->getProperty("cc_expiration_date"),0,2),
        	            "expiration_year" => substr($oContribution->getProperty("cc_expiration_date"),5,2),
        	        ),
        	    ),
         	),
        );
        // --------------------------------------------------------------------------------------------------------
        // 1o - verifica se o cliente existe
        $sURLRequest = "https://sandbox.moip.com.br/assinaturas/v1/customers/".($oUser->getId());
        $aReturnValues = $this->sendMoipInstruction($sURLRequest, "GET");
        $jsonResponse = $aReturnValues["return"];
        $jsonError = $aReturnValues["error"];
        $aResponse = json_decode($jsonResponse,true);
        if (is_array($aResponse)) {
            $sURLRequest = "https://sandbox.moip.com.br/assinaturas/v1/subscriptions?new_customer=false";
        }
        else {
            $sURLRequest = "https://sandbox.moip.com.br/assinaturas/v1/subscriptions?new_customer=true";
        }
        
        // 2o - cria assinatura (pode ser que crie o cliente também)
        $aReturnValues = $this->sendMoipInstruction($sURLRequest, "POST", json_encode($aPostParameters));
        $jsonResponse = $aReturnValues["return"];
        $jsonError = $aReturnValues["error"];
        $aResponse = json_decode($jsonResponse,true);
        
        echo "<pre>"; print_r($aPostParameters); echo "</pre>";
        echo "<b>resposta da criacao da assinatura</b><br>";
        echo "<pre>"; print_r($aResponse); exit;
    
    }
    
    // Envia instruções para a moip
    private function sendMoipInstruction($url, $metodo, $dados = null) {
        $sKey = $this->config["moip"]["key"];
        $sHash = $this->config["moip"]["hash"];
        $header[] = "Request Method: $metodo";
        $header[] = "Authorization: Basic " . base64_encode($sKey.':'.$sHash);
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
