<?php

require_once 'classes/resize.images.class.php';
if (!isset($_GET['save']))
    header('Content-Type: image/jpeg');
$image = new SimpleImage();
$image->load($_GET['image']);


if ($_GET['scale'] != 100)
    $image->scale($_GET['scale']);

$cropW = $_GET['cropW'];
$cropH = $_GET['cropH'];
$cropX = $_GET['cropX'];
$cropY = $_GET['cropY'];

$image->crop($cropW, $cropH, $cropX, $cropY);
$image->resize($_GET['cropWFinal'], $_GET['cropHFinal']);

if (isset($_GET['save'])) {
    $imgName = $_GET['imageFinal'];
    $image->save($imgName);
    echo 1;
}
if (!isset($_GET['save']))
    $image->output();
?>
