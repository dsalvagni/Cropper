<?php

require_once 'classes/resize.images.class.php';
if (!isset($_GET['salvar']))
    header('Content-Type: image/jpeg');
$image = new SimpleImage();
$image->load($_GET['imagem']);
$image->resizeToWidth(500);
$image->output();
?>
