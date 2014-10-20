<?php
	// Classe Project
	
	namespace Application\Model;
	
	use Application\Model\ProjectDB;
	use Application\Model\Contribution;
	use Zend\Session\Container;
	
	class Project {

    protected $oServiceManager;
    protected $dbAdapter;
    protected $oSessionManager;
    protected $oContainer;
    protected $config;
    protected $oProjectDB;
    protected $oController;
    protected $iProjectId = null;
    protected $aData = array();
	
	    /**
	     * Método Construtor
	     *
	     * Inicializa os serviços (Service Manager, config), instancia a camada de conexão do objeto.
	     *
	     * @param ServiceManager $umSM
	     * @param int $iProjectId
	     * @param array $aData
	     */
	    public function __construct($umSM,$iProjectId = null,$aData = null) {
	    	$this->oServiceManager = $umSM;
	    	$this->dbAdapter = $this->oServiceManager->get("Zend\Db\Adapter\Adapter");
	    	$this->config = $this->oServiceManager->get("config");	// Captura o config setado em global|local
	    	$this->oProjetoBD = new ProjectDB($this->oServiceManager->get("Zend\Db\Adapter\Adapter"));
	    	$this->oSessionManager = $this->oServiceManager->get('Zend\Session\SessionManager');
	    	$this->oContainer = new Container("cfc",$this->oSessionManager); // Cria o container, usando o sessionmanager criado no Module.php
	    	if (!empty($iProjectId)) {
	    	    $this->iProjectId = $iProjectId;
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
	     * Método getId()
	     *
	     * Retorna uma coleção de propriedades do projeto.
	     *
	     * @return array
	     */
	    public function getId() {
	    	return $this->iProjectId;
	    }



	    /**
	     * Método loadByShortURL()
	     *
	     * Verifica se o projeto existe baseado em sua url curta. Se existir, carrega o mesmo
	     *
	     * @return array
	     */
	    public function loadByShortURL($sShortURL) {
	        if (!isset($this->oProjectDB)) {
	            $this->oProjectDB = new ProjectDB($this->dbAdapter);
	        }
	        $iProjectId =  $this->oProjectDB->existByShortURL($sShortURL);
	        if ($iProjectId!==false) {
	            $this->iProjectId = $iProjectId;
	            // Carrega todos os dados do projeto
	            $this->aData = $this->loadData();
	            return $iProjectId;	            
	        }
	        else {
	            return false;
	        }
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
	     * Método getAllCompanyTypes()
	     *
	     * Retorna uma estrutura array contendo os tipos de empresa do projeto.
	     *
	     * @return array
	     */
	    public function getAllCompanyTypes() {
	    	$aCompanyTypes = array();
	    	if (!isset($this->oProjectDB)) {
	    		$this->oProjectDB = new ProjectDB($this->dbAdapter);
	    	}
	    	$aCompanyTypes = $this->oProjectDB->getAllCompanyTypesFromDB($this->iProjectId);
	    	return $aCompanyTypes;
	    }



	    /**
	     * Método getAllCategories()
	     *
	     * Retorna uma estrutura array contendo as categorias que o projeto está vinculado.
	     *
	     * @return array
	     */
	    public function getAllCategories() {
	    	$aCategories = array();
	    	if (!isset($this->oProjectDB)) {
	    		$this->oProjectDB = new ProjectDB($this->dbAdapter);
	    	}
	    	$aCategories = $this->oProjectDB->getAllCategoriesFromDB($this->iProjectId);
	    	return $aCategories;
	    }



	    /**
	     * Método getAllLinks()
	     *
	     * Retorna uma estrutura array contendo os telefones vinculados ao projeto.
	     *
	     * @return array
	     */
	    public function getAllLinks() {
	    	$aLinks = array();
	    	if (!isset($this->oProjectDB)) {
	    		$this->oProjectDB = new ProjectDB($this->dbAdapter);
	    	}
	    	$aLinks = $this->oProjectDB->getAllLinksFromDB($this->iProjectId);
	    	return $aLinks;
	    }
	     


	    /**
	     * Método getAllPhones()
	     *
	     * Retorna uma estrutura array contendo os telefones vinculados ao projeto.
	     *
	     * @return array
	     */
	    public function getAllPhones() {
	      $aPhones = array();
	      if (!isset($this->oProjectDB)) {
	      	$this->oProjectDB = new ProjectDB($this->dbAdapter);
	      }
	      $aPhones = $this->oProjectDB->getAllPhonesFromDB($this->iProjectId);
	    	return $aPhones;
	    }



	    /**
	     * Método getAllGoals()
	     *
	     * Retorna uma estrutura array contendo as metas vinculadas ao projeto.
	     *
	     * @return array
	     */
	    public function getAllGoals($bReverse = false) {
	    	$aGoals = array();
	    	if (!isset($this->oProjectDB)) {
	    		$this->oProjectDB = new ProjectDB($this->dbAdapter);
	    	}
	    	$aGoals = $this->oProjectDB->getAllGoalsFromDB($this->iProjectId,$bReverse);
	    	return $aGoals;
	    }



	    /**
	     * Método getAllContributionValuesTypes()
	     *
	     * Retorna uma estrutura array contendo os possíveis valores de contribuição do projeto.
	     *
	     * @return array
	     */
	    public function getAllContributionValuesTypes() {
	    	$aCompanyTypes = array();
	    	if (!isset($this->oProjectDB)) {
	    		$this->oProjectDB = new ProjectDB($this->dbAdapter);
	    	}
	    	$aCompanyTypes = $this->oProjectDB->getAllContributionValuesTypesFromDB($this->iProjectId);
	    	return $aCompanyTypes;
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
	     * Método setContributionOptions()
	     *
	     * Registra as opções de contribuição, por seus ids.
	     *
	     * @param string $sKey
	     * @param string $sValue
	     * @return null
	     */
	    public function setContributionOptions($aIdsContributionOptions) {
        if (!isset($this->oProjectDB)) {
	    		$this->oProjectDB = new ProjectDB($this->dbAdapter);
	    	}
	    	$this->oProjectDB->setContributionsOptions($this->iProjectId,$aIdsContributionOptions);
	    	return true;
	    }
	     


	    /**
	     * Método getProjectId()
	     *
	     * Retorna o ID do projeto.
	     *
	     * @return int
	     */
	    public function getProjectId() {
	    	  return $this->iProjectId;
	    }



	    /**
	     * Método getTotalAmountCollected()
	     *
	     * Retorna o valor total arrecadado no projeto.
	     *
	     * @return int
	     */
	    public function getTotalAmountCollected() {
	        if (!isset($this->oProjectDB)) {
	        	$this->oProjectDB = new ProjectDB($this->dbAdapter);
	        }
	        $fTotal = $this->oProjectDB->getTotalAmountCollectedFromDB($this->iProjectId);
	    	  return $fTotal;
	    }



	    /**
	     * Método getAllContributors()
	     *
	     * Retorna uma coleção de usuários que contribuiram ao projeto (sem repetição).
	     *
	     * @return int
	     */
	    public function getAllContributors() {
	      $aUsers = array();
	    	if (!isset($this->oProjectDB)) {
	    		$this->oProjectDB = new ProjectDB($this->dbAdapter);
	    	}
	    	$aIds = $this->oProjectDB->getAllContributorsFromDB($this->iProjectId);
	    	foreach ($aIds as $iIdUser) {
	    	    array_push($aUsers,new Perfil($this->oServiceManager,$iIdUser));
	    	}
	    	return $aUsers;
	    }



	    /**
	     * Método getLastContributors()
	     *
	     * Retorna uma coleção contendo os últimos N usuários que contribuiram ao projeto (sem repetição).
	     *
	     * @return int
	     */
	    public function getLastContributors($iCount) {
	        $aUsers = array();
	        if (!isset($this->oProjectDB)) {
	            $this->oProjectDB = new ProjectDB($this->dbAdapter);
	        }
	        $aIds = $this->oProjectDB->getLastContributorsFromDB($this->iProjectId,$iCount);
	        foreach ($aIds as $iIdUser) {
	            array_push($aUsers,new Perfil($this->oServiceManager,$iIdUser));
	        }
	        return $aUsers;
	    }
	     


	    /**
	     * Método getActualGoal()
	     *
	     * Retorna a meta em vigor.
	     *
	     * @return int
	     */
	    public function getActualGoal() {
	    	if (!isset($this->oProjectDB)) {
	    		$this->oProjectDB = new ProjectDB($this->dbAdapter);
	    	}
	    	$fTotalAmount = $this->getTotalAmountCollected()+0;;
	    	$bReverse = true;
	    	$aGoals = $this->getAllGoals($bReverse);
//print($fTotalAmount);print("<pre>"); print_r($aGoals); exit;
	    	$aActualGoal = $aGoals[0];
	    	foreach ($aGoals as $aGoal) {
//print($aGoal["reference"].">".$fTotalAmount."?");
	    	    if  ( ($aGoal["reference"]+0)>$fTotalAmount ) {
//print("SIM!<br>");	    	        
	    	        $aActualGoal = $aGoal;
	    	    }
	    	    else {
//print("NÃO...<br>");
	    	        
	    	    }
	    	}
//print($aActualGoal["reference"]);exit;
	    	return $aActualGoal;
	    }



	    /**
	     * Método getTotalViews()
	     *
	     * Retornao total de views do projeto
	     *
	     * @return int
	     */
	    public function getTotalViews() {
	    	if (!isset($this->oProjectDB)) {
	    		$this->oProjectDB = new ProjectDB($this->dbAdapter);
	    	}
	    	return $this->oProjectDB->getViewCount($this->iProjectId);
	    }
	    
	     


	    /**
	     * Método increaseViewCount()
	     *
	     * Acrescenta um registro de view ao acumulador do projeto.
	     *
	     * @return int
	     */
	    public function increaseViewCount() {
	    	if (!isset($this->oProjectDB)) {
	    		$this->oProjectDB = new ProjectDB($this->dbAdapter);
	    	}
	    	$iTotalViews = $this->oProjectDB->addViewRegister($this->iProjectId);
	    	return $iTotalViews;
	    }



	    /**
	     * Método getUserContribution()
	     *
	     * Retorna a contribuição que um determinado usuário fez no projeto.
	     *
	     * @param string $iUserId
	     * @return string
	     */
	    public function getUserContribution($iUserId) {
	    	$iContributionId = null;
	    	$iContributionId = $this->oProjectDB->getLastUserContributionFromDB($this->iProjectId,$iUserId);
	    	if (!empty($iContributionId)) {
	    		$oContribution = new Contribution($this->oServiceManager,$iContributionId);
	    		return $oContribution;
	    	}
	    	else {
	    	    return null;
	    	}
	    }



	    /**
	     * Método getUserDelayedContributions()
	     *
	     * Retorna as contribuições atrasadas.
	     *
	     * @param string $iUserId
	     * @return string
	     */
	    public function getUserDelayedContributions($iUserId) {
	        $iContributionId = null;
	        $iContributionId = $this->oProjectDB->getUserDelayedContributionsFromDB($this->iProjectId,$iUserId);
	        if (!empty($iContributionId)) {
	            $oContribution = new Contribution($this->oServiceManager,$iContributionId);
	            return $oContribution;
	        }
	        else {
	            return null;
	        }
	    }



	    /**
	     * Método changeStatus()
	     *
	     * Altera diretamente o status do projeto.
	     *
	     * @param int $iStatusId
	     * @return int
	     */
	    public function changeStatus($iStatusId) {
	        if (!isset($this->oProjectDB)) {
	            $this->oProjectDB = new ProjectDB($this->dbAdapter);
	        }
	        if ($this->oProjectDB->changeStatusFromDB($this->iProjectId,$iStatusId)) {
	            return true;
	        }
	        else {
	            return false;
	        }
	    }
	     


	    /**
	     * Método loadData()
	     *
	     * Carrega todos os dados de um projeto.
	     *
	     * @return int
	     */
	    private function loadData() {
	        if (!isset($this->oProjectDB)) {
	        	$this->oProjectDB = new ProjectDB($this->dbAdapter);
	        }
	        $this->aData = $this->oProjectDB->getAllFieldsFromDB($this->iProjectId);
	    	  return $this->aData;
	    }



	    /**
	     * Método saveData()
	     *
	     * Grava todos os dados do projeto no banco de dados.
	     *
	     * @return int
	     */
	    public function saveData() {
	    	if (!isset($this->oProjectDB)) {
	    		$this->oProjectDB = new ProjectDB($this->dbAdapter);
	    	}
	    	if ($this->oProjectDB->saveAllFieldsIntoDB($this->iProjectId,$this->aData)) {
	    	    return true;
	    	}
	    	else {
	    	    return false;
	    	}
	    }
	     
	
	
	
	}