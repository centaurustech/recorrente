<?php
	// Classe ProjectDB
	
	namespace Application\Model;
	
	use Zend\Session\Container;
	
	class ProjectDB {
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
		public function getAllFieldsFromDB($iProjectId) {
		    $aData = array();
	      $sQuery = "select * from projects where id='".$iProjectId."'";
	      if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
	          $aData = $oResultSet->next();
	      }
	      return $aData;
		}



		/**
		 * Método getAllCompanyTypesFromDB()
		 *
		 * Captura todos os telefones da tabela projects_phones
		 *
		 * @return int
		 */
		public function getAllCompanyTypesFromDB($iProjectId) {
			$aData = array();
			$sQuery = "select id_company_type from projects_company_types where id_project='".$iProjectId."'";
			if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($linha = $oResultSet->next()) {
					array_push($aData,$linha["id_company_type"]);
			  }
			}
			return $aData;
		}



		/**
		 * Método getAllCategoriesFromDB()
		 *
		 * Captura todos os telefones da tabela projects_phones
		 *
		 * @return int
		 */
		public function getAllCategoriesFromDB($iProjectId) {
			$aData = array();
			$sQuery = "select id_category from projects_categories where id_project='".$iProjectId."'";
			if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($linha = $oResultSet->next()) {
					array_push($aData,$linha["id_category"]);
				}
			}
			return $aData;
		}
		


		/**
		 * Método getAllPhonesFromDB()
		 *
		 * Captura todos os telefones da tabela projects_phones
		 *
		 * @return int
		 */
		public function getAllPhonesFromDB($iProjectId) {
			$aData = array();
			$sQuery = "select title,phone from projects_phones where id_project='".$iProjectId."'";
			if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($linha = $oResultSet->next()) {
				    $aData[$linha["title"]] = $linha["phone"];
				}
			}
			return $aData;
		}



		/**
		 * Método getAllLinksFromDB()
		 *
		 * Captura todos os telefones da tabela projects_phones
		 *
		 * @return int
		 */
		public function getAllLinksFromDB($iProjectId) {
			$aData = array();
			$sQuery = "select title,link from projects_links where id_project='".$iProjectId."'";
			if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($linha = $oResultSet->next()) {
					array_push($aData,array("title"=>$linha["title"],"link"=>$linha["link"]));
				}
			}
			return $aData;
		}
		


		/**
		 * Método getAllGoalsFromDB()
		 *
		 * Captura todos os telefones da tabela projects_phones
		 *
		 * @param int $iProjectId
		 * @return array
		 */
		public function getAllGoalsFromDB($iProjectId,$bReverse) {
			$aData = array();
			$sQuery = "select pr.id_rule,r.type as rule,pr.reference,pr.goal,pr.collective_reward from projects_rules as pr inner join rules as r on r.id=pr.id_rule where pr.id_project='".$iProjectId."' order by pr.reference*1";
			if ($bReverse) {
			    $sQuery .= " desc";
			}
			else {
			    $sQuery .= " asc";
			}
			if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($linha = $oResultSet->next()) {
					array_push($aData, array("id_rule"=>$linha["id_rule"],"rule"=>$linha["rule"],"reference"=>str_replace(array(".",","),array("","."),$linha["reference"]),"goal"=>$linha["goal"],"collective_reward"=>$linha["collective_reward"]));
				}
			}
			return $aData;
		}



		/**
		 * Método getAllContributionValuesTypesFromDB()
		 *
		 * Captura todos os possíveis valores de contribuição por tipo
		 *
		 * @param int $iProjectId
		 * @return array
		 */
		public function getAllContributionValuesTypesFromDB($iProjectId) {
			$aData = array();
			$sQuery = "select co.id as id_contribution,co.value,pco.reward,pco.id as id_project_contribution from contributions_options as co inner join projects_contribution_options as pco on pco.id_contribution_option=co.id where pco.id_project='".$iProjectId."'";
			if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($linha = $oResultSet->next()) {
					array_push($aData, array("id_contribution"=>$linha["id_contribution"],"value"=>$linha["value"],"reward"=>$linha["reward"],"id_project_contribution"=>$linha["id_project_contribution"]));
				}
			}
			return $aData;
		}



		/**
		 * Método getTotalAmountCollectedFromDB()
		 *
		 * Captura o total arrecadado até então no projeto
		 *
		 * @param int $iProjectId
		 * @return float
		 */
		public function getTotalAmountCollectedFromDB($iProjectId) {
			$sQuery = "select sum(value_contribution) as total from contributions where id_project='".$iProjectId."' and status='cobrado e recorrencia autorizada'";
			if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				$line = $oResultSet->next();
				$fSum = $line["total"];
			}
			return $fSum;
		}



		/**
		 * Método getAllContributorsFromDB()
		 *
		 * Captura os ids dos contribuintes do projeto
		 *
		 * @param int $iProjectId
		 * @return array
		 */
		public function getAllContributorsFromDB($iProjectId) {
			$aIds = array();
			$sQuery = "select distinct(id_user) from contributions where id_project='".$iProjectId."' and status='cobrado e recorrencia autorizada'";
			if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($line = $oResultSet->next()) {
				    array_push($aIds,$line["id_user"]);
				}
			}
			return $aIds;
		}



		/**
		 * Método getLastContributorsFromDB()
		 *
		 * Captura os ids dos últimos N contribuintes do projeto
		 *
		 * @param int $iProjectId
		 * @param int $iCount
		 * @return array
		 */
		public function getLastContributorsFromDB($iProjectId,$iCount) {
		    $aIds = array();
		    $sQuery = "select distinct(id_user) from contributions where id_project='".$iProjectId."' and status='cobrado e recorrencia autorizada' order by dt_contribution desc LIMIT 0,".$iCount;
		    if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
		        while ($line = $oResultSet->next()) {
		            array_push($aIds,$line["id_user"]);
		        }
		    }
		    return $aIds;
		}
		


		/**
		 * Método getViewCount()
		 *
		 * Retorna o total de views do projeto
		 *
		 * @return int
		 */
		public function getViewCount($iProjectId) {
			$sQuery = "select total_views from projects where id='".$iProjectId."'";
			if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
			    $line = $oResultSet->next();
			    $iTotal = $line["total_views"];
			}
			return $iTotal;
		}



		/**
		 * Método getLastUserContributionFromDB()
		 *
		 * Retorna a última (e única) contribuição do usuário no projeto
		 *
		 * @return int
		 */
		public function getLastUserContributionFromDB($iProjectId,$iUserId) {
		  $iContributionId = null;
			$sQuery = "select id from contributions where id_project='".$iProjectId."' and id_user='".$iUserId."' and (status='cobrado e recorrencia autorizada' or status like 'cobrança atrasada%')";
			if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				$line = $oResultSet->next();
				$iContributionId = $line["id"];
			}
			return $iContributionId;
		}



		/**
		 * Método getUserDelayedContributionsFromDB()
		 *
		 * Retorna a última (e única) contribuição do usuário no projeto
		 *
		 * @return int
		 */
		public function getUserDelayedContributionsFromDB($iProjectId,$iUserId) {
		    $iContributionId = null;
		    $sQuery = "select id from contributions where id_project='".$iProjectId."' and id_user='".$iUserId."' and status like 'cobrança atrasada%'";
		    if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
		        $line = $oResultSet->next();
		        $iContributionId = $line["id"];
		    }
		    return $iContributionId;
		}
		


		/**
		 * Método addViewRegister()
		 *
		 * Registra um valor a mais no acumulador de views do projeto
		 *
		 * @return int
		 */
		public function addViewRegister($iProjectId) {
			$sQuery = "update projects set total_views=total_views+1 where id='".$iProjectId."'";
			$oResultSet = $this->dbAdapter->query($sQuery)->execute();
			$iTotal = $this->getViewCount($iProjectId);
			return $iTotal;
		}



		/**
		 * Método setContributionsOptions()
		 *
		 * Registra um valor a mais no acumulador de views do projeto
		 *
		 * @param int iProjectId
		 * @param array $aIdsContributionsOptions
		 * @return int
		 */
		public function setContributionsOptionsIntoDB($iProjectId,$aIdsContributionsOptions) {
		    foreach ($aIdsContributionsOptions as $iIdContribuitionOption) {
		        $sQuery = "replace into projects_contributions_options (id_project,id_contribution_option) values('".$iProjectId."','".$iIdContribuitionOption."')";
            $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		    }
        return true;
		}



		/**
		 * Método existByShortURL()
		 *
		 * Registra um valor a mais no acumulador de views do projeto
		 *
		 * @param string sShortURL
		 * @return int
		 */
		public function existByShortURL($sShortURL) {
		    $iProjectId = null;
				$sQuery = "select id from projects where short_url='".$sShortURL."'";
        if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
        	$line = $oResultSet->next();
        	$iProjectId = $line["id"];
        }
        return $iProjectId;
		}



		/**
		 * Método changeStatusFromDB()
		 *
		 * Grava no banco de dados o novo status do projeto
		 *
		 * @param int $iProjectId
		 * @param int $iStatusId
		 * @return boolean
		 */
		public function changeStatusFromDB($iProjectId,$iStatusId) {
		    $sQuery = "update projects set id_status=".$iStatusId." where id=".$iProjectId;
		    if ($oResultSet = $this->dbAdapter->query($sQuery)->execute()) {
		        return true;
		    }
		    else {
		        return false;
		    }
		}
		


		/**
		 * Método saveAllFieldsIntoDB()
		 *
		 * Grava todos os dados do projeto no banco de dados.
		 *
		 * @return int
		 */
		public function saveAllFieldsIntoDB($iProjectId = null,$aData) {
		    if (empty($iProjectId)) {
		        // Insert (novo registro)
		        $sQuery = "insert into projects (id_user,title,organization,email,how_find_us,about_you,project_abstract,operating_region,fundation_year,employees_number,volunteers_number,people_impacted_number,about_project,how_long,average_monthly_spending,resources_from,marketing,dt_registration,dt_update,id_status) values('".$aData["id_user"]."','".$aData["title"]."','".$aData["organization"]."','".$aData["email"]."','".$aData["how_find_us"]."','".$aData["about_you"]."','".$aData["project_abstract"]."','".$aData["operating_region"]."','".$aData["fundation_year"]."','".$aData["employees_number"]."','".$aData["volunteers_number"]."','".$aData["people_impacted_number"]."','".$aData["about_project"]."','".$aData["how_long"]."','".$aData["average_monthly_spending"]."','".$aData["resources_from"]."','".$aData["marketing"]."','".date("Y-m-d H:i:s",$aData["dt_registration"])."','".$aData["dt_update"]."','".$aData["id_status"]."')";
//print($sQuery."<hr>");
		        $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        $iProjectId = $this->dbAdapter->getDriver()->getLastGeneratedValue();
		        // Telefones
		        if ($aData["main_phone"]!==false) {
		          $sQuery = "insert into projects_phones (id,id_project,title,phone) values(null,".$iProjectId.",'Principal','".$aData["main_phone"]."')";
//print($sQuery."<hr>");
		          $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        }
		    		if ($aData["other_phone"]!==false) {
		          $sQuery = "insert into projects_phones (id,id_project,title,phone) values(null,".$iProjectId.",'Outro','".$aData["other_phone"]."')";
//print($sQuery."<hr>");
		          $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        }
		        // Company Types
		        if (is_array($aData["company_type"])) {
		            foreach ($aData["company_type"] as $iIdCompanyType) {
		                $sQuery = "insert into projects_company_types (id,id_project,id_company_type) values(null,".$iProjectId.",".$iIdCompanyType.")";
//print($sQuery."<hr>");
		                $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		            }
		        }
		        // Categories
		    		        if (is_array($aData["categories"])) {
		            foreach ($aData["categories"] as $iIdCategory) {
		                $sQuery = "insert into projects_categories (id,id_project,id_category) values(null,".$iProjectId.",".$iIdCategory.")";
//print($sQuery."<hr>");
		                $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		            }
		        }
		        // Metas
		        if (!empty($aData["goal1_money"])) {
		            $sQuery = "insert into projects_rules (id,id_rule,id_project,reference,goal,collective_reward) values(null,1,".$iProjectId.",'".$aData["goal1_money"]."','".$aData["goal1_objective_for_money"]."','".$aData["goal1_reward"]."')";
//print($sQuery."<hr>");
                $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        }
		        if (!empty($aData["goal2_money"])) {
		        	$sQuery = "insert into projects_rules (id,id_rule,id_project,reference,goal,collective_reward) values(null,1,".$iProjectId.",'".$aData["goal2_money"]."','".$aData["goal2_objective_for_money"]."','".$aData["goal2_reward"]."')";
//print($sQuery."<hr>");
		        	$oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        }
		        if (!empty($aData["goal3_money"])) {
		        	$sQuery = "insert into projects_rules (id,id_rule,id_project,reference,goal,collective_reward) values(null,1,".$iProjectId.",'".$aData["goal3_money"]."','".$aData["goal3_objective_for_money"]."','".$aData["goal3_reward"]."')";
//print($sQuery."<hr>");
		        	$oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        }
		        // Link
		        if (is_array($aData["link_title"])) {
		        	for ($i=0;$i<count($aData["link_title"]);$i++) {
		        	  $aData["link_title"] = trim($aData["link_title"][$i]);
		        	  if (!empty($aData["link_title"][$i])) {
    		        		$sQuery = "insert into projects_links (id,id_project,title,link) values(null,".$iProjectId.",'".$aData["link_title"][$i]."','".$aData["link_url"][$i]."')";
    		        		$oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        	  }
		        	}
		        }
		        return true;
		    }
		    else {
		        // Update
		        $sQuery = "update projects set title='".$aData["title"]."',organization='".$aData["organization"]."',email='".$aData["email"]."',how_find_us='".$aData["how_find_us"]."',about_you='".$aData["about_you"]."',project_abstract='".$aData["project_abstract"]."',operating_region='".$aData["operating_region"]."',fundation_year='".$aData["fundation_year"]."',employees_number='".$aData["employees_number"]."',volunteers_number='".$aData["volunteers_number"]."',people_impacted_number='".$aData["people_impacted_number"]."',about_project='".$aData["about_project"]."',how_long='".$aData["how_long"]."',average_monthly_spending='".$aData["average_monthly_spending"]."',resources_from='".$aData["resources_from"]."',marketing='".$aData["marketing"]."',id_status='".$aData["id_status"]."',dt_update='".date("Y-m-d H:i:s",time())."' where id=".$iProjectId;
		        $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        // Telefones
		        $sQuery = "delete from projects_phones where id_project=".$iProjectId;
		        $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        if ($aData["main_phone"]!==false) {
		          $sQuery = "insert into projects_phones (id,id_project,title,phone) values(null,".$iProjectId.",'Principal','".$aData["main_phone"]."')";
		          $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        }
		    		if ($aData["other_phone"]!==false) {
		          $sQuery = "insert into projects_phones (id,id_project,title,phone) values(null,".$iProjectId.",'Outro','".$aData["other_phone"]."')";
		          $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        }
		        // Company Type
		        $sQuery = "delete from projects_company_types where id_project=".$iProjectId;
		        $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        if (is_array($aData["company_type"])) {
		            foreach ($aData["company_type"] as $iIdCompanyType) {
		                $sQuery = "insert into projects_company_types (id,id_project,id_company_type) values(null,".$iProjectId.",".$iIdCompanyType.")";
		                $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		            }
		        }
		        // Categories
		        $sQuery = "delete from projects_categories where id_project=".$iProjectId;
		        $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        if (is_array($aData["categories"])) {
		            foreach ($aData["categories"] as $iIdCategory) {
		                $sQuery = "insert into projects_categories (id,id_project,id_category) values(null,".$iProjectId.",".$iIdCategory.")";
		                $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		            }
		        }
		        // Goals
		        $sQuery = "delete from projects_rules where id_project=".$iProjectId;
		        $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        if (is_array($aData["goal_money"])) {
		            for ($i=0;$i<count($aData["goal_money"]);$i++) {
                    $sQuery = "insert into projects_rules (id,id_rule,id_project,reference,goal,collective_reward) values(null,1,".$iProjectId.",'".$aData["goal_money"][$i]."','".$aData["goal_objective_for_money"][$i]."','".$aData["goal_reward"][$i]."')";
                    $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		            }
		        }
		        // Link
		        $sQuery = "delete from projects_links where id_project=".$iProjectId;
		        $oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        if (is_array($aData["link_title"])) {
		        	for ($i=0;$i<count($aData["link_title"]);$i++) {
		        	  $aData["link_title"] = trim($aData["link_title"][$i]);
		        	  if (!empty($aData["link_title"][$i])) {
    		        		$sQuery = "insert into projects_links (id,id_project,title,link) values(null,".$iProjectId.",'".$aData["link_title"][$i]."','".$aData["link_url"][$i]."')";
    		        		$oResultSet = $this->dbAdapter->query($sQuery)->execute();
		        	  }
		        	}
		        }
		        return true;
		    }
		}
		
	
	
	
	
	
	}
