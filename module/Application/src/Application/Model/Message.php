<?php
	namespace Application\Model;
	
	use Application\Model\MessageDB;

	/**
		* Mensagem
		*
		* A classe Mensagem acumula dados de uma única mensagem no sistema
		* 
		*/
	class Message {
		protected $oServiceManager;
		protected $oMessageDB;
		protected $iId;
		protected $iIdUserFrom;
		protected $iIdUserTo;
		protected $iIdUserReplied;
		protected $iIdProject;
		protected $sSubject;
		protected $sContent;
		protected $dDtCreation;
		protected $bExcluded;
		protected $sUniqueHash;
		
		/**
			* Método Construtor
			*
			* Inicializa o objeto, 
			* 
			* @param ServiceManager $umSM
			* @param int $iIdMundo
			* @return int
			*/
		public function __construct($oSM,$iId = null,$sHash = null) {
			$this->oServiceManager = $oSM;
			$this->iId = $iId;
			$this->sUniqueHash = $sHash;
			$this->oMessageDB = new MessageDB($this->oServiceManager->get("Zend\Db\Adapter\Adapter"));
			if (!empty($sHash)) {
			    $this->iId = $this->oMessageDB->getIdFromHash($sHash);
			}
			$aData = $this->loadData();
			$this->iIdUserFrom = $aData["id_user_from"];
			$this->iIdUserTo = $aData["id_user_to"];
			$this->iIdUserReplied = $aData["id_user_replied"];
			$this->iIdProject = $aData["id_project"];
			$this->sSubject = $aData["subject"];
			$this->sContent = $aData["content"];
			$this->dDtCreation = $aData["dt_creation"];
			$this->iIdProject = $aData["id_project"];
				
				
		}




		
		
		/**
			* Método getId()
			*
			* Retorna o ID da mensagem
			* 
			* @return int iId
			*/
		public function getId() {
			return $this->iId;
		}






		/**
		 * Método loadData()
		 *
		 * Retorna o ID da mensagem
		 *
		 * @return array aData
		 */
		public function loadData() {
		    return $this->oMessageDB->loadAllDataFromDB($this->iId);
		}
		


	}