<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<!-- METAS -->
	<meta name="description" content="" />
	<meta name="keywords" content=""/>
	<meta name="viewport" content="width=device-width; initial-scale=1">
	<!-- LINKS -->
	<link rel="author" href="humans.txt">
	<!-- JS -->
</head>
<body>
	<?php
	require_once 'modImg.php';

	$imagen = new modImg();
	echo "<pre>".print_r($imagen->setImagen("img/code1.png"),true)."</pre>\n";
	$conf = array('calidad' => 90, 'pathRelativo' => 'img/test_1/' , 'size' => array(240,140) );
	echo "<pre>".print_r($imagen->redimensionar($conf),true)."</pre>\n";
	echo "<pre>".print_r($imagen->getRedimensionar(),true)."</pre>\n";


	?>
</body>
</html>