<?php
	// Classe Project
	
	namespace Application\Model;
	
	use Application\Model\ProjectListDB;
	use Zend\Session\Container;
	use Application\Model\Project;
	
	class ProjectList {

    protected $oServiceManager;
    protected $dbAdapter;
    protected $oSessionManager;
    protected $oContainer;
    protected $config;
    protected $oProjectListDB;
    protected $oController;
	
	    /**
	     * Método Construtor
	     *
	     * Inicializa os serviços (Service Manager, config), instancia a camada de conexão do objeto.
	     *
	     * @param ServiceManager $umSM
	     * @param int $iProjectId
	     * @param array $aData
	     */
	    public function __construct($umSM) {
	    	$this->oServiceManager = $umSM;
	    	$this->dbAdapter = $this->oServiceManager->get("Zend\Db\Adapter\Adapter");
	    	$this->config = $this->oServiceManager->get("config");	// Captura o config setado em global|local
	    	$this->oProjectListDB = new ProjectListDB($this->oServiceManager->get("Zend\Db\Adapter\Adapter"));
	    	$this->oSessionManager = $this->oServiceManager->get('Zend\Session\SessionManager');
	    	$this->oContainer = new Container("cfc",$this->oSessionManager); // Cria o container, usando o sessionmanager criado no Module.php
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
	     * Método getProjectsByStatus()
	     *
	     * Retorna uma coleção de instâncias de projeto filtradas pelo seu status.
	     *
	     * @param Controller $umController
	     * @return null
	     */
	    public function getProjectsByStatus($iIdStatus,$aKeyWords = null,$iCategoryId = null,$iQtd = null) {
	        $aProjects = array();
	        if (!isset($this->oProjectListDB)) {
	        	$this->oProjectListDB = new ProjectListDB($this->dbAdapter);
	        }
	        $aProjectsIds = $this->oProjectListDB->getAllProjectsByStatusFromDB($iIdStatus,$aKeyWords,$iCategoryId,$iQtd);
	        foreach ($aProjectsIds as $iProjectId) {
	            array_push($aProjects,new Project($this->oServiceManager,$iProjectId));
	        }
	        return $aProjects; 
	    }
	     
	    

	}
