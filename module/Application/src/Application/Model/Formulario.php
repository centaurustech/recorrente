<?php
	// Classe Formulario
	
	namespace Application\Model;
	
	use Application\Model\FormularioBD;
	use Zend\Session\Container;
	
	class Formulario {

    protected $umServiceManager;
    protected $umSessionManager;
    protected $umContainer;
    protected $config;
    protected $umFormularioBD;
    protected $umController;
	
	    /**
	     * Método Construtor
	     *
	     * Inicializa os serviços (Service Manager, config), instancia a camada de conexão do objeto.
	     *
	     * @param ServiceManager $umSM
	     */
	    public function __construct($umSM) {
	    	$this->umServiceManager = $umSM;
	    	$this->config = $this->umServiceManager->get("config");	// Captura o config setado em global|local
	    	$this->umFormularioBD = new FormularioBD($this->umServiceManager->get("Zend\Db\Adapter\Adapter"));
	    	$this->umSessionManager = $this->umServiceManager->get('Zend\Session\SessionManager');
	    	$this->umContainer = new Container("cfc",$this->umSessionManager); // Cria o container, usando o sessionmanager criado no Module.php
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
	     * Método getCountries()
	     *
	     * Passa uma instância do objeto controller para a classe.
	     *
	     * @param boolean $bByPriority
	     * @return null
	     */
	    public function getCountries($bByPriority = false) {
	    	$aData = $this->umFormularioBD->getAllCountriesOrderedByName($bByPriority);
	    	return $aData;
	    }


	    /**
	     * Método getCountryStates()
	     *
	     * Passa uma instância do objeto controller para a classe.
	     *
	     * @param int $iIdCountry
	     * @return null
	     */
	    public function getCountryStates($iIdCountry) {
	    	$aData = $this->umFormularioBD->getAllStatesByCountry($iIdCountry);
	    	return $aData;
	    }


	    /**
	     * Método getCitiesByState()
	     *
	     * Passa uma instância do objeto controller para a classe.
	     *
	     * @param string $sState
	     * @return null
	     */
	    public function getCitiesByState($sState) {
	    	$aData = $this->umFormularioBD->getAllCitiesByState($sState);
	    	return $aData;
	    }


	    /**
	     * Método getCompanyTypes()
	     *
	     * Passa uma instância do objeto controller para a classe.
	     *
	     * @return null
	     */
	    public function getCompanyTypes() {
	    	$aData = $this->umFormularioBD->getAllCompanyTypes();
	    	return $aData;
	    }


	    /**
	     * Método getCategories()
	     *
	     * Captura todas as variações de categoria de projeto.
	     *
	     * @return null
	     */
	    public function getCategories() {
	    	$aData = $this->umFormularioBD->getAllCategories();
	    	return $aData;
	    }
	     

	    /**
	     * Método emailExists()
	     *
	     * Passa uma instância do objeto controller para a classe.
	     *
	     * @param string $sEmail
	     * @return null
	     */
	    public function emailExists($sEmail) {
	      $bReturn = false;
	    	$bReturn = $this->umFormularioBD->checkIfEmailExists($sEmail);
	    	return $bReturn;
	    }


	    /**
	     * Método activeAccount()
	     *
	     * Ativa uma conta.
	     *
	     * @param string $sCode
	     * @return null
	     */
	    public function activeAccount($sCode) {
	    	$bReturn = false;
	    	$bReturn = $this->umFormularioBD->activeAccount($sCode);
	    	return $bReturn;
	    }
	     

	    
	    
	    
	     
	}