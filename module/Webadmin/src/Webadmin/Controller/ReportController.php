<?php

namespace Webadmin\Controller;

set_time_limit(0);

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

class ReportController extends AbstractActionController {

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




    /*
     * acao - menu principal dos relatorios
    */
    public function recorrenciasAction()  {
		  	// Init
		  	$this->init();
		  	$sMensagemErro = "";
		  	
		  	// Protege o acesso
		  	$this->sessaoAtiva();
		
		    // template
		    $this->layout('layout/webadmin');
		    
		
		    // variaveis do topo
		    $umPerfil = new Perfil($this->umServiceManager,null,$this->umContainer->sUsuarioAdminLogado);
		    $this->layout()->sLoginUsuario = ucfirst($umPerfil->getNome());	// Seta uma variável no layout diretamente.
		    $this->layout()->sTempoSessaoTela = $this->config["session"]["expira-tela"];
		
		    $config = $this->umServiceManager->get("config");	// Captura o config setado em global|local
		
		    
		    // opcoes do select perfil de acesso
		    $oWebAdminBD = new WebAdminBD($this->umServiceManager->get("Zend\Db\Adapter\Adapter"));
			  $aIdsProjetos = $oWebAdminBD->retornaTodosProjetos();


		    //View de saída
		  	$umViewModel = new ViewModel();
  			$umViewModel->setVariable("oSm",$this->umServiceManager);
		  	$umViewModel->setVariable("aIdsProjetos",$aIdsProjetos);
		  	 
		  	return $umViewModel;
    }

    /*
     * acao - menu principal dos relatorios
    */
    public function recorrenciasFormAction()  {
		  	// Init
		  	$this->init();
		  	$sMensagemErro = "";
		  	
		  	// Protege o acesso
		  	$this->sessaoAtiva();
		
		    // template
		    $this->layout('layout/webadmin');
		
		    // variaveis do topo
		    $umPerfil = new Perfil($this->umServiceManager,null,$this->umContainer->sUsuarioAdminLogado);
		    $this->layout()->sLoginUsuario = ucfirst($umPerfil->getNome());	// Seta uma variável no layout diretamente.
		    $this->layout()->sTempoSessaoTela = $this->config["session"]["expira-tela"];
		
		    $config = $this->umServiceManager->get("config");	// Captura o config setado em global|local
		    
		    // opcoes do select perfil de acesso
		    $oWebAdminBD = new WebAdminBD($this->umServiceManager->get("Zend\Db\Adapter\Adapter"));
			  $aIdsProjetos = $oWebAdminBD->retornaTodosProjetos();
			  
			  $projetos = $this->params()->fromPost('projetos');
			  $data_inicial = $this->params()->fromPost('data_inicial');
			  $data_final = $this->params()->fromPost('data_final');
			  
			  // pegar no banco de dados todas as assinaturas dos projetos selecionados
			  $assinaturas = array();
			  foreach ($projetos as $projeto) {
			  		$temp = $oWebAdminBD->retornaTodasAssinaturasDoProjeto($projeto);
			  		foreach ($temp as $assinatura) {
			  				if ($assinatura!="") {
			  					
									  // pegar no moip todas as faturas de cada assinatura
						        $sURLRequest = "https://api.moip.com.br/assinaturas/v1/invoices/$assinatura/payments";
						        echo $sURLRequest."<br>"; 
						        $aReturnValues = $this->sendMoipInstruction($sURLRequest, "GET");
						        var_dump($aReturnValues);
						        $jsonResponse = $aReturnValues["return"];
						        echo "---------------------------------------------------------<br><br>";
				  			};
			  		};
			  };
			  
			  

		    //View de saída
		  	$umViewModel = new ViewModel();
		    $umViewModel->setTemplate('/webadmin/report/recorrencias.phtml');
		    $umViewModel->setVariable("oSm",$this->umServiceManager);
		  	$umViewModel->setVariable("aIdsProjetos",$aIdsProjetos);
		  	$umViewModel->setVariable("data_inicial",$data_inicial);
		  	$umViewModel->setVariable("data_final",$data_final);
		  	$umViewModel->setVariable("projetos",$projetos);
		  	 
		  	return $umViewModel;
    }
    
    /*
     * acao - menu principal dos relatorios
    */
    public function atualizaRecorrenciasAction()  {
		  	// Init
		  	$this->init();
		  	$sMensagemErro = "";
		  	
		  	$dbAdapter = $this->umServiceManager->get("Zend\Db\Adapter\Adapter");
		  	
		  	// Protege o acesso
		  	$this->sessaoAtiva();
		
		    // template
		    $this->layout('layout/webadmin');
		
		    // variaveis do topo
		    $umPerfil = new Perfil($this->umServiceManager,null,$this->umContainer->sUsuarioAdminLogado);
		    $this->layout()->sLoginUsuario = ucfirst($umPerfil->getNome());	// Seta uma variável no layout diretamente.
		    $this->layout()->sTempoSessaoTela = $this->config["session"]["expira-tela"];
		
		    $config = $this->umServiceManager->get("config");	// Captura o config setado em global|local
		    
		    
		    
		    
		    // 1. pegar todas as assinaturas do moip (subscriptions)
		    $url = "https://api.moip.com.br/assinaturas/v1/subscriptions"; //$this->config["moip"]["url"]."subscriptions"; //https://api.moip.com.br/assinaturas/v1/subscriptions
        $assinaturas = $this->sendMoipInstruction($url, "GET");
        $assinaturas = json_decode($assinaturas["return"], true);
        $assinaturas = $assinaturas["subscriptions"];
        //echo "<b>assinatura:</b><br><pre>"; print_r($cobrancas); echo "</pre><br><br>";
        
        
        foreach ($assinaturas as $assinatura) {
        	
        	
        		
        		// 2. pegar as faturas de cada assinatura (invoices)
        		
				    $url = "https://api.moip.com.br/assinaturas/v1/subscriptions/".$assinatura["code"]."/invoices"; // $this->config["moip"]["url"]."subscriptions/".$assinatura["code"]."/invoices"; //https://api.moip.com.br/assinaturas/v1/subscriptions/recorrente-219/invoices
		        $faturas = $this->sendMoipInstruction($url, "GET");
		        $faturas = json_decode($faturas["return"], true);
		        $faturas = $faturas["invoices"];
		        //echo "<b>faturas:</b><br><pre>"; print_r($faturas); echo "</pre><br><br>";
		        
		        foreach ($faturas as $fatura) {
		        	
		        	
		        		
		        		// 3. pegar as cobranças de cada fatura(payments)
		        		
						    $url = "https://api.moip.com.br/assinaturas/v1/invoices/".$fatura["id"]."/payments";  //$this->config["moip"]["url"]."invoices/".$fatura["id"]."/payments"; //https://api.moip.com.br/assinaturas/v1/invoices/428815/payments
				        $cobrancas = $this->sendMoipInstruction($url, "GET");
				        $cobrancas = json_decode($cobrancas["return"], true);
				        $cobrancas = $cobrancas["payments"];
				        $cobranca = $cobrancas[0];
				        
				        //echo "<b>cobrancas:</b><br><pre>"; print_r($cobrancas); echo "</pre><br><br>";
				        
				        // procurar pela assinatura e recorrencia
				        $q = "
				        select
				        id, status, invoice_code, payment_code
				        
				        from
				        contributions
				        
				        where
				        start_value='".$fatura["occurrence"]."' and
				        payment_gateway_code='".$fatura["subscription_code"]."'
				        ";
				        echo $q."<br>";
								$umResultSet = $dbAdapter->query($q)->execute();
								$quantidade = 0;
								while ($linha = $umResultSet->next()) {
									
										if ($linha["invoice_code"]=="") {
												$q = "
												update
												contributions
												
												set 
												invoice_code='".$fatura["subscription_code"]."', payment_code='".$fatura["id"]."'
												
												where
								        start_value='".$fatura["occurrence"]."' and
								        payment_gateway_code='".$fatura["subscription_code"]."'
												";
												$dbAdapter->query($q)->execute();
								        echo $q."<br>";
										};
						        // se ja existir, atualizar status
						        /*
						        [CANCELADO NO MOIP] - cobrado e recorrencia autorizada
						        aguardando atualização do endereço
						        cancelado pelo usuario
						        [ATRASADO] - cobrado e recorrencia autorizada
						        cobrado e recorrencia autorizada
						        substituida
						        suspensa por não pagamento
						        erro [Erro na requisição] - %
						        cobrado e recorrencia autorizada - ACTIVE
						        dados do cartão atualizados. Aguardando comunicação com MOIP.
						        passo 2 - aguardando preenchimento do endereço
						        
						        */
										$quantidade++;
										$status_atual = $linha["status"];
										$status_novo = $status_atual;
										switch ($cobranca["status"]["code"]) {
											 
											 case "1": 
											 		// autorizado
											 		$status_novo = "autorizado pela MOIP";
											 		
											 break;
											 
											 case "2":
											 		// iniciado
											 		$status_novo = "iniciado pela MOIP";
											 break;
											 
											 case "3":
											 		// boleto impresso
											 		$status_novo = "boleto Impresso";
											 break;
											 
											 case "4":
											 		// concluído
											 		$status_novo = "cobrado e recorrencia autorizada";
											 break;
											 
											 case "5":
											 		// cancelado
											 		$status_novo = "cancelado pelo usuario";
											 break;
											 
											 case "6":
											 		// em análise
											 		$status_novo = "em análise";
											 break;
											 
											 case "7":
											 		// estornado
											 		$status_novo = "estornado";
											 break;
											 
											 case "8":
											 		// nao tem
											 break;
											 
											 case "9":
											 		// reembolsado
											 		$status_novo = "reembolsado";
											 break;
										};
										
										if ($status_novo!=$status_atual) {
												// atualizar status da [recorrencia e assinatura]
												$q = "
												update
												contributions
												
												set 
												status='$status_novo', dt_status_change=CURRENT_TIMESTAMP()
												
												where
								        start_value='".$fatura["occurrence"]."' and
								        payment_gateway_code='".$fatura["subscription_code"]."'
												";
				        				echo $q."<br>";
												$dbAdapter->query($q)->execute();
										};
								};
								if ($quantidade==0) {
										// nao existe assinatura e recorrencia, INSERIR !
										
										// primeiro pegar dados base 
						        $q = "
						        select
						        id_user, id_project, id_payment
						        
						        from
						        contributions
						        
						        where
						        payment_gateway_code='".$fatura["subscription_code"]."'
						        
						        limit 
						        0, 1
						        ";
						        echo $q."<br>";
						        
										$umResultSet2 = $dbAdapter->query($q)->execute();
										$quantidade = 0;
										while ($linha = $umResultSet2->next()) {
												$fatura["amount"] = substr($fatura["amount"], 0, -2).".".substr($fatura["amount"], -2);
											
												$q = "
												insert into contributions
												(id_user, id_project, id_payment, dt_contribution, value_contribution, status, start_value, dt_status_change, payment_gateway_code, invoice_code, payment_code) values
												('".$linha["id_user"]."', '".$linha["id_project"]."', '".$linha["id_payment"]."', '".$cobranca["creation_date"]["year"]."-".$cobranca["creation_date"]["month"]."-".$cobranca["creation_date"]["day"]." ".$cobranca["creation_date"]["hour"].":".$cobranca["creation_date"]["minute"].":".$cobranca["creation_date"]["second"]."', '".$fatura["amount"]."', '".$cobranca["status"]["description"]."', '".$fatura["occurrence"]."', CURRENT_TIMESTAMP(), '".$fatura["subscription_code"]."', '".$fatura["subscription_code"]."', '".$fatura["id"]."')
												";
				        				echo $q."<br>";
				        				$dbAdapter->query($q)->execute();
										};

								};

		        };
		        
		        echo "---------------------------------------------------------------------<br>";
        		
        };
			  
		    //View de saída
		  	$umViewModel = new ViewModel();
		  	 
		  	return $umViewModel;
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
