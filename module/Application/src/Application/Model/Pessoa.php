<?php
	namespace Application\Model;
	
	use Application\Model\PedidoBD;
	
	// teste patao

	/**
		* Pedido
		*
		* Representa um pedido feito no sistema
		* 
		*/
	class Pessoa {
		protected $umServiceManager;
		protected $umPedidoBD;
		protected $iIdPedido;
		protected $iIdResponsavel;
		protected $iIdEstudante;
		protected $iIdPlano;
		protected $dDataPedido;
		protected $fValor;
		protected $sCartaoBandeira;
		protected $sCartaoNumero;
		protected $sCartaoCCV;
		protected $sCartaoNome;
		protected $iCartaoMesValidade;
		protected $iCartaoAnoValidade;
		protected $sStatus;
		protected $sCPF;
		protected $aJogosAssinados = array();

		
		/**
			* Método Construtor
			*
			* Inicializa o objeto, 
			* 
			* @param ServiceManager $umSM
			* @param int $iIdPedido
			*/
		public function __construct($umSM,$iIdPedido = null) {
			$this->umServiceManager = $umSM;
			$this->iIdPedido = $iIdPedido;
			$this->umPedidoBD = new PedidoBD($this->umServiceManager->get("Zend\Db\Adapter\Adapter"));
			$this->umPedidoBD->setServiceManager($this->umServiceManager);
		}




		
		
		/**
			* Método getId()
			*
			* Retorna o ID do pedido
			* 
			* @return int iIdEscola
			*/
		public function getId() {
			return $this->iIdPedido;
		}



		



		/**
			* Método getIdResponsavel()
			*
			* Retorna o id do usuário responsável
			* 
			* @return int
			*/
		public function getIdResponsavel() {
			if (!isset($this->iIdResponsavel)) {
				$aDados = $this->umPedidoBD->retornaDadosBasicos($this->getId());
				$this->iIdResponsavel = $aDados["id_responsavel"];
				$this->iIdEstudante = $aDados["id_estudante"];
				$this->iIdPlano = $aDados["id_opcao"];
				$this->dDataPedido = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
				$this->sCartaoBandeira = $aDados["cartao_bandeira"];
				$this->sCartaoNumero = $aDados["cartao_numero"];
				$this->sCartaoCCV = $aDados["cartao_ccv"];
				$this->sCartaoNome = $aDados["cartao_nome"];
				$this->iCartaoMesValidade = $aDados["mes"];
				$this->iCartaoAnoValidade = $aDados["ano"];
				$this->sCPF = $aDados["cpf"];
			} 
			return $this->iIdResponsavel;
		}



		



		/**
			* Método getIdEstudante()
			*
			* Retorna o id do usuário estudante
			* 
			* @return int
			*/
		public function getIdEstudante() {
			if (!isset($this->iIdEstudante)) {
				$aDados = $this->umPedidoBD->retornaDadosBasicos($this->getId());
				$this->iIdResponsavel = $aDados["id_responsavel"];
				$this->iIdEstudante = $aDados["id_estudante"];
				$this->iIdPlano = $aDados["id_opcao"];
				$this->dDataPedido = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
				$this->sCartaoBandeira = $aDados["cartao_bandeira"];
				$this->sCartaoNumero = $aDados["cartao_numero"];
				$this->sCartaoCCV = $aDados["cartao_ccv"];
				$this->sCartaoNome = $aDados["cartao_nome"];
				$this->iCartaoMesValidade = $aDados["mes"];
				$this->iCartaoAnoValidade = $aDados["ano"];
				$this->sCPF = $aDados["cpf"];
			} 
			return $this->iIdEstudante;
		}



		



		/**
			* Método getIdPlano()
			*
			* Retorna o id do plano assinado
			* 
			* @return int
			*/
		public function getIdPlano() {
			if (!isset($this->iIdPlano)) {
				$aDados = $this->umPedidoBD->retornaDadosBasicos($this->getId());
				$this->iIdResponsavel = $aDados["id_responsavel"];
				$this->iIdEstudante = $aDados["id_estudante"];
				$this->iIdPlano = $aDados["id_opcao"];
				$this->dDataPedido = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
				$this->sCartaoBandeira = $aDados["cartao_bandeira"];
				$this->sCartaoNumero = $aDados["cartao_numero"];
				$this->sCartaoCCV = $aDados["cartao_ccv"];
				$this->sCartaoNome = $aDados["cartao_nome"];
				$this->iCartaoMesValidade = $aDados["mes"];
				$this->iCartaoAnoValidade = $aDados["ano"];
				$this->sCPF = $aDados["cpf"];
			} 
			return $this->iIdPlano;
		}



		



		/**
			* Método getDataPedido()
			*
			* Retorna a data do pedido
			* 
			* @return date
			*/
		public function getDataPedido() {
			if (!isset($this->dDataPedido)) {
				$aDados = $this->umPedidoBD->retornaDadosBasicos($this->getId());
				$this->iIdResponsavel = $aDados["id_responsavel"];
				$this->iIdEstudante = $aDados["id_estudante"];
				$this->iIdPlano = $aDados["id_opcao"];
				$this->dDataPedido = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
				$this->sCartaoBandeira = $aDados["cartao_bandeira"];
				$this->sCartaoNumero = $aDados["cartao_numero"];
				$this->sCartaoCCV = $aDados["cartao_ccv"];
				$this->sCartaoNome = $aDados["cartao_nome"];
				$this->iCartaoMesValidade = $aDados["mes"];
				$this->iCartaoAnoValidade = $aDados["ano"];
				$this->sCPF = $aDados["cpf"];
			} 
			return $this->dDataPedido;
		}



		



		/**
			* Método getValor()
			*
			* Retorna o id do plano assinado
			* 
			* @return float
			*/
		public function getValor() {
			if (!isset($this->fValor)) {
				$aDados = $this->umPedidoBD->retornaDadosBasicos($this->getId());
				$this->iIdResponsavel = $aDados["id_responsavel"];
				$this->iIdEstudante = $aDados["id_estudante"];
				$this->iIdPlano = $aDados["id_opcao"];
				$this->dDataPedido = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
				$this->sCartaoBandeira = $aDados["cartao_bandeira"];
				$this->sCartaoNumero = $aDados["cartao_numero"];
				$this->sCartaoCCV = $aDados["cartao_ccv"];
				$this->sCartaoNome = $aDados["cartao_nome"];
				$this->iCartaoMesValidade = $aDados["mes"];
				$this->iCartaoAnoValidade = $aDados["ano"];
				$this->sCPF = $aDados["cpf"];
			} 
			return $this->fValor;
		}



		



		/**
			* Método getCartaoBandeira()
			*
			* Retorna a bandeira do cartão
			* 
			* @return string
			*/
		public function getCartaoBandeira() {
			if (!isset($this->sCartaoBandeira)) {
				$aDados = $this->umPedidoBD->retornaDadosBasicos($this->getId());
				$this->iIdResponsavel = $aDados["id_responsavel"];
				$this->iIdEstudante = $aDados["id_estudante"];
				$this->iIdPlano = $aDados["id_opcao"];
				$this->dDataPedido = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
				$this->sCartaoBandeira = $aDados["cartao_bandeira"];
				$this->sCartaoNumero = $aDados["cartao_numero"];
				$this->sCartaoCCV = $aDados["cartao_ccv"];
				$this->sCartaoNome = $aDados["cartao_nome"];
				$this->iCartaoMesValidade = $aDados["mes"];
				$this->iCartaoAnoValidade = $aDados["ano"];
				$this->sCPF = $aDados["cpf"];
			} 
			return $this->sCartaoBandeira;
		}



		



		/**
			* Método getCartaoNumero()
			*
			* Retorna número do cartão (somente os 4 últimos dígitos se a compra foi autorizada)
			* 
			* @return string
			*/
		public function getCartaoNumero() {
			if (!isset($this->sCartaoNumero)) {
				$aDados = $this->umPedidoBD->retornaDadosBasicos($this->getId());
				$this->iIdResponsavel = $aDados["id_responsavel"];
				$this->iIdEstudante = $aDados["id_estudante"];
				$this->iIdPlano = $aDados["id_opcao"];
				$this->dDataPedido = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
				$this->sCartaoBandeira = $aDados["cartao_bandeira"];
				$this->sCartaoNumero = $aDados["cartao_numero"];
				$this->sCartaoCCV = $aDados["cartao_ccv"];
				$this->sCartaoNome = $aDados["cartao_nome"];
				$this->iCartaoMesValidade = $aDados["mes"];
				$this->iCartaoAnoValidade = $aDados["ano"];
				$this->sCPF = $aDados["cpf"];
			} 
			return $this->sCartaoNumero;
		}



		



		/**
			* Método getCartaoCCV()
			*
			* Retorna o CCV do cartão
			* 
			* @return string
			*/
		public function getCartaoCCV() {
			if (!isset($this->sCartaoCCV)) {
				$aDados = $this->umPedidoBD->retornaDadosBasicos($this->getId());
				$this->iIdResponsavel = $aDados["id_responsavel"];
				$this->iIdEstudante = $aDados["id_estudante"];
				$this->iIdPlano = $aDados["id_opcao"];
				$this->dDataPedido = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
				$this->sCartaoBandeira = $aDados["cartao_bandeira"];
				$this->sCartaoNumero = $aDados["cartao_numero"];
				$this->sCartaoCCV = $aDados["cartao_ccv"];
				$this->sCartaoNome = $aDados["cartao_nome"];
				$this->iCartaoMesValidade = $aDados["mes"];
				$this->iCartaoAnoValidade = $aDados["ano"];
				$this->sCPF = $aDados["cpf"];
			} 
			return $this->sCartaoCCV;
		}



		



		/**
			* Método getCartaoNome()
			*
			* Retorna o nome impresso no cartão
			* 
			* @return string
			*/
		public function getCartaoNome() {
			if (!isset($this->sCartaoNome)) {
				$aDados = $this->umPedidoBD->retornaDadosBasicos($this->getId());
				$this->iIdResponsavel = $aDados["id_responsavel"];
				$this->iIdEstudante = $aDados["id_estudante"];
				$this->iIdPlano = $aDados["id_opcao"];
				$this->dDataPedido = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
				$this->sCartaoBandeira = $aDados["cartao_bandeira"];
				$this->sCartaoNumero = $aDados["cartao_numero"];
				$this->sCartaoCCV = $aDados["cartao_ccv"];
				$this->sCartaoNome = $aDados["cartao_nome"];
				$this->iCartaoMesValidade = $aDados["mes"];
				$this->iCartaoAnoValidade = $aDados["ano"];
				$this->sCPF = $aDados["cpf"];
			} 
			return $this->sCartaoNome;
		}



		



		/**
			* Método getCartaoMesValidade()
			*
			* Retorna o mês de validade do cartão
			* 
			* @return int
			*/
		public function getCartaoMesValidade() {
			if (!isset($this->iCartaoMesValidade)) {
				$aDados = $this->umPedidoBD->retornaDadosBasicos($this->getId());
				$this->iIdResponsavel = $aDados["id_responsavel"];
				$this->iIdEstudante = $aDados["id_estudante"];
				$this->iIdPlano = $aDados["id_opcao"];
				$this->dDataPedido = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
				$this->sCartaoBandeira = $aDados["cartao_bandeira"];
				$this->sCartaoNumero = $aDados["cartao_numero"];
				$this->sCartaoCCV = $aDados["cartao_ccv"];
				$this->sCartaoNome = $aDados["cartao_nome"];
				$this->iCartaoMesValidade = $aDados["mes"];
				$this->iCartaoAnoValidade = $aDados["ano"];
				$this->sCPF = $aDados["cpf"];
			} 
			return $this->iCartaoMesValidade;
		}



		



		/**
			* Método getCartaoAnoValidade()
			*
			* Retorna o mês de validade do cartão
			* 
			* @return int
			*/
		public function getCartaoAnoValidade() {
			if (!isset($this->iCartaoAnoValidade)) {
				$aDados = $this->umPedidoBD->retornaDadosBasicos($this->getId());
				$this->iIdResponsavel = $aDados["id_responsavel"];
				$this->iIdEstudante = $aDados["id_estudante"];
				$this->iIdPlano = $aDados["id_opcao"];
				$this->dDataPedido = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
				$this->sCartaoBandeira = $aDados["cartao_bandeira"];
				$this->sCartaoNumero = $aDados["cartao_numero"];
				$this->sCartaoCCV = $aDados["cartao_ccv"];
				$this->sCartaoNome = $aDados["cartao_nome"];
				$this->iCartaoMesValidade = $aDados["mes"];
				$this->iCartaoAnoValidade = $aDados["ano"];
				$this->sCPF = $aDados["cpf"];
			} 
			return $this->iCartaoAnoValidade;
		}



		



		/**
			* Método getCPF()
			*
			* Retorna o CPF vinculado ao pedido
			* 
			* @return string
			*/
		public function getCPF() {
			if (!isset($this->sCPF)) {
				$aDados = $this->umPedidoBD->retornaDadosBasicos($this->getId());
				$this->iIdResponsavel = $aDados["id_responsavel"];
				$this->iIdEstudante = $aDados["id_estudante"];
				$this->iIdPlano = $aDados["id_opcao"];
				$this->dDataPedido = strtotime($aDados["data"]);
				$this->fValor = $aDados["valor"];
				$this->sCartaoBandeira = $aDados["cartao_bandeira"];
				$this->sCartaoNumero = $aDados["cartao_numero"];
				$this->sCartaoCCV = $aDados["cartao_ccv"];
				$this->sCartaoNome = $aDados["cartao_nome"];
				$this->iCartaoMesValidade = $aDados["mes"];
				$this->iCartaoAnoValidade = $aDados["ano"];
				$this->sCPF = $aDados["cpf"];
			} 
			return $this->sCPF;
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