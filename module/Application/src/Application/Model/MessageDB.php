<?php
	namespace Application\Model;
	
	/**
		* Classe MessageDB
		*
		* Responsável por operações em banco de dados. 
		* 
		* Sempre usar ANSI SQL 
		*/
	class MessageDB {
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
		 * Método getIdFromHash()
		 *
		 * Retorna todos os dados do banco de dados
		 *
		 * @param int $sHash
		 * @return mixed $linha
		 */
		public function getIdFromHash($sHash) {
		    $sQuery = "select id from messages where unique_hash='" . $sHash . "'";
		    if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
		        $linha = $umResultSet->next();
		    }
		    return $linha["id"];
		}
		





		
		
		/**
			* Método loadAllDataFromDB()
			*
			* Retorna todos os dados do banco de dados
			* 
			* @param int $iId
			* @return mixed $linha
			*/
		public function loadAllDataFromDB($iIdMessage) {
			$sQuery = "select * from messages where id=" . $iIdMessage;
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				$linha = $umResultSet->next();
			}
			return $linha;
		}


	}