<?php
	// Classe ProjectStatus
	
	namespace Application\Model;
	
	use Application\Model\ProjectStatusDB;
	use Zend\Session\Container;
	
	class ProjectStatus {

    protected $oServiceManager;
    protected $dbAdapter;
    protected $oSessionManager;
    protected $oContainer;
    protected $config;
    protected $oProjectStatusDB;
    protected $oController;
    protected $iStatusId = null;
    protected $aData = array();
	
	    /**
	     * Método Construtor
	     *
	     * Inicializa os serviços (Service Manager, config), instancia a camada de conexão do objeto.
	     *
	     * @param ServiceManager $umSM
	     * @param int $iStatusId
	     * @param array $aData
	     */
	    public function __construct($umSM,$iStatusId = null,$aData = null) {
	    	$this->oServiceManager = $umSM;
	    	$this->dbAdapter = $this->oServiceManager->get("Zend\Db\Adapter\Adapter");
	    	$this->config = $this->oServiceManager->get("config");	// Captura o config setado em global|local
	    	$this->oProjetoBD = new ProjectStatusDB($this->oServiceManager->get("Zend\Db\Adapter\Adapter"));
	    	$this->oSessionManager = $this->oServiceManager->get('Zend\Session\SessionManager');
	    	$this->oContainer = new Container("cfc",$this->oSessionManager); // Cria o container, usando o sessionmanager criado no Module.php
	    	if (!empty($iStatusId)) {
	    	    $this->iStatusId = $iStatusId;
	    	    // Carrega todos os dados do projeto
	    	    $this->aData = $this->loadData();
	    	}
	    	if (!empty($aData)) {
	    	    $this->aData = $aData;
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
	    public function setController($oController) {
	    	  $this->oController = $oController;
	    }



	    /**
	     * Método loadData()
	     *
	     * Carrega todos os dados.
	     *
	     * @return int
	     */
	    private function loadData() {
	    	if (!isset($this->oProjectStatusDB)) {
	    		$this->oProjectStatusDB = new ProjectStatusDB($this->dbAdapter);
	    	}
	    	return $this->oProjectStatusDB->getAllFieldsFromDB($this->iStatusId);
	    }
	     


	    /**
	     * Método getId()
	     *
	     * Retorna uma coleção de propriedades.
	     *
	     * @return array
	     */
	    public function getId() {
	    	return $this->iProjectId;
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
	     	     
	}
	
	
	
