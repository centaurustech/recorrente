<?php
	namespace Application\Model;
	
	/**
		* Classe PlanoBD
		*
		* Responsável por operações em banco de dados. 
		* 
		* Sempre usar ANSI SQL 
		*/
	class PlanoBD {
		protected $dbAdapter;
		protected $umServiceManager;
		
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
			* Método setServiceManager()
			*
			* Recebe um apontador para o service manager
			* 
			* @param object $umSM
			*/
		public function setServiceManager($umSM) {
			$this->umServiceManager = $umSM;
		}


		
		
		/**
			* Método retornaDadosBasicos()
			*
			* Recebe uma coleção de informações
			* 
			* @param int $iIdPedido
			* @return mixed $linha
			*/
		public function retornaDadosBasicos($iIdPlano) {
			$sQuery = "select o.titulo,o.promocao,ov.data,ov.valor from opcoes as o inner join opcoes_valores as ov on o.id=ov.id_opcao where o.id=" . $iIdPlano;
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				$linha = $umResultSet->next();
			}
			return $linha;
		}


		
		
		/**
			* Método capturaTodosJogosDoPlano()
			*
			* Captura os ids dos jogos de um plano específico
			* 
			* @param int $iIdPlano
			* @return mixed aIdsJogos
			*/
		public function capturaTodosJogosDoPlano($iIdPlano) {
			$aIdsJogos = array();
			$sQuery = "select id_produto from opcoes_produtos where id_opcao='".$iIdPlano."'";
			if ($umResultSet = $this->dbAdapter->query($sQuery)->execute()) {
				while ($linha = $umResultSet->next()) {
					array_push($aIdsJogos,$linha["id_produto"]);
				}
			}
			return $aIdsJogos;
		}

	}
	