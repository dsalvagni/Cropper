(function($){
    $.fn.cropper = function(options) {
        var obj = this;
        // opções padrão
        var defaults = {
            width           : 200,
            height          : 200,
            aspectRatio     : true,
            imageOriginal   : null,
            imageFinal      : null,
            getWH           : 'getWH.php',
            previewFile     : 'preview.php',
            wImg            : 0,
            hImg            : 0,
            buttons         : { save: "Save", cancel:"Cancel", preview:"Preview", edit:"Edit", original:"Use Original File"},
            onsave          : function(finalFile) {},
            oncancel        : function(finalFile) {}
        };
       
        options = $.extend(defaults, options);
        
        var closeCropperCropper = function() {
            $('#cropper').html('');
            $('#cropper-shadow-pre,#cropper-modal-pre,#cropper-box-options').remove();
            
        }
        
        var save = function() {
            var _url = buildUrlImg();
            _url += '&save=true';
            _url += '&imageFinal='+options.imageFinal;
            $.get(_url,{},function(data) {
                if(data==1){
                    if (typeof options.onsave == 'function') {
                        options.onsave.call('',options.imageFinal);
                        closeCropper();
                    }
                }
            });
        }
        

        //monta html do cropper
        var cropper = function(image) {
            var _html = "";
            //Cropper
            _html += '<div id="cropper" class="cropper">';
            //Top
            _html += '<div class="cropper-top-bar">';
            //Left
            _html += '<div class="cropper-left-top-bar">';
            _html += '<div class="cropper-slider-value">';
            _html += '<input type="text" id="cropper-image-zoom" class="cropper-input" />';
            _html += '</div>';
            _html += '<div class="cropper-slider">';
            _html += '<div id="cropper-slider-image"></div>';
            _html += '</div>';
            _html += '</div>';
            //Right Botões
            _html += '<div class="cropper-right-top-bar">';
            _html += '<a href="javascript:void(0)" id="cropper-btn-cancel" class="cropper-btn">'+options.buttons.cancel+'</a>';
            _html += '<a href="javascript:void(0)" id="cropper-btn-preview" class="cropper-btn cropper-btn-primary">'+options.buttons.preview+'</a>';
            _html += '</div>';
            _html += '</div>';

            //Caixa da Imagem
            _html += '<div class="cropper-box-img">';
            //Área do Crop
            _html += '<div class="cropper-crop"></div>';
            //Imagem a Ser Editada
            _html += '<img class="cropper-box-img-img" src="'+image+'" id="cropper-img" />';
            _html += '</div>';
            _html += '</div>';
            
            
            $(obj).replaceWith(_html);
            
        
            $('#cropper-btn-preview').click(function() {
                preview();
            });
            $('#cropper-btn-cancel').click(function() {
                if (typeof options.quandoSalvar == 'function') {
                    options.quandoCancelar.call('',options.imageOriginal);
                    closeCropper();
                }
            });        
                                
            center(options.wImg,options.hImg);
            build();
        }


        var build = function(){
            //CROP
            $( ".cropper-crop" ).resizable({
                aspectRatio: options.aspectRatio,
                containment:'parent'
            });
            $('.cropper-crop').width(options.width+'px').height(options.height+'px');
            $( ".cropper-crop" ).draggable({
                containment:'parent'
            });
            //SLIDER ZOOM IMAGEM
            $( "#cropper-slider-image" ).slider({
                value:100,
                min: 10,
                max: 200,
                step: 10,
                slide: function( event, ui ) {
                    $( "#cropper-image-zoom" ).val(ui.value);
                    resize(ui.value,options.wImg,options.hImg);
                }
            });
            $( "#cropper-image-zoom" ).val($( "#cropper-slider-image" ).slider( "value" ));
            
        }
        
        
        //Centraliza a imagem
        var center = function(w,h){
            var boxImgW = $('.cropper-box-img').width();
            var boxImgH = $('.cropper-box-img').height();
            var newTop = (boxImgH/2)-(h/2);
            var newLeft = (boxImgW/2)-(w/2);
            $('#cropper-img').css('top',(newTop)+'px');
            $('#cropper-img').css('left',(newLeft)+'px');    
        }
        
        //Altera o tamanho da imagem com o slider de zoom
        var resize = function(percent,w,h){
            var newW = (w*percent)/100;
            var newH = (h*percent)/100;
            $('#cropper-img').width(newW+'px').height(newH+'px');            
            center(newW,newH);
        }


        var openOptions = function(){
            var _html = '<div id="cropper-box-options" class="cropper-options">';
            _html += '<a href="javascript:;" id="cropper-btn-save" class="cropper-btn cropper-btn-primary">'+options.buttons.save+'</a>';
            _html += '<a href="javascript:;" id="cropper-btn-original" class="cropper-btn">'+options.buttons.original+'</a>';
            _html += '<a href="javascript:;" id="cropper-btn-edit" class="cropper-btn">'+options.buttons.edit+'</a>';
            _html += '</div>';
            
            if(!document.getElementById('cropper-box-options')){
                $('body').append(_html);
            }
            $('#cropper-box-options').show();
            
        }

        var preview = function(){
            openOptions();
            var _url = buildUrlImg();
            var _margin = '-'+(Math.round(options.height/2)+10)+'px 0 0 -'+(Math.round((options.width)/2))+'px';
            
            var _img = '<img src="'+_url+'" class="cropper-box-img-img" style="width:'+options.width+'px; height:'+options.height+'px;" />';

            var _html = '<div class="cropper-shadow-pre" id="cropper-shadow-pre"></div><div class="cropper-modal-pre" id="cropper-modal-pre" style="margin:'+_margin+'; width:'+options.height+'px; height:'+options.height+'px;">'+_img+'</div>';

            if(!document.getElementById('cropper-modal-pre')){
                $('body').append(_html);
                $('#cropper-shadow-pre,#cropper-btn-edit').live('click',function() {
                    $('#cropper-shadow-pre,#cropper-modal-pre,#cropper-box-options').hide();
                });
                $('#cropper-btn-save').live('click',function() {
                    save();
                });
                $('#cropper-btn-original').live('click',function() {
                    if (typeof options.oncancel == 'function') {
                        options.oncancel.call('',options.imageOriginal);
                        closeCropper();
                    }
                });
        
            } else {
                $('#cropper-modal-pre').html(_img);
            }
            
            $('#cropper-shadow-pre,#cropper-modal-pre').show();
            
        }
        
        var buildCropData = function() {
            
            var crop = { 
                x:0,
                y:0,
                w:0,
                h:0,
                wFinal:0,
                hFinal:0,
                scale:0
            };
            crop.scale = $('#cropper-image-zoom').val();
            
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

            crop.x=difX;
            crop.y=difY;
            //w e h da imagem final
            crop.wFinal=options.width;
            crop.hFinal=options.height;
            //w e h atual
            crop.w=cropW;
            crop.h=cropH;
            
            return crop;
        }
        
        var buildUrlImg = function() {
            
            var data = buildCropData();
            
            var srcImg = options.previewFile;
            //crop X e Y
            srcImg += '?cropX='+data.x+'&cropY='+data.y;
            //crop WFinal e HFinal
            srcImg += '&cropWFinal='+data.wFinal+'&cropHFinal='+data.hFinal;
            //crop WAtual e HAtual
            srcImg += '&cropW='+data.w+'&cropH='+data.h;
            //scala
            srcImg += '&scale='+data.scale;
            image = options.imageOriginal
            srcImg += '&image='+image;
                
            return srcImg;
        }

        //INICIALIZA
        return this.each(function(){
            if(options.imageOriginal==null) {
                alert('Ops! Where is the image?');
                return false;
            }
            if(options.getWH!=null){
                $.post(options.getWH,{
                    image:options.imageOriginal
                },function(data){
                    if(data!=0){
                        //alert(data);
                        var imageSize = data.split(',');
                    
                        options.wImg = imageSize[0];
                        options.hImg = imageSize[1];
                    
                        cropper(options.imageOriginal);
                    }
                });
            } else cropper(options.imageOriginal);
            
        });
    };
})(jQuery);


