<?php
namespace Usuario\Controller;

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
    use Application\Model\ProjectStatus;
    use Application\Model\Contribution;
    use Application\Model\City;
    
class UsuarioController extends AbstractActionController {

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
    	$this->umContainer = new Container("cfc",$this->umSessionManager); // Cria o container, usando o sessionmanager criado no Module.php
    	$this->umaSeguranca = new Seguranca($this->umServiceManager); // Instancia o gerenciador de segurança
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

	
	
	
	
	/* Página do perfil do usuário */
  public function perfilAction() {
        $this->init();
        $this->layout("v1/layout/moldura");
        $this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
        
        $umForm = new Formulario($this->umServiceManager);
        $oUser = new Perfil($this->umServiceManager,$this->umContainer->iIdUsuarioSiteLogado);
        
        // Captura dados do endereço do usuário
        $aEndereco = $oUser->getEndereco("residencial");
        $iCityId = $aEndereco["id_city"];
        // Captura o Estado
        $oCity = new City($this->umServiceManager,$iCityId);
        $umViewModel = new ViewModel();
        $umViewModel->setTemplate("v1/usuario/perfil");
        $umViewModel->setVariable("oUser", $oUser);
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
        
        return $umViewModel;
  }





  /* Página do perfil do usuário */
  public function pagamentosAction() {
  	$this->init();
  	$this->layout("v1/layout/moldura");
  	$this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
  
  	$oUser = new Perfil($this->umServiceManager,$this->umContainer->iIdUsuarioSiteLogado);
  
  	$umViewModel = new ViewModel();
  	$umViewModel->setTemplate("v1/usuario/pagamentos");
  	$umViewModel->setVariable("oSM", $this->umServiceManager);
  	$umViewModel->setVariable("oUser", $oUser);
  
  	return $umViewModel;
  }





  /* Página com a relação de projetos do usuário */
  public function projetosAction() {
  	$this->init();
  	$this->layout("v1/layout/moldura");
  	$this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
  
  	$oUser = new Perfil($this->umServiceManager,$this->umContainer->iIdUsuarioSiteLogado);
  
  	$umViewModel = new ViewModel();
  	$umViewModel->setTemplate("v1/usuario/projetos");
  	$umViewModel->setVariable("oSM", $this->umServiceManager);
  	$umViewModel->setVariable("oUser", $oUser);
  
  	return $umViewModel;
  }





  /* Página com a relação de projetos do usuário */
  public function projetosValoresRecebidosAction() {
  	$this->init();
  	$this->layout("v1/layout/moldura");
  	$this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
  
  	$oUser = new Perfil($this->umServiceManager,$this->umContainer->iIdUsuarioSiteLogado);
  	
  	$aPTBRMonths = array(1 => "janeiro",2 => "fevereiro",3 => "março",4 => "abril",5 => "maio",6 => "junho",7 => "julho",8 => "agosto",9 => "setembro",10 => "outubro",11 => "novembro",12 => "dezembro");
  	
  	$iFutureMonth = (int)date("n",strtotime("+1 month"));
  	$iFutureYear = date("Y",strtotime("+1 month"));
  	$sNextMonthDay = ucfirst($aPTBRMonths[$iFutureMonth])."/".$iFutureYear;
  
  	$umViewModel = new ViewModel();
  	$umViewModel->setTemplate("v1/usuario/projetos-valores-recebidos");
  	$umViewModel->setVariable("oSM", $this->umServiceManager);
  	$umViewModel->setVariable("oUser", $oUser);
  	$umViewModel->setVariable("sNextMonthDate", $sNextMonthDay);
  	$umViewModel->setVariable("iFutureMonth", $iFutureMonth);
  	$umViewModel->setVariable("iFutureYear", $iFutureYear);
  	 
  	return $umViewModel;
  }





  /* Permite ao usuário ler uma mensagem */
  public function lerMensagemAction() {
        $this->init();
      	if ($this->umaSeguranca->sessaoAtiva($this)) {
      	    $this->layout("v1/layout/moldura");
            $this->layout()->setVariable("iIdUsuarioLogadoSite",$this->umContainer->iIdUsuarioSiteLogado);
            
            $oUser = new Perfil($this->umServiceManager,$this->umContainer->iIdUsuarioSiteLogado);
            
            $umViewModel = new ViewModel();
            $umViewModel->setTemplate("v1/usuario/ler-mensagem");
            $umViewModel->setVariable("oSM", $this->umServiceManager);
            $umViewModel->setVariable("oUser", $oUser);
            
            return $umViewModel;
        }
        else {
            // Encaminha para a tela de login
            $pageURL = @$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
            $pageURL .= $_SERVER['SERVER_PORT'] != '80' ? $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"] : $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
            return $this->redirect()->toURL($this->umRenderer->basePath('/login/u='.base64_encode($pageURL)));
        }
}
  


}
