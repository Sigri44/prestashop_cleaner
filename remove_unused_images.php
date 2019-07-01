<?php
####PUT THIS FILE INTO YOUR MAIN SHOP FOLDER####

// root path of the shop, almost no one needs to change something here.
$shop_root = $_SERVER['DOCUMENT_ROOT']."/"; // need to have slash / at the end
$image_folder = 'img/p/'; // also needs slash at the ennd
$scan_dir = $shop_root.$image_folder;

include_once($shop_root.'config/config.inc.php');
include $shop_root . 'config/settings.inc.php';

// Type : test (echo) OR prod (unlink)
$type = "test";

#---------------------------------------------#
$last_id = (int)Db::getInstance()->getValue('
	SELECT id_image FROM '._DB_PREFIX_.'image ORDER BY id_image DESC
');

$counted_images = Db::getInstance()->executeS('
	SELECT count(*) as qnt FROM '._DB_PREFIX_.'image
');
$counted_images = (int)$counted_images[0]['qnt'];


echo 'There was '.$last_id.' images in database but only '.$counted_images.' is used right now. Lets check how many of them are eating up our storage without no reason.<br>';

$limit = 150; // for testing
//$limit = $last_id; // for production

$removed_images = 0;

for ($i=1; $i <= $limit; $i++) {
	if (!imageExistsInDB($i)){
		$imageDir = str_split($i);
		$imageDir = implode('/', $imageDir);
		$path = $scan_dir.$imageDir;
		deleteImagesFromPath($path);
	}
}

function deleteImagesFromPath($path) {
	global $removed_images;
	$images = glob($path . '/*.{jpg,png,gif,jpeg}', GLOB_BRACE);
	if ($images){
		foreach ($images as $file) {
			if (is_file($file)) {
				if ($type === "prod") {
				    unlink($file);
				} else {
				    echo $file . 'devrait être supprimé !<br/>';
				}
			}
		}
		$removed_images++;
		echo 'Deleted images from folder ' . $path . '/' ."<br/>";
	}
}

function imageExistsInDB($id_image){
	return Db::getInstance()->getValue('
	    SELECT id_image FROM '._DB_PREFIX_.'image WHERE id_image = '.(int)$id_image
	);
}

echo '--------------------------------------<br>';
if  ($removed_images > 0)
	echo 'Hurray! We removed '.$removed_images.' product images!';
else
	echo 'Everything is ok with Your images. I did not removed any of them or I made it before. Good Job Presta!';
