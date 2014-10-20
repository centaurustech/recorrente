<?php
	// Classe ProjectStatusDB
	
	namespace Application\Model;
	
	use Zend\Session\Container;
	
	class ProjectStatusDB {
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
		 * Método getAllFieldsFromDB()
		 *
		 * Grava todos os dados do projeto no banco de dados.
		 *
		 * @return int
		 */
		public function getAllFieldsFromDB($iStatusId) {
		    $aData = array();
	      $sQuery = "select * from status where id='".$iStatusId."'";
	      if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
	          $aData = $oResultSet->next();
	      }
	      return $aData;
		}
	
	}
	
	