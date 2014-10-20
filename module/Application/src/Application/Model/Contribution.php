<?php
	// Classe Contribution
	
	namespace Application\Model;
	
	use Application\Model\ContributionDB;
	use Zend\Session\Container;
	
	class Contribution {

    protected $oServiceManager;
    protected $oSessionManager;
    protected $oContainer;
    protected $config;
    protected $oContributionDB;
    protected $oController;
    protected $iId;
    protected $iUserId;
    protected $iProjectId;
    protected $fValue;
    protected $iContributionOptionId;
    protected $iPaymentId;
    protected $aData = array();
    
	
	    /**
	     * Método Construtor
	     *
	     * Inicializa os serviços (Service Manager, config), instancia a camada de conexão do objeto.
	     *
	     * @param ServiceManager $oSM
	     */
	    public function __construct($oSM,$iId = null,$aData = null) {
	    	$this->oServiceManager = $oSM;
	    	$this->config = $this->oServiceManager->get("config");	// Captura o config setado em global|local
	    	$this->oContributionDB = new ContributionDB($this->oServiceManager->get("Zend\Db\Adapter\Adapter"));
	    	$this->oSessionManager = $this->oServiceManager->get('Zend\Session\SessionManager');
	    	$this->oContainer = new Container("cfc",$this->oSessionManager); // Cria o container, usando o sessionmanager criado no Module.php
	    	if (!empty($iId)) {
	    	    $this->iId = $iId;
	    	    $this->aData = $this->oContributionDB->getAllFieldsFromDB($this->iId);
	    	}
	    	if (!empty($aData)) {
	    	    if (is_array($aData)) {
	    	        $this->aData = $aData;
	    	    }
	    	}
	    }
	    
	    
	    /**
	     * Método setController()
	     *
	     * Passa uma instância do objeto controller para a classe.
	     *
	     * @param Controller $oController
	     * @return null
	     */
	    public function setController($oController) {
	    	$this->oController = $oController;
	    }
	    



	    /**
	     * Método getId()
	     *
	     * Retorna o ID da contribuição
	     *
	     * @return array
	     */
	    public function getId() {
	    	return $this->iId;
	    }
	    
	    
	    
	    /**
	     * Método getAllProperties()
	     *
	     * Retorna uma coleção de propriedades do projeto.
	     *
	     * @return array
	     */
	    public function getAllProperties() {
	    	return $this->aData;
	    }



	    /**
	     * Método getProperty()
	     *
	     * Retorna o valor de uma propriedade do projeto, baseado em sua chave.
	     *
	     * @param string $sKey
	     * @return string
	     */
	    public function getProperty($sKey) {
	    	return $this->aData[$sKey];
	    }
	    
	    
	    
	    /**
	     * Método setProperty()
	     *
	     * Registra um valor para uma propriedade do projeto, baseado em sua chave.
	     *
	     * @param string $sKey
	     * @param string $sValue
	     * @return null
	     */
	    public function setProperty($sKey,$sValue) {
	    	$this->aData[$sKey] = $sValue;
	    }



	    /**
	     * Método cancelByUserRequest()
	     *
	     * Registra um valor para uma propriedade do projeto, baseado em sua chave.
	     *
	     * @param string $sKey
	     * @param string $sValue
	     * @return null
	     */
	    public function cancelByUserRequest() {
	    	if (!isset($this->oContributionDB)) {
	    		$this->oContributionDB = new ContributionDB($this->dbAdapter);
	    	}
	    	if ($this->oContributionDB->cancelByUser($this->iId)) {
	    	    return true;
	    	}
	    	else {
	    	    return false;
	    	}
	    }
	     
	    
	    



	    /**
	     * Método saveData()
	     *
	     * Grava todos os dados da contribuição no banco de dados.
	     *
	     * @return int/boolean
	     */
	    public function saveData() {
	    	if (!isset($this->oContributionDB)) {
	    		$this->oContributionDB = new ContributionDB($this->dbAdapter);
	    	}
	    	$iReturn = $this->oContributionDB->saveAllFieldsIntoDB($this->iId,$this->aData);
	    	if ($iReturn!==false) {
	    	    if (is_numeric($iReturn)) {
	    	      $this->iId = $iReturn;
	    	    }
	    		 return true;
	    	}
	    	else {
	    		return false;
	    	}
	    }
	     
	    
	    
	    
	    
	}