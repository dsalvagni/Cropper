Cropper
===
Just a jQuery plugin to crop your images.

How To
===

Basic

    <div id="cropper"></div>
    <div id="finalImageSample"></div>
    $("#cropper").cropper({
        imageOriginal : imagem,
        onsave   : function(finalFile) {
            $('#finalImageSample').html('<img src='+finalFile+' />');
        }
    });  

Defaults

    var defaults = {
        width           : 200,
        height          : 200,
        aspectRatio     : false,
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
