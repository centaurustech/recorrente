	$(function() {
		// Validar forms
		if ($('#formCadastroPF').length>0) {
			$('#formCadastroPF').validate({
				ignore: [],		
				messages: {
					'#emailExiste': 'O email informado já foi cadastrado.',
					'#csenha' : 'Senha deve ser igual',
				}
			});
		}
		if ($('#formCadastroPJ').length>0) {
			$('#formCadastroPJ').validate({
				ignore: [],		
				messages: {
					'#emailExiste': 'O email informado já foi cadastrado.',
				}
			});
		}
		
		// Máscaras
		if ($('#inputCNPJ').length>0)
			$('#inputCNPJ').mask('99.999.999/9999-99');
		if ($('.phone_number_mask').length>0)
			$('.phone_number_mask').mask("(99) 9999-99999");
		if ($('#npAnoFundacao').length>0)
			$('#npAnoFundacao').mask('9999');
		if ($('#npNumFuncionarios').length>0)
			$('#npNumFuncionarios').mask('99999');
		if ($('#npNumVoluntarios').length>0)
			$('#npNumVoluntarios').mask('99999999');
		if ($('#npNumPessoasImpactadas').length>0)
			$('#npNumPessoasImpactadas').mask('9999999999');
		if ($('.money').length>0)
			$('.money').mask('000.000.000.000.000', {reverse: true});
		if ($('#inputCEP').length>0)
			$('#inputCEP').mask('99999-999');
		if ($('#inputDataNascimento').length>0)
			$('#inputDataNascimento').mask('99/99/9999');
		

		// faz o scroll até o elemento
		if ($('#checkboxConfirmar').length>0) {
			$("#checkboxConfirmar").click(function (){
				$('#divConteudoForm').show();
	            $('html, body').animate({
	                scrollTop: $("#pontoDeScroll").offset().top
	            }, 2000);
	        });
		}
	
		// Contadores dos textareas
		if ($('#formNovoProjeto').length>0) {
			if ($('#npComoChegouBenfeitoria').length>0) 
				$('#npComoChegouBenfeitoria').limit('200','#countComoChegouBenfeitoria');
			if ($('#npSobreVoce').length>0) 
				$('#npSobreVoce').limit('200','#countSobreVoce');
			if ($('#npResumoProjeto').length>0) 
				$('#npResumoProjeto').limit('140','#countResumoProjeto');
			if ($('#npMaisSobre').length>0) 
				$('#npMaisSobre').limit('500','#countMaisSobre');
			if ($('#npExperienciasPassadas').length>0) 
				$('#npExperienciasPassadas').limit('250','#countExperienciasPassadas');
			if ($('#npRecursosExternos').length>0) 
				$('#npRecursosExternos').limit('250','#countRecursosExternos');
			if ($('#npMeta1ObjetivosValor').length>0) 
				$('#npMeta1ObjetivosValor').limit('200','#countMeta1ObjetivosValor');
			if ($('#npMeta1Recompensa').length>0) 
				$('#npMeta1Recompensa').limit('200','#countMeta1Recompensa');
			if ($('#npMeta2ObjetivosValor').length>0) 
				$('#npMeta2ObjetivosValor').limit('200','#countMeta2ObjetivosValor');
			if ($('#npMeta2Recompensa').length>0) 
				$('#npMeta2Recompensa').limit('200','#countMeta2Recompensa');
			if ($('#npMeta3ObjetivosValor').length>0) 
				$('#npMeta3ObjetivosValor').limit('200','#countMeta3ObjetivosValor');
			if ($('#npMeta3Recompensa').length>0) 
				$('#npMeta3Recompensa').limit('200','#countMeta3Recompensa');
			if ($('#npComoSeraArrecadacao').length>0) 
				$('#npComoSeraArrecadacao').limit('300','#countComoSeraArrecadacao');
			if ($('#npResumoAtividade').length>0) 
				$('#npResumoAtividade').limit('140','#countResumoAtividade');
		}
		
		// Adiciona cópias do elemento LINK no form de cadastro de projeto
		if ($('#btnAddLink').length>0) {
			$("#btnAddLink").click(function () {
	            if( ($('.bloco-inicial .bloco-final').length+1) > 5) {
	                alert("Você só pode adicionar 5 itens");
	                return false;
	            }
	            var id = ($('.bloco-inicial .bloco-final .item').length + 1).toString();
	            $('.bloco-final').append('<div class="row item" id="bloco-final' + id + '"><div class="form-group col-md-12"><label for="npTitulo">Título *</label><br/><input type="text" name="link_titulo[]" id="npTitulo" class="form-control" required></div><div class="form-group col-md-12"><label for="npURL">URL *</label><br/><input type="text" name="link_url[]" id="npURL" class="form-control" required></div></div>');
	        });
	
	        $("#btnRemoveLink").click(function () {
	            if ($('.bloco-inicial .bloco-final .item').length == 0) {
	                //alert("Você deve informar ao menos um link");
	                return false;
	            }
	
	            $(".bloco-inicial .bloco-final .item:last").remove();
	        });
		}
	
	});

	
	
	
	// Faz o tratamento de validação para os campos que ficam escondidos
	function enviaFormPF() {
		if ($('#divSeletorEstado').is(':visible')) {
			$('#formCadastroPF').validate();
			$('#novo_estado').rules('remove','required');
			$('#estado').rules('add','required');
		}
		else {
			$('#formCadastroPF').validate();
			$('#novo_estado').rules('add','required');
			$('#estado').rules('remove','required');
		}
		if ($('#divSeletorCidade').is(':visible')) {
			$('#formCadastroPF').validate();
			$('#nova_cidade').rules('remove','required');
			$('#cidade').rules('add','required');
		}
		else {
			$('#formCadastroPF').validate();
			$('#nova_cidade').rules('add','required');
			$('#cidade').rules('remove','required');
		}
		if ($('#email').val()!='') {
			if ($('#emailExiste').val()!='') {
				$('#formCadastroPF').validate();
				$('#emailExiste').rules('add',{
			        required: true,
			        messages: {
			            required: "Este e-mail já está em uso.",
			        }
				  }
				);
			}
			else {
				$('#formCadastroPF').validate();
				$('#emailExiste').rules('remove','required');
			}
		}
		if ($('#formCadastroPF').valid()) {
			$('#formCadastroPF').submit();
		}
	}

	
	
	
	// Faz o tratamento de validação para os campos que ficam escondidos
	function enviaFormPJ() {
		if ($('#divSeletorEstado').is(':visible')) {
			$('#formCadastroPJ').validate();
			$('#novo_estado').rules('remove','required');
			$('#estado').rules('add','required');
		}
		else {
			$('#formCadastroPJ').validate();
			$('#novo_estado').rules('add','required');
			$('#estado').rules('remove','required');
		}
		if ($('#divSeletorCidade').is(':visible')) {
			$('#formCadastroPJ').validate();
			$('#nova_cidade').rules('remove','required');
			$('#cidade').rules('add','required');
		}
		else {
			$('#formCadastroPJ').validate();
			$('#nova_cidade').rules('add','required');
			$('#cidade').rules('remove','required');
		}
		if ($('#inputEmail').val()!='') {
			if ($('#emailExiste').val()!='') {
				$('#formCadastroPJ').validate();
				$('#emailExiste').rules('add',{
			        required: true,
			        messages: {
			            required: "Este e-mail já está em uso.",
			        }
				  }
				);
			}
			else {
				$('#formCadastroPJ').validate();
				$('#emailExiste').rules('remove','required');
			}
		}
		if ($('#formCadastroPJ').valid()) {
			$('#formCadastroPJ').submit();
		}
	}

	
	
	
	// Faz o tratamento de validação para os campos que ficam escondidos
	function enviaFormNovoProjeto() {
		$('#formNovoProjeto').validate({
			rules: {
				npAnoFundacao: {
			      required: true,
			      minlength: 4
			    }
			},
			errorElement: 'div',
			errorPlacement: function(error,element){
				if(element.parent().parent().attr('class') == 'optionBox'){
					error.prependTo(element.parent().parent());
				} else if(element.parent().parent().parent().attr('class') == 'optionBox secondOne'){
					error.prependTo(element.parent().parent().parent());
				} else {
					error.appendTo(element.parent());
				}
			}
		});
		if ($('#formNovoProjeto').valid()) {
			$('#formNovoProjeto').submit();
		}
	}

	
	
	
	// Valida e envia o formulário de novo cadastro de PF por ajax
	function enviaFormContribuicaoNovoCadastroPF() {
		if ($('#divSeletorEstado').is(':visible')) {
			$('#formCadastroPF').validate();
			$('#inputNovoEstado').rules('remove','required');
			$('#estado').rules('add','required');
			$('#formCadastroPF').validate();
		}
		else {
			$('#formCadastroPF').validate();
			$('#inputNovoEstado').rules('add','required');
			$('#estado').rules('remove','required');
			$('#formCadastroPF').validate();
		}
		if ($('#divSeletorCidade').is(':visible')) {
			$('#formCadastroPF').validate();
			$('#inputNovaCidade').rules('remove','required');
			$('#cidade').rules('add','required');
			$('#formCadastroPF').validate();
		}
		else {
			$('#formCadastroPF').validate();
			$('#inputNovaCidade').rules('add','required');
			$('#cidade').rules('remove','required');
			$('#formCadastroPF').validate();
		}
		if ($('#inputEmail').val()!='') {
			if ($('#emailExiste').val()!='') {
				$('#formCadastroPF').validate();
				$('#emailExiste').rules('add',{
			        required: true,
			        messages: {
			            required: "Este e-mail já está em uso.",
			        }
				  }
				);
			}
			else {
				$('#formCadastroPF').validate();
				$('#emailExiste').rules('remove','required');
			}
		}
		if ($('#formCadastroPF').valid()) {
			$('#divLoadingContent').show();
			iProjectId = $('#project_id').val();
			$.ajax({
				  type: 'POST',
				  url: sBasePath+'/criaCadastro',
				  dataType: 'json',
			      data: {ajax: 'sim', project_id: $('#inputProjectId').val(), tipo: 'PF', email: $('#inputEmail').val(), senha1: $('#inputSenha1').val(), senha2: $('#inputSenha2').val(), nome: $('#inputNomeCompleto').val(), sexo: $('#inputSexo').val(), pais: $('#inputPais').val(), estado: $('#estado').val(), novo_estado: $('#inputNovoEstado').val(), cidade: $('#cidade').val(), nova_cidade: $('#inputNovaCidade').val() },
			      cache: false,
				  timeout: 10000,
				  success: function(json) {
					  $('#divLoadingContent').hide();
					  if (json.retorno=='ok') {
						  if (json.status_user=='waiting') {
							  // Direciona para a tela de confirmação de cadastro por email
							  $('#divConteudo').load(sBasePath+'/projeto/ajaxContribuicaoValidacaoCadastro/'+$('#inputProjectId').val());
					  	  }
						  if (json.status_user=='active') {
							  // Direciona para a tela de login diretamente pois o cadastro foi realizado e ativado
							  $('#divConteudo').load(sBasePath+'/projeto/ajaxContribuicaoP0/'+$('#inputProjectId').val()+'_'+json.user_id);
					  	  }
					  }
					  else {
						  $('#spanMensagemErroGeral').html('Ocorreu um problema ao realizar seu cadastro. Pedimos que tente novamente.');
					  }
				  },
				  error: function (jqXHR, textStatus, errorThrown) {
				  	switch (textStatus) {
					  	case 'timeout'	:	$('#spanMensagemErroGeral').html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\" onClick=\"CarregaConteudoAjax(sURL,sClass,sLoading)\">Tentar novamente</a></strong></div>');
					  										break;
					  	default         : 	$('#spanMensagemErroGeral').html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\">Erro: </a></strong>'+errorThrown+'</div>');
					  										break;
				  	}
					return false;
			  	  }
				});
		}
	}

	
	
	
	// Valida e envia o código de ativação do cadastro
	function enviaValidaCodigo() {
		$('#divLoadingContent').show();
		if ($('#formValidaCodigo').valid()) {
			iProjectId = $('#project_id').val();
			$.ajax({
				  type: 'POST',
				  url: sBasePath+'/validaCodigoCadastro',
				  dataType: 'json',
			      data: {project_id: iProjectId, code: $('#inputCodigoValidacao').val() },
			      cache: false,
				  timeout: 10000,
				  success: function(json) {
					  $('#divLoadingContent').hide();
					  if (json.retorno=='ok') {
						  // Direciona para a tela de confirmação de cadastro por email
						  $('#divConteudo').load(sBasePath+'/projeto/ajaxContribuicaoP0/'+iProjectId+'_'+json.user_id);
					  }
					  else {
						  $('#divMensagemErroGeral').html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>Código inválido. Tente novamente.</div>');
					  }
				  },
				  error: function (jqXHR, textStatus, errorThrown) {
					  $('#divLoadingContent').hide();
				  	switch (textStatus) {
					  	case 'timeout'	:	$('#divMensagemErroGeral').html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\" onClick=\"CarregaConteudoAjax(sURL,sClass,sLoading)\">Tentar novamente</a></strong></div>');
					  										break;
					  	default         : 	$('#divMensagemErroGeral').html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\">Erro: </a></strong>'+errorThrown+'</div>');
					  										break;
				  	}
					return false;
			  	  }
				});
		}
	}

	
	
	function reenviaEmailValidacao(sEmail) {
		$.ajax({
			  type: 'POST',
			  url: sBasePath+'/reenviaEmailAtivacao',
			  dataType: 'json',
		      data: {email: sEmail },
		      cache: false,
			  timeout: 10000,
			  success: function(json) {
				  $('#divLoadingContent').hide();
				  if (json.retorno=='ok') {
					  // Esconde o modal
					  $('#modalPoliticaPrivacidade').hide();
					  // Mostra mensagem de envio
					  $('#divMensagemSucesso').show();
				  }
				  else {
					  $('#spanMensagemErroGeral').html('Ocorreu um problema ao realizar seu cadastro. Pedimos que tente novamente.');
				  }
			  },
			  error: function (jqXHR, textStatus, errorThrown) {
			  	switch (textStatus) {
				  	case 'timeout'	:	$('#spanMensagemErroGeral').html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\" onClick=\"CarregaConteudoAjax(sURL,sClass,sLoading)\">Tentar novamente</a></strong></div>');
				  										break;
				  	default         : 	$('#spanMensagemErroGeral').html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\">Erro: </a></strong>'+errorThrown+'</div>');
				  										break;
			  	}
				return false;
		  	  }
			});

	}
	
	
	
	function verificaEmail(sURL,sEmail) {
		$('#divLoadingEmail').show();
		$.ajax({
			  type: 'POST',
			  url: sURL,
			  dataType: 'json',
		      data: {email: sEmail},
		      cache: false,
			  timeout: 10000,
			  success: function(json) {
				  $('#divLoadingEmail').hide();
				  if (json.retorno=='existe') {
					  $('#emailExiste').val('em uso');
					  $('#spanMensagemErro').html('<div class=\"alert alert-danger\"><strong>Este email já está cadastrado. Por favor, escolha outro.</strong></div>');
				  }
				  else {
					  $('#emailExiste').val('');
					  $('#spanMensagemErro').html('');
				  }
			  },
			  error: function (jqXHR, textStatus, errorThrown) {
			  	switch (textStatus) {
				  	case 'timeout'	:	$('#spanMensagemErro').html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\" onClick=\"CarregaConteudoAjax(sURL,sClass,sLoading)\">Tentar novamente</a></strong></div>');
				  										break;
				  	default         : 	$('#spanMensagemErro').html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\">Erro: </a></strong>'+errorThrown+'</div>');
				  										break;
			  	}
				return false;
		  	  }
			});
		
	}

	
	
	
	function verificaEmailReenvio(sURL,sEmail) {
		$('#divLoadingEmail').show();
		$.ajax({
			  type: 'POST',
			  url: sURL,
			  dataType: 'json',
		      data: {email: sEmail},
		      cache: false,
			  timeout: 10000,
			  success: function(json) {
				  $('#divLoadingEmail').hide();
				  if (json.retorno=='existe') {
					  $('#spanMensagemErro').html('<div class=\"alert alert-success\"><strong>Confirme o reenvio abaixo.</strong></div>');
					  $('#btnReenvio').removeAttr('disabled');
				  }
				  else {
					  $('#spanMensagemErro').html('<div class=\"alert alert-danger\"><strong>E-mail inexistente.</strong></div>');
					  $('#btnReenvio').attr('disabled','disabled');
				  }
			  },
			  error: function (jqXHR, textStatus, errorThrown) {
			  	switch (textStatus) {
				  	case 'timeout'	:	$('#spanMensagemErro').html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\" onClick=\"CarregaConteudoAjax(sURL,sClass,sLoading)\">Tentar novamente</a></strong></div>');
				  										break;
				  	default         : 	$('#spanMensagemErro').html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\">Erro: </a></strong>'+errorThrown+'</div>');
				  										break;
			  	}
				return false;
		  	  }
			});
		
	}
	
	
	// Carrega conteúdo dinâmico via AJAX
	function loadAjaxContent(sURL,sClass,sLoading,iTimeOut) {
		if (!iTimeOut) {
			iTimeOut = 8000;
		}
		$(sLoading).show();
		$.ajax({
		  type: 'POST',
		  url: sURL,
		  dataType: 'html',
		  cache: false,
		  timeout: iTimeOut,
		  success: function(data) {
			  $(sLoading).hide();
			  $(sClass).html(data);
			  return true;
		  },
		  error: function (jqXHR, textStatus, errorThrown) {
		  	switch (textStatus) {
			  	case 'timeout'	:	$(sClass).html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\" onClick=\"CarregaConteudoAjax(sURL,sClass,sLoading)\">Tentar novamente</a></strong></div>');
			  										break;
			  	default         : 	$(sClass).html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\" onClick=\"CarregaConteudoAjax(sURL,sClass,sLoading)\">Tentar novamente</a></strong></div>'+errorThrown);
			  										break;
		  	}
			return false;
	  	  }
		});
	}

	
	// Chama o evento de carregamento dinamico quando se escolhe um país
	function selecionaPais(sIdPais) {
		if (sIdPais==5) {
			$('#divSeletorEstado').show();
			$('#divInputEstado').hide();
			loadAjaxContent(sBasePath+'/ajaxListaEstados/5','#estado','#divLoadingEstado',10000);
		}
		else {
			$('#divSeletorEstado').hide();
			$('#divInputEstado').show();
		}
	}
	
	

	
	// Chama o evento de carregamento dinamico quando se escolhe um estado
	function selecionaCidade(sSiglaEstado) {
		$('#divSeletorCidade').show();
		$('#divInputCidade').hide();
		loadAjaxContent(sBasePath+'/ajaxListaCidadesDoEstado/'+sSiglaEstado,'#cidade','#divLoadingCidade',10000);
	}	
	
	
	

	
	// Verifica se o email está livre ou não
	function verificaDisponibilidadeEmail(sEmail) {
		$.ajax({
			  type: 'POST',
			  url: sBasePath+'/',
			  dataType: 'html',
			  cache: false,
			  timeout: iTimeOut,
			  success: function(data) {
				  $(sClass).html(data);
				  return true;
			  },
			  error: function (jqXHR, textStatus, errorThrown) {
			  	switch (textStatus) {
				  	case 'timeout'	:	$(sClass).html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\" onClick=\"CarregaConteudoAjax(sURL,sClass,sLoading)\">Tentar novamente</a></strong></div>');
				  										break;
				  	default         : 	$(sClass).html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\" onClick=\"CarregaConteudoAjax(sURL,sClass,sLoading)\">Tentar novamente</a></strong></div>'+errorThrown);
				  										break;
			  	}
				return false;
		  	  }
			});
	}
	
	
	
	// Inicia a interface de contribuição
	function contribuir(iProjectId,iIdUsuarioLogado) {
		if (!iIdUsuarioLogado) {
			$('#divConteudo').fadeOut('slow', function(){
				$(".loader").fadeIn('fast');
				$(this).load(sBasePath+'/projeto/ajaxContribuicaoP0/'+iProjectId, function(){
					$(".loader").fadeOut('fast', function(){ $('#divConteudo').fadeIn(); });
				})	
			})
		}
		else {
			$('#divConteudo').fadeOut('slow', function(){
				$(".loader").fadeIn('fast');
				$(this).load(sBasePath+'/projeto/ajaxContribuicaoP1/'+iProjectId, function(){
					$(".loader").fadeOut('fast', function(){ $('#divConteudo').fadeIn(); });
				})
			})
		}
	}

	
	// Chama o procedimento de alteração de contribuicao
	function alterarContribuicao(iProjectId,iContributionId) {
		$('#divLoadingContent').show();
		$('#divConteudo').load(sBasePath+'/projeto/ajaxContribuicaoP1/'+iProjectId+'-'+iContributionId);
	}
	
	
	
	// Cancelar contribuição
	function cancelarContribuicao(iProjectId,iIdUsuarioLogado,iContributionId) {
		$('#divLoadingContent').show();
		$('#divConteudo').load(sBasePath+'/projeto/ajaxCancelaContribuicao/'+iProjectId+'-'+iIdUsuarioLogado+'-'+iContributionId);
	}
	
	
	
	// Cancelar contribuição
	function cancelarContribuicaoDireto(iProjectId,iIdUsuarioLogado,iContributionId) {
		document.location = sBasePath+'/projeto/cancelaContribuicaoDireto/'+iProjectId+'-'+iIdUsuarioLogado+'-'+iContributionId;
	}
	
	
	
	// 
	function contribuicaoLogin(sLogin,sSenha,iProjectId,sURL) {
		$.ajax({
			  type: 'POST',
			  url: sBasePath+'/seguro/ajaxValidaLogin',
			  data: { usuario:sLogin, senha:sSenha, id_projeto:iProjectId },
			  dataType: 'json',
			  cache: false,
			  timeout: 8000,
			  success: function(data) {
				  switch (data.status) {
				  	case 'ok' : // Fez login, então troca o cabeçalho e também troca para o passo P1
				  				$('#divMenuContent').load(sBasePath+'/ajaxV1HeaderLogged/u='+sURL)
				  				$('#divConteudo').load(sBasePath+'/projeto/ajaxContribuicaoP1/'+iProjectId);
				  				break;
				  	case 'ja-contribuiu' : // Já contribuiu.
				  				document.location = sBasePath+'/projeto/detalhes/'+iProjectId;
				  				break;
				  	default   : $('#divError').show();
				  				$('#divError').html('<b class="red">Usuário ou senha incorretos. Tente novamente.</b>');
				  				break;
				  }
				  return true;
			  },
			  error: function (jqXHR, textStatus, errorThrown) {
			  	switch (textStatus) {
				  	case 'timeout'	:	alert('timeout');$(sClass).html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\" onClick=\"CarregaConteudoAjax(sURL,sClass,sLoading)\">Tentar novamente</a></strong></div>');
				  										break;
				  	default         : 	alert(errorThrown);$(sClass).html('<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong><a href=\"#\" onClick=\"CarregaConteudoAjax(sURL,sClass,sLoading)\">Tentar novamente</a></strong></div>'+errorThrown);
				  										break;
			  	}
				return false;
		  	  }
			});
	}
	
	//Coisas da interface de Crop
	var options = {
        thumbBox: '.thumbBox',
        spinner: '.spinner'
    };
  
    var cropper = $('.imageBox').cropbox(options);
    
    $('#file').on('change', function(){
        var reader = new FileReader();
        reader.onload = function(e) {
            options.imgSrc = e.target.result;
            cropper = $('.imageBox').cropbox(options);
        }
        reader.readAsDataURL(this.files[0]);
        this.files = [];
    })
    $('#btnCrop').on('click', function(){
        var img = cropper.getAvatar();
        $('.cropped').html('').append('<img src="'+img+'">');
        $('#image_data').val(img);
    })
    $('#btnZoomIn').on('click', function(){
        cropper.zoomIn();
    })
    $('#btnZoomOut').on('click', function(){
        cropper.zoomOut();
    });

    //Inicializa o tooltip em todos os elementos com atr.
    $('[title!=""]').qtip(); 

    //Isso era para fazer funcionar os tooltips por ajax, porem ta deixando a página muito lenta, fica para investigar...
    // $(document).on('mouseover','[title!=""]', function(){
    // 	$('[title!=""]').qtip(); 
    // })

    //Obrigatório leitura de termos de assinatura no verProjeto->escolherValor
    $(document).on("click", "#liTermos" , function(){
    	if ($(this).is(':checked')) {
        	$('#btnConfirmar').prop('disabled', false);
        } else {
        	$('#btnConfirmar').prop('disabled', true);
        }
    });

    $("#campanha button").click(function() {
	    $('html, body').animate({
	        scrollTop: $("#divConteudo").offset().top
	    }, 2000);
	});

     $('.slider').bxSlider({
        slideSelector: 'div.slide',
        controls: false,
        randomStart: false,
        speed: 1000,
        preloadImages: 'all',
        auto: true,
        pause: 8000
      });
