<?php
	// Classe ProjectListDB
	
	namespace Application\Model;
	
	use Zend\Session\Container;
	
	class ProjectListDB {
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
		 * Método getAllProjectsByStatusFromDB()
		 *
		 * Retorna uma lista de projetos, conforme os filtros passados
		 *
		 * @param string $sStatus
		 * @param array $aKeys
		 * @param int $iCategoryId
		 * @param int $iQtd
		 * @return int
		 */
		public function getAllProjectsByStatusFromDB($iIdStatus,$aKeys = null,$iCategoryId = null,$iQtd = null) {
		    $aData = array();
        $sQuerySearchKeys = "";
        $sQuerySearchCategory = "";
		    if (!empty($aKeys)) {
		        if (count($aKeys)>0) {
		            $sQuerySearchKeys .= " and (";
		            foreach ($aKeys as $sKey) {
		                $sQuerySearchKeys .= "lower(c.title) like '%".mb_strtolower($sKey)."%' or lower(c.about_project) like '%".mb_strtolower($sKey)."%' or ";
		            }
		            $sQuerySearchKeys = substr($sQuerySearchKeys,0,-4).") ";
		        }
		    }
		    if (!empty($iCategoryId)) {
		        $sQuerySearchCategory = " inner join projects_categories as pc on pc.id_project=c.id and pc.id_category=".$iCategoryId;
		    }
	      $sQuery = "select c.id from projects as c".$sQuerySearchCategory." where c.id_status='".$iIdStatus."'".$sQuerySearchKeys." order by c.order_number asc";
	      if (!empty($iQtd)) {
	          if (is_numeric($iQtd)) {
	              $sQuery .= " LIMIT 0,".$iQtd;
	          }
	      }
	      if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
	          while ($linha = $umResultSet->next()) {
	          	array_push($aData,$linha["id"]);
	          }
	      }
	      return $aData;
		}
	}