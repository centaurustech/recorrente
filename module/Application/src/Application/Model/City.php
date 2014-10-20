<?php
	// Classe City
	
	namespace Application\Model;
	
	use Application\Model\CityDB;
	
	class City {

    protected $oServiceManager;
    protected $dbAdapter;
    protected $config;
    protected $oProjectDB;
    protected $iCityId;
    protected $sCityName;
    protected $sState;
    protected $sCountry;
    protected $sCountryName;
    protected $sCountryInternationalCode;
	

		/**
	     * Método Construtor
	     *
	     * Inicializa os serviços (Service Manager, config), instancia a camada de conexão do objeto.
	     *
	     * @param ServiceManager $umSM
	     * @param int $iProjectId
	     * @param array $aData
	     */
	    public function __construct($umSM,$iCityId = null) {
	        $this->oServiceManager = $umSM;
	        $this->config = $this->oServiceManager->get("config");	// Captura o config setado em global|local
	        $this->oProjectDB = new CityDB($this->oServiceManager->get("Zend\Db\Adapter\Adapter"));
	        if (!empty($iCityId)) {
	            $aData = $this->oProjectDB->loadData($iCityId);
	           $this->sCityName = $aData["city"];
	           $this->sState = $aData["state"];
	           $this->sCountry = $aData["id_country"];
	           $this->sCountryName = $aData["country_name"];
	           $this->iCityId = $iCityId;
	        }
	    }

	    /**
	     * @return the $iCityId
	     */
	    public function getCityId() {
	    	return $this->iCityId;
	    }
	    
	    /**
	     * @return the $sState
	     */
	    public function getState() {
	    	return $this->sState;
	    }
	    
	    /**
	     * @return the $sCityName
	     */
	    public function getCityName() {
	    	return $this->sCityName;
	    }
	    
	    /**
	     * @param field_type $sCityName
	     */
	    public function setCityName($sCityName) {
	    	$this->sCityName = $sCityName;
	    }
	    
	    /**
	     * @return the $sCountry
	     */
	    public function getCountry() {
	    	return $this->sCountry;
	    }

	    /**
	     * @return the $sCountry
	     */
	    public function getCountryName() {
	    	return $this->sCountryName;
	    }
	     
	    /**
	     * @param field_type $iCityId
	     */
	    public function setCityId($iCityId) {
	    	$this->iCityId = $iCityId;
	    }
	    
	    /**
	     * @param field_type $sState
	     */
	    public function setState($sState) {
	    	$this->sState = $sState;
	    }
	    
	    /**
	     * @param field_type $sCountry
	     */
	    public function setCountry($sCountry) {
	    	$this->sCountry = $sCountry;
	    }
	     
	
	
	}