<?php
	namespace Application\Model;
	
	/**
		* Classe CityDB
		*
		* Responsável por operações em banco de dados. 
		* 
		* Sempre usar ANSI SQL 
		*/
	class CityDB {
	
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
			* Método loadData()
			*
			* Captura os dados da cidade.
			* 
			* @param string $iCityId
			* @return array
			*/
		public function loadData($iCityId) {
			$sQuery = "select c.city,c.state,c.id_country,co.country_name from cities as c inner join countries as co on co.id=c.id_country where c.id='" . $iCityId . "'";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				$linha = $umResultSet->next();
				return $linha;
			}
		}
	}