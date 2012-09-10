<?php
if(isset($_POST['imagem'])){
    $imagem = $_POST['imagem'];
    $tamanho = getimagesize($imagem);
    echo $tamanho[0].','.$tamanho[1];
} else echo 0;
?>
