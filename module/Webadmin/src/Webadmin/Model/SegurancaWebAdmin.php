<?php
namespace Webadmin\Model;

use Application\Model\Seguranca;

class SegurancaWebAdmin extends Seguranca {
	private $dbAdapter;
	private $umRenderer;

	public function __construct($sm,$umRenderer) {
		$this->dbAdapter = $sm->get("Zend\Db\Adapter\Adapter");
		$this->umRenderer = $umRenderer;
	}

	/* Valida a sessão para ver se está ativa (usuário está logado) */
	public function sessaoAtiva($umObj) {
		$umContainer = $umObj->getContainer();
	  $umRenderer =  $umObj->getRenderer();
		if (isset($umContainer->sUsuarioAdminLogado)) {
			if ($umContainer->sUsuarioAdminLogado!="") {
				return true;
			}
			else {
				$umObj->plugin('redirect')->toUrl($umRenderer->basePath('/wbadmin/logout/expirou'));
			}
		}
		else {
			$umObj->plugin('redirect')->toUrl($umRenderer->basePath('/wbadmin/logout/expirou'));
		}
	}

	
	
	/* Controle de usuários na sessão */
	public function validaLogin($sUsuario,$sSenha) {
		$sQuery = "
        select 
        u.id 
        
        from 
        users u
        inner join users_profiles up on up.id_user=u.id
        
        where 
        u.email='" . $sUsuario . "' and 
        u.password='" . md5($sSenha) . "' and
        up.id_profile='3'
        ";
		$umStandment = $this->dbAdapter->query($sQuery);
		$umResultSet = $umStandment->execute();
		if ($umResultSet->count()>0) {
			return true;
		}
		else {
			return false;
		}
	}
    
}
