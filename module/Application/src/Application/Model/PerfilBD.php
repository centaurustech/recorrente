<?php
	namespace Application\Model;
	
	/**
		* Classe PerfilBD
		*
		* Responsável por operações em banco de dados. 
		* 
		* Sempre usar ANSI SQL 
		*/
	class PerfilBD {
	
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
			* Método retornaId()
			*
			* Captura o ID usuário.
			* 
			* @param string $sUsuario
			* @return int $iIdUsuario
			*/
		public function retornaId($sUsuario) {
			$sQuery = "select id from users where email='" . $sUsuario . "'";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				$linha = $umResultSet->next();
				return (int) $linha["id"];
			}
		}
	
	
		/**
			* Método retornaIdPorEmail()
			*
			* Captura o ID usuário baseado em seu email.
			* 
			* @param string $sEmail
			* @return int $iIdUsuario
			*/
		public function retornaIdPorEmail($sEmail) {
			$sQuery = "select id from users where email='" . $sEmail . "'";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				$linha = $umResultSet->next();
				return (int) $linha["id"];
			}
		}



	
	
		/**
			* Método dadosEndereco()
			*
			* Captura apenas os dados de endereço.
			* 
			* @param int $iIdUsuario
			* @return mixed
			*/
		public function dadosEnderecos($iIdUsuario) {
			$aTemp = array();
			$sQuery = "select zip,address,number,complement,neighborhood,id_city,literal_country,literal_state,literal_city from users where id=" . $iIdUsuario;
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				if ($umResultSet->count()>0) {
					while ($linha = $umResultSet->next()) {
						$aTemp["residencial"] = array("zip"=>$linha["zip"],"address"=>$linha["address"],"number"=>$linha["number"],"complement"=>$linha["complement"],"neighborhood"=>$linha["neighborhood"],"id_city"=>$linha["id_city"],"literal_country"=>$linha["literal_country"],"literal_state"=>$linha["literal_state"],"literal_city"=>$linha["literal_city"]);
					}
				}
			}
			return $aTemp;
		}



	
	
		/**
			* Método dadosTelefone()
			*
			* Captura da base os dados de login e senha do id usuario informado.
			* 
			* @param int $iIdUsuario
			* @return mixed
			*/
		public function dadosTelefones($iIdUsuario) {
			$aTemp = array();
			$sQuery = "select phone_home,phone_business,phone_cell from users where id=" . $iIdUsuario;
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				if ($umResultSet->count()>0) {
					$linha = $umResultSet->next();
					array_push($aTemp,array("title"=>"residencial","number"=>$linha["phone_home"]));
					array_push($aTemp,array("title"=>"comercial","number"=>$linha["phone_business"]));
					array_push($aTemp,array("title"=>"celular","number"=>$linha["phone_cell"]));
				}
			}
			return $aTemp;
		}



	
	
		/**
			* Método dadosLogin()
			*
			* Captura da base os dados de login e senha do id usuario informado.
			* 
			* @param int $iIdUsuario
			* @return mixed
			*/
		public function dadosLogin($iIdUsuario) {
			$sQuery = "select email,password,status from users where id=" . $iIdUsuario;
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				$linha = $umResultSet->next();
				return $linha;
			}
			else {
				return false;
			}
		}





		/**
		 * Método dadosComplementares()
		 *
		 * Captura da base os dados complementares ao perfil.
		 *
		 * @param int $iIdUsuario
		 * @return mixed
		 */
		public function dadosComplementares($iIdUsuario) {
			$sQuery = "select cpf,cnpj,type,company_name from users where id=" . $iIdUsuario;
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				$linha = $umResultSet->next();
				return $linha;
			}
			else {
				return false;
			}
		}
		






		/**
			* Método capturaTodosPerfis()
			*
			* Captura da base os dados todos os perfis associados.
			* 
			* @param int $iIdUsuario
			* @return mixed
			*/
		public function capturaTodosPerfis($iIdUsuario) {
			$aTemp = array();
			$sQuery = "select id_profile from users_profiles where id_user=" . $iIdUsuario;
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($linha = $umResultSet->next()) {
					array_push($aTemp,$linha["id_profile"]);
				}
				return $aTemp;
			}
		}



		/**
			* Método capturaDadosBasicos()
			*
			* Captura da base os dados os campos nome, sexo, data_nascimento e data_cadastro do usuário
			* 
			* @param int $iIdUsuario
			* @return mixed
			*/
		public function capturaDadosBasicos($iIdUsuario) {
			$sQuery = "select name,gender,birthday,dt_registration,email,type,cpf,cnpj,phone_home,phone_business,phone_cell,zip,address,id_city,status,type,company_name from users where id=" . $iIdUsuario;
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				$linha = $umResultSet->next();
				return $linha;
			}
			else {
				return false;
			}
		}



		/**
		 * Método getAllProjectsByUserInDB()
		 *
		 * Captura todos os projetos que o usuário é proprietario
		 *
		 * @param int $iIdUsuario
		 * @param array $aExcludeStatus
		 * @return mixed
		 */
		public function getAllProjectsByUserInDB($iIdUsuario,$aExcludeStatus = null) {
			$aTemp = array();
			$sQuery = "select id from projects where id_user=" . $iIdUsuario;
			if (is_array($aExcludeStatus)) {
			    if (count($aExcludeStatus)>0) {
			        $sQuery .= " and (";
			        foreach ($aExcludeStatus as $iIdStatus) {
			            $sQuery .= "id_status<>".$iIdStatus." and ";
			        }
			        $sQuery = substr($sQuery,0,-4).")";
			    }
			}
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($line = $umResultSet->next()) {
				    array_push($aTemp,$line["id"]);
				}
			}
			return $aTemp;
		}



		/**
		 * Método getAllActivedProjectsByUserInDB()
		 *
		 * Captura todos os projetos que estão ativos no momento onde o usuário é proprietario
		 *
		 * @param int $iIdUsuario
		 * @return mixed
		 */
		public function getAllActivedProjectsByUserInDB($iIdUsuario) {
			$aTemp = array();
			$sQuery = "select id from projects where id_user=" . $iIdUsuario . " and status='aprovado'";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($line = $umResultSet->next()) {
					array_push($aTemp,$line["id"]);
				}
			}
			return $aTemp;
		}



		/**
		 * Método getAllActiveContributionsMadeInDB()
		 *
		 * Captura todas as contribuições que foram feitas pelo usuário
		 *
		 * @param int $iIdUsuario
		 * @return mixed
		 */
		public function getAllActiveContributionsMadeInDB($iIdUsuario,$bReverse) {
		    $aData = array();
		    $sQuery = "select id,id_project,id_payment,dt_contribution,value_contribution,payment_gateway_code from contributions where status='cobrado e recorrencia autorizada' and id_user=" . $iIdUsuario;
		    if ($bReverse) {
		        $sQuery .= " order by dt_contribution desc";
		    }
		    if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
		        while ($line = $umResultSet->next()) {
		            array_push($aData,array("id_contribution"=>$line["id"],"id_project"=>$line["id_project"],"id_payment"=>$line["id_payment"],"dt_contribution"=>$line["dt_contribution"],"value_contribution"=>$line["value_contribution"],"payment_gateway_code"=>$line["payment_gateway_code"]));
		        }
		        return $aData;
		    }
		    else {
		        return false;
		    }
		}
		


		/**
		 * Método getAllContributionsMadeInDB()
		 *
		 * Captura todas as contribuições que foram feitas pelo usuário
		 *
		 * @param int $iIdUsuario
		 * @return mixed
		 */
		public function getAllContributionsMadeInDB($iIdUsuario,$bReverse) {
		  $aData = array();
			$sQuery = "select id,id_project,id_payment,dt_contribution,dt_status_change,value_contribution,payment_gateway_code,status from contributions where status<>'cobrado e recorrencia autorizada' and id_user=" . $iIdUsuario;
			if ($bReverse) {
			    $sQuery .= " order by dt_status_change desc";
			}
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($line = $umResultSet->next()) {
				    array_push($aData,array("id_contribution"=>$line["id"],"id_project"=>$line["id_project"],"id_payment"=>$line["id_payment"],"dt_contribution"=>$line["dt_contribution"],"dt_status_change"=>$line["dt_status_change"],"status"=>$line["status"],"value_contribution"=>$line["value_contribution"],"payment_gateway_code"=>$line["payment_gateway_code"]));
				}
				return $aData;
			}
			else {
				return false;
			}
		}



		/**
		 * Método getAllContributionsReceivedInDB()
		 *
		 * Captura todas as contribuições que foram feitas para os projetos do usuário
		 *
		 * @param int $iIdUsuario
		 * @return mixed
		 */
		public function getAllContributionsReceivedInDB($iIdUsuario,$bReverse) {
		  $aProjectsId = $this->getAllProjectsByUserInDB($iIdUsuario);
		  $sQueryProjects = "";
		  foreach ($aProjectsId as $iProjectId) {
		      $sQueryProjects .= $iProjectId.",";
		  }
		  $sQueryProjects = substr($sQueryProjects,0,-1);
			$aData = array();
			$sQuery = "select c.id,c.id_project,p.title,c.id_payment,c.dt_contribution,c.value_contribution from contributions as c inner join projects as p on p.id=c.id_project where p.id_user=" . $iIdUsuario . " and c.status='cobrado e recorrencia autorizada' and c.id_project in (" . $sQueryProjects . ")";
			if ($bReverse) {
				$sQuery .= " order by c.dt_contribution desc";
			}
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($line = $umResultSet->next()) {
					array_push($aData,array("id_contribution"=>$line["id"],"id_project"=>$line["id_project"],"title"=>$line["title"],"id_payment"=>$line["id_payment"],"dt_contribution"=>$line["dt_contribution"],"value_contribution"=>$line["value_contribution"]));
				}
				return $aData;
			}
			else {
				return false;
			}
		}



		/**
		 * Método getSumReceivedContributionsAllTimeInDB()
		 *
		 * Captura todas as contribuições que foram feitas pelo usuário
		 *
		 * @param int $iIdUsuario
		 * @return mixed
		 */
		public function getSumReceivedContributionsAllTimeInDB($iIdUsuario) {
		  $aProjectsId = $this->getAllProjectsByUserInDB($iIdUsuario);
		  $sQueryProjects = "";
		  foreach ($aProjectsId as $iProjectId) {
		      $sQueryProjects .= $iProjectId.",";
		  }
		  $sQueryProjects = substr($sQueryProjects,0,-1);
		  $aData = array();
			$sQuery = "select sum(value_contribution) as total from contributions as c inner join projects as p on c.id_project=p.id where p.id_user=" . $iIdUsuario. " and c.status='cobrado e recorrencia autorizada' and id_project in (" . $sQueryProjects . ")";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				$line = $umResultSet->next();
				$fSum = $line["total"];
			}
			return $fSum;
		}



		/**
		 * Método getSumReceivedContributionsMonthInDB()
		 *
		 * Captura todas as contribuições que foram feitas pelo usuário
		 *
		 * @param int $iIdUsuario
		 * @param int $iMonth
		 * @param int $iYear
		 * @return mixed
		 */
		public function getSumReceivedContributionsMonthInDB($iIdUsuario,$iMonth,$iYear) {
		    $aProjectsId = $this->getAllProjectsByUserInDB($iIdUsuario);
			$sQueryProjects = "";
			foreach ($aProjectsId as $iProjectId) {
				$sQueryProjects .= $iProjectId.",";
			}
			$sQueryProjects = substr($sQueryProjects,0,-1);
			$aData = array();
			$sQuery = "select sum(value_contribution) as total from contributions as c inner join projects as p on c.id_project=p.id where p.id_user=" . $iIdUsuario. " and MONTH(dt_contribution)='".$iMonth."' and YEAR(dt_contribution)='".$iYear."' and c.status='cobrado e recorrencia autorizada' and id_project in (" . $sQueryProjects . ")";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				$line = $umResultSet->next();
				$fSum = $line["total"];
			}
			return $fSum;
		}



		/**
		 * Método getContributionsVariationLastMonthInDB()
		 *
		 * Captura todas as contribuições que foram feitas pelo usuário
		 *
		 * @param int $iIdUsuario
		 * @return mixed
		 */
		public function getContributionsVariationLastMonthInDB($iIdUsuario) {
			$aProjectsId = $this->getAllProjectsByUserInDB($iIdUsuario);
			$sQueryProjects = "";
			foreach ($aProjectsId as $iProjectId) {
				$sQueryProjects .= $iProjectId.",";
			}
			$sQueryProjects = substr($sQueryProjects,0,-1);
			// Actual Month
			$iActualMonth = date("n");
			$iActualYear = date("Y");
			$fSumActualMonth = $this->getSumReceivedContributionsMonthInDB($iIdUsuario,$iActualMonth,$iActualYear);
			// Last month
			$iLastMonth = date("n",strtotime("-1 month"));
			$iLastYear = date("Y",strtotime("-1 month"));
			$fSumLastMonth = $this->getSumReceivedContributionsMonthInDB($iIdUsuario,$iLastMonth,$iLastYear);
			if ($fSumLastMonth>0) {
			    $fVariation = $fSumActualMonth/$fSumLastMonth;
			    return $fVariation;
			}
			else {
			    return false;
			}
		}



		/**
		 * Método getSumProjectedContributionsMonthInDB()
		 *
		 * Retorna a soma das parcelas dos projetos ativos, com projeção para o mês e ano escolhidos
		 *
		 * @param int $iIdUsuario
		 * @param int $iMonth
		 * @param int $iYear
		 * @return mixed
		 */
		public function getSumProjectedContributionsMonthInDB($iIdUsuario,$iMonth,$iYear) {
		    $iActualMonth = date("m");
		    $iActualYear = date("Y");
		    $fSumActualMonth = $this->getSumReceivedContributionsMonthInDB($iIdUsuario,$iActualMonth,$iActualYear);
		    // Calcula quantas parcelas existirão até a data
		    $sDate1 = $iActualYear."-".$iActualMonth."-01";
		    $sDate2 = $iYear."-".$iMonth."-01";
		    
		    $diff = abs(strtotime($sDate2) - strtotime($sDate1));
		    
		    $years = floor($diff / (365*60*60*24));
		    $iQtdMonths = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		    
		    return $fSumActualMonth*$iQtdMonths;
		}
		



		/**
			* Método bloqueiaUsuario()
			*
			* Bloqueia um usuário
			* 
			* @param int $iIdUsuario
			* @return boolean
			*/
		public function bloqueiaUsuario($iIdUsuario) {
			$sQuery = "update users set status='bloqueado' where id=" . $iIdUsuario;
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				return true;
			}
			else {
				return false;
			}
		}




		/**
			* Método registraChaveRecuperacaoSenha()
			*
			* Vincula ao usuário a chave que permite o mesmo alterar sua senha
			* 
			* @param int $iIdUsuario
			* @return boolean
			*/
		public function registraChaveRecuperacaoSenha($iIdUsuario,$sChave,$sEmailInformado) {
			// Apaga as chaves desse usuário que não foram usadas ainda
			$sQuery = "delete from users_password_recovery where email='".$sEmailInformado."' and used is null";
			$this->dbAdapter->query($sQuery)->execute();
			$sQuery = "insert into users_password_recovery (hashkey,email,id_user) values('".$sChave."','".$sEmailInformado."','".$iIdUsuario."')";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				return true;
			}
			else {
				return false;
			}
		}




		/**
		 * Método getWebAdminActions()
		 *
		 * Captura ocorrências diversas no webadmin que precisam da atenção do usuário
		 *
		 * @param int $iIdUsuario
		 * @return boolean
		 */
		public function getWebAdminActions($iIdUsuario) {
		    $iContOcorrencias = 0;
        // 1o - Verifica se existem projetos aguardando aprovação e quantos são
        $sQuery = "select count(*) as total from projects where id_status=1";
        if ($umResult = $this->dbAdapter->query($sQuery)->execute()) {
            $line = $umResult->next();
            $iContOcorrencias += $line["total"];
        }
        return $iContOcorrencias;
		}




		/**
		 * Método getWebAdminActions()
		 *
		 * Captura ocorrências diversas no webadmin que precisam da atenção do usuário
		 *
		 * @param int $iIdUsuario
		 * @return boolean
		 */
		public function userIsProjectContributor($iUserId,$iProjectId) {
		    $iContOcorrencias = 0;
		    $sQuery = "select count(*) as total from contributions where id_user='".$iUserId."' and id_project='".$iProjectId."' and (status='cobrado e recorrencia autorizada' or status like 'cobrança atrasada%')";
        if ($umResult = $this->dbAdapter->query($sQuery)->execute()) {
            $line = $umResult->next();
            $iContOcorrencias += $line["total"];
        }
        if ($iContOcorrencias>0) {
            return true;
        }
        else {
            return false;
        }
		}







		
		/**
			* Método save()
			*
			* Grava o perfil do usuário
			* 
			* @param mixed $aData
			* @return int
			*/
				public function save($aData) {
		    if (!empty($aData["id_usuario"])) {
		        // Update
		        if (!empty($aData["password"])) {
                $sQuery = "update users set name='".$aData["name"]."',password='".$aData["password"]."',gender='".$aData["gender"]."',birthday='".$aData["birthday"]."',cpf='".$aData["cpf"]."',phone_home='".$aData["phone_home"]["number"]."',phone_business='".$aData["phone_business"]["number"]."',phone_cell='".$aData["phone_cell"]["number"]."',zip='".$aData["zip"]."',address='".$aData["address"]."',id_city='".$aData["id_city"]."',dt_update='".date("Y-m-d H:i:s")."',status='".$aData["status"]."',type='".$aData["type"]."',cnpj='".$aData["cnpj"]."',activation_id='".$aData["activation_id"]."',company_name='".$aData["company_name"]."' where id='".$aData["id_usuario"]."'";
		        }
		        else {
		            $sQuery = "update users set name='".$aData["name"]."',gender='".$aData["gender"]."',birthday='".$aData["birthday"]."',cpf='".$aData["cpf"]."',phone_home='".$aData["phone_home"]["number"]."',phone_business='".$aData["phone_business"]["number"]."',phone_cell='".$aData["phone_cell"]["number"]."',zip='".$aData["zip"]."',address='".$aData["address"]."',id_city='".$aData["id_city"]."',dt_update='".date("Y-m-d H:i:s")."',status='".$aData["status"]."',type='".$aData["type"]."',cnpj='".$aData["cnpj"]."',activation_id='".$aData["activation_id"]."',company_name='".$aData["company_name"]."' where id='".$aData["id_usuario"]."'";
		        }
//print_r($aData["phone_home"]);print($sQuery); exit;
		    }
		    else {
		        // Insert
		        $sQuery = "insert into users (id,name,email,password,gender,birthday,cpf,phone_home,phone_business,phone_cell,zip,address,id_city,dt_registration,dt_update,status,type,cnpj,activation_id,company_name) values(null,'".$aData["name"]."','".$aData["email"]."','".$aData["password"]."','".$aData["gender"]."','".$aData["birthday"]."','".$aData["cpf"]."','".@$aData["phone_home"]["number"]."','".@$aData["phone_business"]["number"]."','".@$aData["phone_cell"]["number"]."','".$aData["zip"]."','".$aData["address"]."','".$aData["id_city"]."','".date("Y-m-d H:i:s",$aData["dt_registration"])."','".date("Y-m-d H:i:s")."','".$aData["status"]."','".$aData["type"]."','".$aData["cnpj"]."','".$aData["activation_id"]."','".$aData["company_name"]."')";
		    }
		    if ($this->dbAdapter->query($sQuery)->execute()) {
		        if (empty($aData["id_usuario"])) {
		            $aData["id_usuario"] = $this->dbAdapter->getDriver()->getLastGeneratedValue();
		        }
		        // Atualiza a tabela de tipos de perfis
		        foreach ($aData["id_perfil"] as $iIdPerfil) {
		            $sQuery = "select id from users_profiles where id_user='".$aData["id_usuario"]."' and id_profile='".$iIdPerfil."'";
		            if ($umResult = $this->dbAdapter->query($sQuery)->execute()) {
		                if ($umResult->count()==0) {
		                      // Registra o perfil
		                      $sQuery = "insert into users_profiles (id_user,id_profile) values('".$aData["id_usuario"]."','".$iIdPerfil."')";
		                      $umResult = $this->dbAdapter->query($sQuery)->execute();
		                }
		            }
		        }
		        return $aData["id_usuario"];
		    }
		    else {
		        return false;
		    }
		    
		}




		/**
			* Método gravaEnderecosBanco()
			*
			* Grava (atualiza) o endereço do usuário
			* 
			* @param int $iIdUsuario
			* @param mixed $aEndereco
			* @return boolean
			*/
		public function gravaEnderecosBanco($iIdUsuario,$aEndereco) {
//print_r($aEndereco); exit;
        // Faz update
        $sQuery = "update users set zip='".$aEndereco["residencial"]["zip"]."',number='".$aEndereco["residencial"]["number"]."',complement='".$aEndereco["residencial"]["complement"]."',neighborhood='".$aEndereco["residencial"]["neighborhood"]."',id_city='".$aEndereco["residencial"]["id_city"]."',address='".$aEndereco["residencial"]["address"]."' where id='".$iIdUsuario."'";
        $this->dbAdapter->query($sQuery)->execute();
        return true;
		}




		/**
			* Método gravaTelefonesBanco()
			*
			* Grava (atualiza) todos os telefones vinculados ao usuário
			* 
			* @param int $iIdUsuario
			* @param mixed $aTelefones
			* @return boolean
			*/
		public function gravaTelefonesBanco($iIdUsuario,$aTelefones) {
		
    		// Faz update
    		$sQuery = "update users set phone_home='".$aDados["phone_home"]."',phone_business='".$aDados["phone_business"]."',phone_cell='".$aDados["phone_cell"]."' where id='".$iIdUsuario."'";
    		$this->dbAdapter->query($sQuery)->execute();
		    return true;
		}
	
	}
