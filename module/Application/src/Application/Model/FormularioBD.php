<?php
	// Classe FormularioBD
	
	namespace Application\Model;
	
	use Zend\Session\Container;
	
	class FormularioBD {
	    protected $dbAdapter;
	     
			
		/**
			* Método Construtor
			*
			* Recebe uma instância de dbAdapter e armazena localmente para utilização dos métodos.
			* 
			* @param dbAdapter $dbAdapter
			*/
		public function __construct(\Zend\Db\Adapter\Adapter $dbAdapter) {
			$this->dbAdapter = $dbAdapter;
		}
		
		
		
		/**
			* Método getAllCountriesOrderedByName
			*
			* Captura todos os países ordenados pelo nome
			* 
			* @param dbAdapter $dbAdapter
			*/
		public function getAllCountriesOrderedByName($bByPriority) {
		    $aData = array();
		    $sQuery = "select id,country_name from countries";
		    if ($bByPriority) {
		        $sQuery .= " order by priority,country_name";
		    }
		    else {
		        $sQuery .= " order by country_name";
		    }
		    if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
		    	while ($line = $umResultSet->next()) {
		    	    array_push($aData,array("id"=>$line["id"],"name"=>$line["country_name"]));
		    	}
		    	return $aData;
		    }		    
		}



		/**
		 * Método getAllStatesByCountry
		 *
		 * Captura todos os países ordenados pelo nome
		 *
		 * @param int $iIdCountry
		 */
		public function getAllStatesByCountry($iIdCountry) {
			$aData = array();
			$sQuery = "select distinct(state) from cities order by state asc";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($line = $umResultSet->next()) {
					array_push($aData,array("name"=>$line["state"]));
				}
				return $aData;
			}
		}



		/**
		 * Método getAllCitiesByState
		 *
		 * Captura todos os países ordenados pelo nome
		 *
		 * @param string $sState
		 */
		public function getAllCitiesByState($sState) {
			$aData = array();
			$sQuery = "select id,city from cities where state='".$sState."' order by city asc";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($line = $umResultSet->next()) {
					array_push($aData,array("id"=>$line["id"], "name"=>$line["city"]));
				}
				return $aData;
			}
		}



		/**
		 * Método getAllCompanyTypes
		 *
		 * Captura todos os tipos de empresa disponíveis.
		 *
		 * @param dbAdapter $dbAdapter
		 */
		public function getAllCompanyTypes() {
			$aValues = array();
			$sQuery = "select id,title from company_types";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($line = $umResultSet->next()) {
					array_push($aValues,array("id"=>$line["id"],"title"=>$line["title"]));
				}
				return $aValues;
			}
		}



		/**
		 * Método getAllCategories
		 *
		 * Captura todas as categorias de projetos
		 *
		 * @param dbAdapter $dbAdapter
		 */
		public function getAllCategories() {
			$aValues = array();
			$sQuery = "select id,title from categories";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($line = $umResultSet->next()) {
					array_push($aValues,array("id"=>$line["id"],"title"=>$line["title"]));
				}
				return $aValues;
			}
		}
		


		/**
		 * Método checkIfEmailExists
		 *
		 * Verifica se o email já existe no banco de dados
		 *
		 * @param string $sEmail
		 */
		public function checkIfEmailExists($sEmail) {
			$sQuery = "select id from users where email='" . $sEmail . "'";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				if ($umResultSet->count()>0) {
				    return true;
				}
				else {
				    return false;
				}
			}
		}



		/**
		 * Método activeAccount
		 *
		 * Verifica se o email já existe no banco de dados
		 *
		 * @param string $sCode
		 */
		public function activeAccount($sCode) {
			$sQuery = "update users set status='ativo' where activation_id='" . $sCode . "'";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				if ($umResultSet->getAffectedRows()>0) {
					return true;
				}
				else {
					return false;
				}
			}
		}
		
		
		
		
		
	}