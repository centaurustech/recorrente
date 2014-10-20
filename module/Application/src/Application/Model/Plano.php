<?php
	namespace Application\Model;
	
	use Application\Model\PlanoBD;

	/**
		* Plano
		*
		* Representa um plano de assinatura
		* 
		*/
	class Plano {
		protected $umServiceManager;
		protected $umPedidoBD;
		protected $iIdPlano;
		protected $sTitulo;
		protected $bPromocao;
		protected $fValor;
		protected $dDataValor;
		protected $aJogosPlano = array();

		
		/**
			* Método Construtor
			*
			* Inicializa o objeto, 
			* 
			* @param ServiceManager $umSM
			* @param int $iIdPlano
			*/
		public function __construct($umSM,$iIdPlano = null) {
			$this->umServiceManager = $umSM;
			$this->iIdPlano = $iIdPlano;
			$this->umPlanoBD = new PlanoBD($this->umServiceManager->get("Zend\Db\Adapter\Adapter"));
			$this->umPlanoBD->setServiceManager($this->umServiceManager);
		}




		
		
		/**
			* Método getId()
			*
			* Retorna o ID do plano
			* 
			* @return int iIdEscola
			*/
		public function getId() {
			return $this->iIdPlano;
		}



		



		/**
			* Método getTitulo()
			*
			* Retorna o titulo do plano
			* 
			* @return string
			*/
		public function getTitulo() {
			if (!isset($this->sTitulo)) {
				$aDados = $this->umPlanoBD->retornaDadosBasicos($this->getId());
				$this->sTitulo = $aDados["titulo"];
				$this->bPromocao = $aDados["promocao"];
				$this->dDataValor = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
			} 
			return $this->sTitulo;
		}



		



		/**
			* Método getPromocao()
			*
			* Retorna se o plano está em promocao
			* 
			* @return boolean
			*/
		public function getPromocao() {
			if (!isset($this->bPromocao)) {
				$aDados = $this->umPlanoBD->retornaDadosBasicos($this->getId());
				$this->sTitulo = $aDados["titulo"];
				$this->bPromocao = $aDados["promocao"];
				$this->dDataValor = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
			} 
			return $this->bPromocao;
		}



		



		/**
			* Método getDataValor()
			*
			* Retorna a data relativa ao valor do plano (data em que foi cadastrado o valor)
			* 
			* @return datetime
			*/
		public function getDataValor() {
			if (!isset($this->dDataValor)) {
				$aDados = $this->umPlanoBD->retornaDadosBasicos($this->getId());
				$this->sTitulo = $aDados["titulo"];
				$this->bPromocao = $aDados["promocao"];
				$this->dDataValor = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
			} 
			return $this->dDataValor;
		}



		



		/**
			* Método getValor()
			*
			* Retorna o valor do plano
			* 
			* @return float
			*/
		public function getValor() {
			if (!isset($this->fValor)) {
				$aDados = $this->umPlanoBD->retornaDadosBasicos($this->getId());
				$this->sTitulo = $aDados["titulo"];
				$this->bPromocao = $aDados["promocao"];
				$this->dDataValor = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
			} 
			return $this->fValor;
		}



		



		/**
			* Método retornaJogosAssinados()
			*
			* Retorna uma coleção de jogos que o estudante assinou
			* 
			* @return mixed
			*/
		public function retornaJogosAssinados() {
			if (count($this->aJogosAssinados)==0) {
				$this->aJogosAssinados = $this->umPedidoBD->capturaTodosJogosDoPlano($this->getIdPlano());
			} 
			return $this->aJogosAssinados;
		}


}