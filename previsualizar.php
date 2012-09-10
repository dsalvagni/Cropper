<?php

require_once 'classes/resize.images.class.php';
if (!isset($_GET['salvar']))
    header('Content-Type: image/jpeg');
$image = new SimpleImage();
$image->load($_GET['imagem']);


if ($_GET['scale'] != 100)
    $image->scale($_GET['scale']);

$cropW = $_GET['cropW'];
$cropH = $_GET['cropH'];
$cropX = $_GET['cropX'];
$cropY = $_GET['cropY'];

$image->crop($cropW, $cropH, $cropX, $cropY);
$image->resize($_GET['cropWFinal'], $_GET['cropHFinal']);

if (isset($_GET['girar']))
    $image->rotate($_GET['girar']);

if (isset($_GET['sepia']))
    $image->sepia();
if (isset($_GET['cinza']))
    $image->escala_de_cinza();
if (isset($_GET['negativa']))
    $image->negativa();
if (isset($_GET['envelhecer']))
    $image->envelhecida();
if (isset($_GET['hipster']))
    $image->hipster();


//filtros 
if(isset($_GET['brilho']))
$image->brilho($_GET['brilho']);
if(isset($_GET['contraste']))
$image->contraste($_GET['contraste']);
//$image->suavizar($_GET['suavizar']);
//colorir
if(isset($_GET['colorir_r']) && isset($_GET['colorir_g']) && isset($_GET['colorir_b']))
$image->colorir($_GET['colorir_r'], $_GET['colorir_g'], $_GET['colorir_b']);


if (isset($_GET['borda']))
    $image->borda ($_GET['borda']);

if (isset($_GET['salvar'])) {
    $imgName = $_GET['imagemFinal'];
    $image->save($imgName);
    echo 1;
}
if (!isset($_GET['salvar']))
    $image->output();
?>
