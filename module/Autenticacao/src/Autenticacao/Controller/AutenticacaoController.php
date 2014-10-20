<?php
namespace Autenticacao\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Config\StandardConfig;
use Zend\Session\Container;
use Zend\Mail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime;
use Application\Model\Seguranca;
use Application\Model\Perfil;

class AutenticacaoController extends AbstractActionController {

	protected $umServiceManagerm;
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



    private function setaCabecalho($umLayout,$sAjustaPath) {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $sURLLogout = $renderer->basePath('/seguro/logout');
        $sURLLogin = $renderer->basePath('/seguro/validaLogin');
        $sURLCadastro = $renderer->basePath('/cadastro');
        if ( (isset($this->umContainer->iIdUsuarioSiteLogado)) && (!empty($this->umContainer->iIdUsuarioSiteLogado)) ) {
        	$umUsuario = new Perfil($this->umServiceManager,$this->umContainer->iIdUsuarioSiteLogado);
        	$sHTMLUsuario = '
        <div class="navbar-collapse collapse">
          <form class="navbar-form navbar-right" role="form" action="'.$sURLLogout.'" method="post">
            <div class="form-group">
              <a class="navbar-brand" href="#">Olá ' . $umUsuario->getPrimeiroNome() . '</a>
            </div>
            <button type="submit" class="btn btn-success">Sair</button>
          </form>
        </div>
                ';
        }
        else {
        	$sHTMLUsuario = '
        <div class="navbar-collapse collapse">
          <form class="navbar-form navbar-right" role="form" action="'.$sURLLogin.'" method="post">
            <div class="form-group">
              <input type="text" name="usuario" placeholder="Usuário" class="form-control">
            </div>
            <div class="form-group">
              <input type="password" name="senha" placeholder="Senha" class="form-control">
            </div>
            <div class="form-group">
        	    <button type="submit" class="btn btn-success">Entrar</button>
    	      </div>
            <div class="form-group">
        	    <a href="'.$sURLCadastro.'"  class="btn btn-warning">Não tenho cadastro</a>
    	      </div>
        	    </form>
        </div>
                ';
        }
        $umLayout->setVariable("sHTMLUsuario",$sHTMLUsuario);   
        $umLayout->setVariable("sAjustaPath",$sAjustaPath);
    }
		




  public function loginAction() {
    	$this->init();
    	$this->layout("layout/moldura");
    	
		// ViewModel de saída
	  	$umViewModel = new ViewModel();
// 	  	$umViewModel->setTerminal(true);
// 		$umViewModel->setTemplate('autenticacao/login');
	  	return $umViewModel;
  }

  
  
  public function mensagemAction() {
  	$this->init();
  	$this->layout("layout/moldura");
  	$this->setaCabecalho($this->layout(),"../");
  	 
  	$sIdMensagemErro = $this->params()->fromRoute('id', '');
  	 
  	switch ($sIdMensagemErro) {
  		case "invalido"     : $sMensagemErro = "Usuário/senha incorretos ou usuário bloqueado";
  		                      break;
  		default             : $sMensagemErro = "Erro genérico";
  		                      break;
  	}
  	
  	// ViewModel de saída
  	$umViewModel = new ViewModel();
  	$umViewModel->setVariable("sMensagemErro", $sMensagemErro);

  	return $umViewModel;
  }
  





  public function validaLoginAction() {
  	$this->init();
  	$request = $this->getRequest();
  	if ($request->isPost()) {
  		$sUsuario = $request->getPost("usuario");
  		$sSenha = $request->getPost("senha");
  		$sURLRedirect = $request->getPost("url_redirect");
      // Adapter
      $dbAdapter = $this->umServiceManager->get("Zend\Db\Adapter\Adapter");
      // Valida login
      $iIdUsuario = $this->umaSeguranca->validaLogin($sUsuario,$sSenha);
  	  if ($iIdUsuario!==false) {
  			$umPerfil = new Perfil($this->umServiceManager,$iIdUsuario);
  			// Verifica se é do tipo responsável
  			$aIdsPerfis = $umPerfil->getIdsPerfis();
  			$iIdUsuarioSiteLogado = $umPerfil->getId();
  			// Tem Id? Então verifica qual o perfil para encaminhar para a área certa do XMS
  			if (!empty($iIdUsuarioSiteLogado)) {
  				// Registra variáveis de sessão
  				$this->umContainer->iIdUsuarioSiteLogado = $iIdUsuarioSiteLogado;
  				// Registra dados para o webadmin
  				$umContainerWebAdmin = new Container("cfc_webadmin",$this->umSessionManager);
          $umContainerWebAdmin->sUsuarioAdminLogado = $sUsuario; // Já registra para navegar no webadmin
          if (!empty($sURLRedirect)) {
              return $this->redirect()->toURL($sURLRedirect);
          }
          else {
  				  return $this->redirect()->toURL($this->umRenderer->basePath('/'));
          }
  			} else {
  				// Erro
  				print("ID USUARIO LOGADO não existe para o login $sUsuario");
  				exit();
  			}
  		}
  		else {
  			// Falha de login
  			$url = $this->umRenderer->basePath('/login/invalido');
  			return $this->redirect()->toURL($url);
  		}
  	}
  }






  public function ajaxValidaLoginAction() {
  	$this->init();
  	$request = $this->getRequest();
  	if ($request->isPost()) {
  		$sUsuario = $request->getPost("usuario");
  		$sSenha = $request->getPost("senha");
  		$iProjectId = $request->getPost("id_projeto");
  		// Adapter
  		$dbAdapter = $this->umServiceManager->get("Zend\Db\Adapter\Adapter");
  		// Valida login
  		if ($umUsuario = $this->umaSeguranca->validaLogin($sUsuario,$sSenha)) {
  			$umPerfil = new Perfil($this->umServiceManager,null,$sUsuario);
  			// Verifica se é do tipo responsável
  			$aIdsPerfis = $umPerfil->getIdsPerfis();
  			$iIdUsuarioSiteLogado = $umPerfil->getId();
  			// Tem Id?
  			if (!empty($iIdUsuarioSiteLogado)) {
  				// Registra variáveis de sessão
  				$this->umContainer->iIdUsuarioSiteLogado = $iIdUsuarioSiteLogado;
  				$url = $this->umRenderer->basePath('/');
  				// Já contribuiu?
  				if ($umPerfil->isProjectContributor($iProjectId)) {
      				$response = $this->getResponse();
      				$response->setContent(\Zend\Json\Json::encode(array('status' => 'ja-contribuiu','name' => $umPerfil->getPrimeiroNome())));
  				}
  				else {
      				$response = $this->getResponse();
      				$response->setContent(\Zend\Json\Json::encode(array('status' => 'ok','name' => $umPerfil->getPrimeiroNome())));
  				}
  				return $response;
  			}
  			else {
  				// Erro
  				$response = $this->getResponse();
  				$response->setContent(\Zend\Json\Json::encode(array('status' => 'id empty')));
  				return $response;
  			}
  		}
  		else {
  			// Falha de login
  		  $response = $this->getResponse();
  			$response->setContent(\Zend\Json\Json::encode(array('status' => 'error')));
  			return $response;
  		}
  		
  		
  	}
  	else {
  	    // Falha de login
  	    $response = $this->getResponse();
  	    $response->setContent(\Zend\Json\Json::encode(array('status' => 'post only')));
  	    return $response;
  	}
  }






	/* Verifica se o login está disponível (não existe) */
	public function verificaDisponibilidadeLoginAction() {
  	$this->init();
  	$request = $this->getRequest();
  	$sUsuario = strtolower($request->getPost("usuario"));
  	if ($this->umaSeguranca->verificaUsuario($sUsuario)) {
	  	$sSucesso = "true";
  	}
  	else {
	  	$sSucesso = "false";
  	}
  	// Prepara a saída JSON
    $response = $this->getResponse();
  	$response->setContent(\Zend\Json\Json::encode(array('sucesso' => $sSucesso)));
  	return $response;
	}






	
	
	/* Realiza o logout */
	public function logoutAction() {
		// Init
		$this->init();
		// Log
		$this->umaSeguranca->registraLog("autenticacao site","logout realizado com sucesso.");
		// Encerra a sessão para o usuário logado
		$this->umContainer->offsetUnset("iIdUsuarioSiteLogado");
		$sIdErro = $this->params()->fromRoute('id', 0);
		// Direciona para página inicial
		$url = $this->umRenderer->basePath('/');
		return $this->redirect()->toURL($url);
	}







  public function recuperarSenhaAction() {
    	$this->init();
    	$this->layout("v1/layout/moldura");
    	 
			// ViewModel de saída
	  	$umViewModel = new ViewModel();
	  	$umViewModel->setTemplate("v1/autenticacao/esqueci-senha");
	  	return $umViewModel;
  }









		/* Form Registra a lista de contatos encaminhada para indicar o mistério dos sonhos */
    public function formRecuperarSenhaAction() {
    	$this->init();

		  	$request = $this->getRequest();
		  	if ($request->isPost()) {
		  	
					// Lista dos emails
					$sEmail = $request->getPost("email");

					// Instancia o usuário (login = email)
					$umUsuario = new Perfil($this->umServiceManager,null,null,$sEmail);

					// Usuário existe?
					if ($umUsuario->getId()>0) {
						
						// Gera o registro de recuperaçao de senha
						$sChaveUnica = md5(uniqid(rand(), true));
						$umUsuario->preparaParaRecuperacaoSenha($sChaveUnica,$sEmail);
	
						// Encaminha um email para o amigo, com as instruções de como criar o cadastro
						$options = new SmtpOptions( array(
																			"name" => "mandrillapp",
																			"host" => "smtp.mandrillapp.com",
																			"port" => 587,
																			"connection_class" => "plain",
																			"connection_config" => array( "username" => "andre.piacentini@gmail.com",
																			"password" => "LrWNuu9ZlJObxRWsT-J63Q","ssl" => "tls" )
																			) );
																		
					  $body = "Conforme solicitado em nosso web site, segue instruções para recuperação de sua senha:<br/>
					  <br/>
					  Acesse <a href=\"http://".$_SERVER["HTTP_HOST"].$this->umRenderer->basePath("/seguro/alteraSenha/").$sChaveUnica."\">este link</a> para informar uma nova senha.<br/>";
					  
	
					  $htmlPart = new MimePart($body);
					  $htmlPart->type = "text/html";
			
						$body = new MimeMessage();
					  $body->setParts(array($htmlPart));
	
						$mail = new Mail\Message();
						$mail->setBody($body);
    				$mail->setFrom('recorrente@benfeitoria.com', 'Recorrente');
    				$mail->addReplyTo('recorrente@benfeitoria.com', 'Recorrente');
						$mail->addTo($sEmail, $umUsuario->getNome());
						$mail->setSubject("Recupera&ccedil;&atilde;o de senha");
					
						$transport = new SmtpTransport();
						$transport->setOptions( $options );
						$transport->send($mail);

						// Registra o log
						$this->umaSeguranca->registraLog("recuperacao senha","foram enviadas instruções de recuperação de senha para o email ".$sEmail);
	
						// Envia para a tela de aviso
						return $this->redirect()->toURL($this->umRenderer->basePath('/seguro/recuperarSenhaAviso/sucesso'));
					}
					else {
						// Email inexistente (nenhum usuário possui este email)
						return $this->redirect()->toURL($this->umRenderer->basePath('/seguro/recuperarSenhaAviso/inexistente'));
					}
				}

    }







  public function recuperarSenhaAvisoAction() {
    	$this->init();
			$sTipoAviso = $this->params()->fromRoute('id', 0);
			
			switch ($sTipoAviso) {
				case "sucesso"			:	$sMensagem = "Um email foi encaminhado com as instruções de recuperação de senha.";
															break;
				case "inexistente"	:	$sMensagem = "O email informado não pertence a nenhum cadastro existente.";
															break;
				case "chave-invalida"	:	$sMensagem = "A chave informada é inválida ou já foi usada.";
															break;
			}
			
			// ViewModel de saída
	  	$umViewModel = new ViewModel();
    	$this->layout("v1/layout/moldura");
	  	$umViewModel->setTemplate("v1/autenticacao/aviso-recuperar-senha");
	  	$umViewModel->setVariable("sMensagemErro",$sMensagem);
	  	return $umViewModel;
  }










  public function alteraSenhaAction() {
  	$this->init();
  	$this->layout("v1/layout/moldura");
  
  	$sHashKey = $this->params()->fromRoute('id', 0);
  	 
  	
  	// ViewModel de saída
  	$umViewModel = new ViewModel();
  	$umViewModel->setTemplate("v1/autenticacao/altera-senha");
  	$umViewModel->setVariable("sHashKey", $sHashKey);
  	return $umViewModel;
  }
  


	
	
	/* Realiza a alteração de senha */
	public function formAlteraSenhaBDAction() {
		// Init
		$this->init();

  	$request = $this->getRequest();
  	if ($request->isPost()) {
  		$sChave = $request->getPost("hash_key");
  		$sNovaSenha = $request->getPost("password1");

			// Valida a chave
			$iIdUsuarioRecuperacao = $this->umaSeguranca->verificaChaveRecuperacaoSenha($sChave);
			if (!empty($iIdUsuarioRecuperacao)) {
				// Chave válida
				$umUsuario = new Perfil($this->umServiceManager,$iIdUsuarioRecuperacao);
				$umUsuario->loadAllData();
				$umUsuario->setDataNascimento(date("Y-m-d",$umUsuario->getDataNascimento()));
				$umUsuario->setSenha(md5($sNovaSenha));
				$umUsuario->salvarDados();
				// Encaminha um email para o amigo, com as instruções de como criar o cadastro
						$options = new SmtpOptions( array(
																			"name" => "mandrillapp",
																			"host" => "smtp.mandrillapp.com",
																			"port" => 587,
																			"connection_class" => "plain",
																			"connection_config" => array( "username" => "andre.piacentini@gmail.com",
																			"password" => "LrWNuu9ZlJObxRWsT-J63Q","ssl" => "tls" )
																			) );
																								
			  $body = "Sua senha foi alterada com sucesso!<br/>
			  <br/>
			  Anote em algum lugar seguro: <b>".$sNovaSenha."</b><br/>";
			  

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
				$mail->setFrom('recorrente@benfeitoria.com', 'Recorrente');
				$mail->addReplyTo('recorrente@benfeitoria.com', 'Recorrente');
				$mail->addTo($umUsuario->getEmail(), $umUsuario->getNome());
				$mail->setSubject("Alteração de sua senha confirmada!");
			
				$transport = new SmtpTransport();
				$transport->setOptions( $options );
				$transport->send($mail);
				// Log
				$this->umaSeguranca->registraLog("alteracao senha","senha trocada com sucesso.");
				// Inutiliza a chave para que o usuário não aproveite o link
				$this->umaSeguranca->inutilizaChaveRecuperacaoSenha($sChave);
				// Direciona para página de login
				return $this->redirect()->toURL($this->umRenderer->basePath('/login/rec-sucesso'));
			}
			else {
				// Chave inválida ou em uso
				return $this->redirect()->toURL($this->umRenderer->basePath('/seguro/recuperarSenhaAviso/chave-invalida'));
			}			

		}
		else {
			return $this->redirect()->toURL($this->umRenderer->basePath('/seguro/recuperarSenhaAviso/inexistente'));
		}
	}



}
