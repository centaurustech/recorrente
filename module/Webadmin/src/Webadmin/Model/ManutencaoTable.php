<?php
	namespace Webadmin\Model;
	
	use Zend\Db\TableGateway\TableGateway;
	use Zend\Db\Sql\Where;
	
	class ManutencaoTable {
		protected $umaTableGateway;
		
		public function __construct(TableGateway $umaTableGateway) {
			$this->umaTableGateway = $umaTableGateway;
		}
		
		
		public function validaUsuario($sUsuario,$sSenha) {
			$umResultSet = $this->umaTableGateway->select("id")->from("usuario")->where("usuario=" . $sUsuario . " and senha=" . $sSenha);
			return $umResultSet;
		}
	}
