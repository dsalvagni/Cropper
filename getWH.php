<?php
if(isset($_POST['image'])){
    $imagem = $_POST['image'];
    $tamanho = getimagesize($imagem);
    echo $tamanho[0].','.$tamanho[1];
} else echo 0;
?>
