<?php
	// Classe ContributionDB
	
	namespace Application\Model;
	
	use Zend\Session\Container;
	
	class ContributionDB {
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
		 * Captura todos os dados da contribuição e de seu pagamento.
		 *
		 * @param int $iContributionId
		 * @return int
		 */
		public function getAllFieldsFromDB($iContributionId) {
		    $aData = array();
	      $sQuery = "select c.id_project,c.id_user,c.id_payment,c.dt_contribution,c.value_contribution,c.status,c.payment_gateway_code,ppi.type,ppi.cc_flag,ppi.cc_number,ppi.cc_holder_name,ppi.cc_expiration_date,ppi.cc_security_number from contributions as c inner join projects_payment_info as ppi on ppi.id=c.id_payment where c.id='".$iContributionId."'";
	      if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
	          $aData = $umResultSet->next();
	      }
	      return $aData;
		}



		/**
		 * Método cancelByUser()
		 *
		 * Captura todos os dados da contribuição e de seu pagamento.
		 *
		 * @param int $iContributionId
		 * @return int
		 */
		public function cancelByUser($iContributionId) {
			$sQuery = "update contributions set status='cancelado pelo usuario',dt_status_change='".date("Y-m-d H:i:s")."' where id='".$iContributionId."'";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				return true;
			}
			else {
			    return false;
			}
		}
		


		/**
		 * Método saveAllFieldsIntoDB()
		 *
		 * Grava todos os dados da contribuição no banco de dados.
		 *
		 * @return int
		 */
		public function saveAllFieldsIntoDB($iContributionId = null,$aData) {
			if (empty($iContributionId)) {
				// Insert (novo registro)
				$sQuery = "insert into contributions (id_user,id_project,id_payment,dt_contribution,value_contribution,status,start_value,payment_gateway_code) values('".@$aData["id_user"]."','".@$aData["id_project"]."','".@$aData["id_payment"]."','".date("Y-m-d H:i:s")."','".@$aData["value_contribution"]."','".@$aData["status"]."','".@$aData["start_value"]."','".@$aData["gateway_payment_code"]."')";
				$umResultSet = $this->dbAdapter->query($sQuery)->execute();
				$iContributionId = $this->dbAdapter->getDriver()->getLastGeneratedValue();
				// Pagamento
				if (@$aData["type"]!==false) {
					$sQuery = "insert into projects_payment_info (type,cc_flag,cc_number,cc_holder_name,cc_expiration_date,cc_security_number) values('".@$aData["type"]."','".@$aData["cc_flag"]."','".@$aData["cc_number"]."','".@$aData["cc_holder_name"]."','".@$aData["cc_expiration_date"]."','".@$aData["cc_security_number"]."')";
					$umResultSet = $this->dbAdapter->query($sQuery)->execute();
				  $iPaymentInfoId = $this->dbAdapter->getDriver()->getLastGeneratedValue();
				  // Update
				  $sQuery = "update contributions set id_payment='".$iPaymentInfoId."' where id='".$iContributionId."'";
					$umResultSet = $this->dbAdapter->query($sQuery)->execute();
				}
				return $iContributionId;
			}
			else {
				// Update
				$sQuery = "update contributions set id_user='".$aData["id_user"]."',id_project='".$aData["id_project"]."',value_contribution='".$aData["value_contribution"]."',status='".$aData["status"]."',payment_gateway_code='".$aData["payment_gateway_code"]."',dt_status_change='".date("Y-m-d H:i:s")."' where id=".$iContributionId;
				$umResultSet = $this->dbAdapter->query($sQuery)->execute();
				$sQuery = "select id_payment from contributions where id='".$iContributionId."'";
				if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				    $line = $umResultSet->next();
				    $iPaymentInfoId = $line["id_payment"];
				}
				$sQuery = "update projects_payment_info set type='".$aData["type"]."',cc_flag='".$aData["cc_flag"]."',cc_number='".$aData["cc_number"]."',cc_holder_name='".$aData["cc_holder_name"]."',cc_expiration_date='".$aData["cc_expiration_date"]."',cc_security_number='".$aData["cc_security_number"]."' where id='".$iPaymentInfoId."'";
				$umResultSet = $this->dbAdapter->query($sQuery)->execute();
				return true;
			}
		}
		


	}