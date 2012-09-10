(function($){
    $.fn.cropper = function(opcoes) {
        var obj = this;
        // opções padrão
        var defaults = {
            largura         : 500,
            altura          : 500,
            imagemOriginal  : null,
            imagemFinal     : 'novaImagem', //sem extensão
            filtros         : true,
            getWH           : 'getWH.php',
            preVisualizar   : 'previsualizar.php',
            wImg            : 0,
            hImg            : 0,
            quandoSalvar    : function(arquivoFinal) {},
            quandoCancelar  : function(arquivoFinal) {}
        };
       
        opcoes = $.extend(defaults, opcoes);
        
        var fecharCropper = function() {
            $('#cropper').html('');
            $('#cropper-sombra-pre,#cropper-modal-pre,#cropper-box-filtros').remove();
            
        }
        
        var salvarImg = function() {
            var _url = montaUrlImg();
            _url += '&salvar=true';
            _url += '&imagemFinal='+opcoes.imagemFinal;
            $.get(_url,{},function(data) {
                if(data==1){
                    if (typeof opcoes.quandoSalvar == 'function') {
                        opcoes.quandoSalvar.call('',opcoes.imagemFinal);
                        fecharCropper();
                    }
                }
            });
        }
        

        //monta html do cropper
        var cropper = function(imagem) {
            var _html = "";
            //Cropper
            _html += '<div id="cropper" class="cropper">';
            //Top
            _html += '<div class="cropper-top-bar">';
            //Left
            _html += '<div class="cropper-left-top-bar">';
            _html += '<div class="cropper-valor-slider">';
            _html += '<input type="text" id="cropper-tamanho-imagem" class="cropper-input" />';
            _html += '</div>';
            _html += '<div class="cropper-slider">';
            _html += '<div id="cropper-slider-imagem"></div>';
            _html += '</div>';
            _html += '</div>';
            //Right Botões
            _html += '<div class="cropper-right-top-bar">';
            _html += '<a href="javascript:void(0)" id="cropper-btn-salvar" class="cropper-btn">Cancelar</a>';
            _html += '<a href="javascript:void(0)" id="cropper-btn-previsualizar" class="cropper-btn cropper-btn-primary">Pr&eacute;-Visualizar</a>';
            _html += '</div>';
            _html += '</div>';

            //Caixa da Imagem
            _html += '<div class="cropper-box-img">';
            //Área do Crop
            _html += '<div class="cropper-crop"></div>';
            //Imagem a Ser Editada
            _html += '<img class="cropper-box-img-img" src="'+imagem+'" id="cropper-img" />';
            _html += '</div>';
            _html += '</div>';
            
            
            $(obj).replaceWith(_html);
            
        
            $('#cropper-btn-previsualizar').click(function() {
                previsualizar();
            });
            $('#cropper-btn-cancelar').click(function() {
                if (typeof opcoes.quandoSalvar == 'function') {
                    opcoes.quandoCancelar.call('',opcoes.imagemOriginal);
                    fecharCropper();
                }
            });        
                                
            centralizarImg(opcoes.wImg,opcoes.hImg);
            montaInterface();
        }


        var montaInterface = function(){
            //CROP
            $( ".cropper-crop" ).resizable({
                aspectRatio: true,
                containment:'parent'
            });
            $('.cropper-crop').width(opcoes.largura+'px').height(opcoes.altura+'px');
            $( ".cropper-crop" ).draggable({
                containment:'parent'
            });
            //SLIDER ZOOM IMAGEM
            $( "#cropper-slider-imagem" ).slider({
                value:100,
                min: 10,
                max: 200,
                step: 10,
                slide: function( event, ui ) {
                    $( "#cropper-tamanho-imagem" ).val(ui.value);
                    alterarTamanho(ui.value,opcoes.wImg,opcoes.hImg);
                }
            });
            $( "#cropper-tamanho-imagem" ).val($( "#cropper-slider-imagem" ).slider( "value" ));
            
        }
        
        
        //Centraliza a imagem
        var centralizarImg = function(w,h){
            var boxImgW = $('.cropper-box-img').width();
            var boxImgH = $('.cropper-box-img').height();
            var novoTop = (boxImgH/2)-(h/2);
            var novoLeft = (boxImgW/2)-(w/2);
            $('#cropper-img').css('top',(novoTop)+'px');
            $('#cropper-img').css('left',(novoLeft)+'px');    
        }
        
        //Altera o tamanho da imagem com o slider de zoom
        var alterarTamanho = function alterarTamanho(porcentagem,w,h){
            var novoW = (w*porcentagem)/100;
            var novoH = (h*porcentagem)/100;
            $('#cropper-img').width(novoW+'px').height(novoH+'px');            
            centralizarImg(novoW,novoH);
        }


        var abreOpcoes = function(){
            //Filtros
            var _html = '<div id="cropper-box-filtros" class="cropper-filtros">';
            _html += '<a href="javascript:;" id="cropper-btn-salvar" class="cropper-btn cropper-btn-primary">Salvar</a>';
            _html += '<a href="javascript:;" id="cropper-btn-original" class="cropper-btn">Usar a Original</a>';
            _html += '<a href="javascript:;" id="cropper-btn-editar" class="cropper-btn">Editar</a>';
            _html += '</div>';
            
            if(!document.getElementById('cropper-box-filtros')){
                $('body').append(_html);
            }
            $('#cropper-box-filtros').show();
            
        }
        
        var montarFiltros = function(){
        }


        var previsualizar = function(){
            
            abreOpcoes();
            var _url = montaUrlImg();
            var _margin = '-'+(Math.round(opcoes.altura/2)+10)+'px 0 0 -'+(Math.round((opcoes.largura-$('#cropper-box-filtros').width())/2))+'px';
            
            var _img = '<img src="'+_url+'" class="cropper-box-img-img" style="width:'+opcoes.altura+'px; height:'+opcoes.altura+'px;" />';
            
            
            var _html = '<div class="cropper-sombra-pre" id="cropper-sombra-pre"></div><div class="cropper-modal-pre" id="cropper-modal-pre" style="margin:'+_margin+'; width:'+opcoes.altura+'px; height:'+opcoes.altura+'px;">'+_img+'</div>';
            if(!document.getElementById('cropper-modal-pre')){
                $('body').append(_html);
                $('#cropper-sombra-pre,#cropper-btn-editar').live('click',function() {
                    $('#cropper-sombra-pre,#cropper-modal-pre,#cropper-box-filtros').hide();
                });
                $('#cropper-btn-salvar').live('click',function() {
                    salvarImg();
                });
                $('#cropper-btn-original').live('click',function() {
                    if (typeof opcoes.quandoSalvar == 'function') {
                        opcoes.quandoCancelar.call('',opcoes.imagemOriginal);
                        fecharCropper();
                    }
                });
        
            } else {
                $('#cropper-modal-pre').html(_img);
            }
            
            $('#cropper-sombra-pre,#cropper-modal-pre').show();
            
        }
        
        var dadosCrop = function() {
            
            var crop = { 
                x:0,
                y:0,
                w:0,
                h:0,
                wFinal:0,
                hFinal:0,
                escala:0
            };
            crop.escala = $('#cropper-tamanho-imagem').val();
            
            var cropX = $('.cropper-crop').css('left');
            if(cropX=="auto") cropX=0;
            else cropX = cropX.replace('px','');
                
            var cropY = $('.cropper-crop').css('top');
            if(cropY=="auto") cropY=0;
            else cropY = cropY.replace('px','');
                
            var imgX = $('#cropper-img').css('left');
            if(imgX=="auto") imgX=0;
            else imgX = imgX.replace('px','');
                
            var imgY = $('#cropper-img').css('top');
            if(imgY=="auto") imgY=0;
            else imgY = imgY.replace('px','');
                
            var boxImgY = $('.cropper-box-img').css('top');
            if(boxImgY=="auto") boxImgY=0;
            else boxImgY = boxImgY.replace('px','');
            
            var boxImgX = $('.cropper-box-img').css('left');
            if(boxImgX=="auto") boxImgX=0;
            else boxImgX = boxImgX.replace('px','');
                
            var cropW = $('.cropper-crop').width();
            var cropH = $('.cropper-crop').height();
                
            var wImg = $('#cropper-img').width();
            var hImg = $('#cropper-img').height();
                
            var difX = 0;
            var difY = 0;     
            if(imgX<0){
                difX = Math.abs(Math.abs(imgX)+Number(cropX));
            } else {
                difX = Math.abs(imgX-cropX);
            }
                
            if(imgY<0){
                difY = Math.abs(Math.abs(imgY)+Number(cropY));
            } else {
                difY = Math.abs(imgY-cropY);
            }

            //x e y do crop
            
            crop.x=difX;
            crop.y=difY;
            //w e h da imagem final
            crop.wFinal=opcoes.largura;
            crop.hFinal=opcoes.altura;
            //w e h atual
            crop.w=cropW;
            crop.h=cropH;
            
            
            
            return crop;
        }
        
        var montaUrlImg = function() {
            
            var dados = dadosCrop();
            
            var srcImg = opcoes.preVisualizar;
            //crop X e Y
            srcImg += '?cropX='+dados.x+'&cropY='+dados.y;
            //crop WFinal e HFinal
            srcImg += '&cropWFinal='+dados.wFinal+'&cropHFinal='+dados.hFinal;
            //crop WAtual e HAtual
            srcImg += '&cropW='+dados.w+'&cropH='+dados.h;
            //scala
            srcImg += '&scale='+dados.escala;
            imagem = opcoes.imagemOriginal
            srcImg += '&imagem='+imagem;
                
            return srcImg;
        }

        //INICIALIZA
        return this.each(function(){
            if(opcoes.imagemOriginal==null) {
                alert('Informe a imagem original.');
                return false;
            }
            if(opcoes.getWH!=null){
                $.post(opcoes.getWH,{
                    imagem:opcoes.imagemOriginal
                },function(data){
                    if(data!=0){
                        //alert(data);
                        var tamanho = data.split(',');
                    
                        opcoes.wImg = tamanho[0];
                        opcoes.hImg = tamanho[1];
                    
                        cropper(opcoes.imagemOriginal);
                    }
                });
            } else cropper(opcoes.imagemOriginal);
            
        });
    };
})(jQuery);


