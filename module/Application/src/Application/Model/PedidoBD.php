<?php
	namespace Application\Model;
	
	/**
		* Classe PedidoBD
		*
		* Responsável por operações em banco de dados. 
		* 
		* Sempre usar ANSI SQL 
		*/
	class PedidoBD {
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
		public function retornaDadosBasicos($iIdPedido) {
			$sQuery = "select id_responsavel,id_estudante,data,id_opcao,valor,cartao_bandeira,cartao_numero,cartao_ccv,cartao_nome,ano,mes,status,cpf from pedidos where id=" . $iIdPedido;
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
	