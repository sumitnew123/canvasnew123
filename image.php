<?php
$im = new Imagick();
$svg = file_get_contents('media/pdp/projects/20/bodyparts71.svg');
					$im->readImageBlob($svg);
					
					/*png settings*/
					$im->setImageFormat("png24");
					
					$im->writeImage('bodyparts71.png');
					header('Content-type: image/png');
					echo $im;
					$im->clear();
					$im->destroy();
					exit;