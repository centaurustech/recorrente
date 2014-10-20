<?php
	namespace Webadmin\Model;

	class WebAdmin {
		// Alimenta informações (o nome deste método não pode mudar pois é usado automaticamente)
		public function exchangeArray($aDados) {
			$this->iIdTipo = (isset($aDados['id_tipo'])) ? $aDados['id_tipo'] : null;
			$this->sNome = (isset($aDados['nome'])) ? $aDados['nome'] : null;
		}
	
	}
?>