<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<?php

//Liste des extensions des images à lister
define('GAL_EXTENSIONS', '{jpg,jpeg,gif,png,JPG}');

//Définition des miniatures
define('GAL_MINI_LARGEUR', 64);
define('GAL_MINI_QUALITY', 90);
define('GAL_SUFIX_MINI', '_mini');

//Non de la variable dans le GET
define('GAL_IDENTIFIANT', 'dossier');

//Dossier de la galerie d'image
define('GAL_DOSSIER', './galerie/');

function CreeMiniature($Lien_Image, $Lien_Miniature, $Largeur)
	{
	 $Ext_Image = strrchr($Lien_Image, ".");
	 
	 list($width, $height) = getimagesize($Lien_Image);
	 $percent = ($width / $Largeur);
	 $X_New	  = $Largeur;
	 $Y_New	  = $height / $percent;
	 $Mini	  = imagecreatetruecolor($X_New, $Y_New);
	 switch(strtolower($Ext_Image))
	 	{
		case '.jpeg':
		case '.jpg' :
			$Img = imagecreatefromjpeg($Lien_Image);
			imagecopyresized($Mini, $Img, 0,0,0,0,$X_New,$Y_New,$width,$height);
			imagejpeg($Mini, $Lien_Miniature, GAL_MINI_QUALITY);
		break;
		case '.gif':
			$Img = imagecreatefromgif($Lien_Image);
			imagecopyresized($Mini, $Img, 0,0,0,0,$X_New,$Y_New,$width,$height);
	 		imagegif($Mini, $Lien_Miniature, GAL_MINI_QUALITY);
		break;
		case '.png':
			$Img = imagecreatefrompng($Lien_Image);
			imagecopyresized($Mini, $Img, 0,0,0,0,$X_New,$Y_New,$width,$height);
	 		imagepng($Mini, $Lien_Miniature, GAL_MINI_QUALITY);
		break;
		default:
			imagedestroy($Mini);
			return FALSE;
		}
	 imagedestroy($Mini);
	 @imagedestroy($Img);
	 return TRUE;
	}
	
function AjouterImage($Lien_Image)
{
	$Ext_Image = strrchr($Lien_Image, '.');
	//Si c'est une miniature, on ne l'affiche pas
	if(strpos($Lien_Image, GAL_SUFIX_MINI . $Ext_Image) !== FALSE)return FALSE;
	
	$Nom_Image = substr($Lien_Image, strrpos($Lien_Image, '/') + 1, - strlen($Ext_Image));
	
	$Dossier_Image = substr($Lien_Image, 0, strrpos($Lien_Image, '/')) . '/';
	
	
	if(!file_exists($Dossier_Image . $Nom_Image . GAL_SUFIX_MINI . $Ext_Image))//Si la miniture n'es pas trouver
		//On la cree
		 CreeMiniature($Lien_Image, $Dossier_Image . $Nom_Image . GAL_SUFIX_MINI . $Ext_Image, GAL_MINI_LARGEUR);
		 
	echo '<li>'."\n".
		 '<a href="'.$Lien_Image.'" title="'.htmlentities($Nom_Image).'"><img src="'.$Dossier_Image . $Nom_Image . GAL_SUFIX_MINI . $Ext_Image .'" /></a>'."\n".
		 '</li>'."\n";

}

// Création de la liste des dossiers
$Liste_Dossiers = glob(GAL_DOSSIER.'*', GLOB_ONLYDIR);



$Liste_Dossiers = array();
if ($Dossier = opendir(GAL_DOSSIER)) {
	while (($Fichier = readdir($Dossier)) !== false)
		if($Fichier !== '.' && $Fichier !== '..' && is_dir(GAL_DOSSIER . $Fichier))$Liste_Dossiers[] = GAL_DOSSIER . $Fichier;
    closedir($Dossier);
}
*/

$SELECT_DOSSIER = NULL;
//Si un dossier a ete selectionné et qu'il est disponible
if(@isset($_GET[GAL_IDENTIFIANT]))
if(array_key_exists((int)$_GET[GAL_IDENTIFIANT], $Liste_Dossiers) && $_GET[GAL_IDENTIFIANT] !== NULL)
	$SELECT_DOSSIER = $Liste_Dossiers[(int)$_GET[GAL_IDENTIFIANT]].'/';//On definit le dossier selectioner
?>
<style type="text/css">
div#galerie a
{
	text-align: center;
	font-family: Georgia, serif;
	font-size: 1em;
	text-decoration: none;
	color: #000000;
}
div#galerie
{
	width: 350px;
	border: 1px solid #dcb;
	padding: 15px;
	margin: 15px 30px;
	text-align: center;
	font: 0.9em Georgia, serif;
	background-color: #FFFFFF;
}

ul#galerie_mini
{
	margin: 0;
	padding: 0;
	position: static;
	list-style-type: none;
	background-position: center center;
}

ul#galerie_mini li
{
	float: left;
	background-position: center;
}

ul#galerie_mini li a img
{
	margin: 2px 1px ;
	border: 1px solid #dcb ;
}

dl#photo
{
	clear: Aucune;
	margin-top: 0;
	margin-right: auto;
	margin-bottom: 0;
	margin-left: auto;
}

dl#photo dt
{
	font: italic 2.5em/1.5em Georgia, serif ;
	color: #dcb ;
}

dl#photo dd
{
	margin: 0 ;
}

dl#photo img
{
	border: 1px solid #dcb ;
}
</style>
<script language="javascript">
<!--
function displayPics()
{
	if(!document.getElementById('galerie_mini'))return false;
	// Si on es pas dans une categorie et que l'element galerie_mini n'existe pas
	// On retourne FALSE (Erreur signalé par niceman23)
	var photos = document.getElementById('galerie_mini') ;
	// On récupère l'élément ayant pour id galerie_mini
	var liens = photos.getElementsByTagName('a') ;
	// On récupère dans une variable tous les liens contenu dans galerie_mini
	var big_photo = document.getElementById('big_pict') ;
	// Ici c'est l'élément ayant pour id big_pict qui est récupéré, c'est notre photo en taille normale

	var titre_photo = document.getElementById('photo').getElementsByTagName('dt')[0] ;
	// Et enfin le titre de la photo de taille normale

	// Une boucle parcourant l'ensemble des liens contenu dans galerie_mini
	for (var i = 0 ; i < liens.length ; ++i) {
		//Affiche la première image
		if( i == 0 )
			{
		    big_photo.src = liens[i].href;
			big_photo.alt = liens[i].title;
			titre_photo.firstChild.nodeValue = liens[i].title;
			}
		// Au clique sur ces liens 	
		liens[i].onclick = function() {
			big_photo.src = this.href; // On change l'attribut src de l'image en le remplaçant par la valeur du lien
			big_photo.alt = this.title; // On change son titre
			titre_photo.firstChild.nodeValue = this.title; // On change le texte de titre de la photo
			return false; // Et pour finir on inhibe l'action réelle du lien
		};
	}
 return true;
}
// Il ne reste plus qu'à appeler notre fonction au chargement de la page
window.onload = displayPics;
-->
</script>
</head>
<body>
<div id="galerie">Galeries :<br />
  <?php
$Pos = strpos($_SERVER['REQUEST_URI'], '?');
if($Pos === FALSE)
	$Pos = strlen($_SERVER['REQUEST_URI']); 
/*
//PHP 4
$Query = @explode('?', $_SERVER['REQUEST_URI']);
@parse_str($Query[1], $Query);
//
*/

//PHP 5
parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $Query);
//

//Affichage de la liste des dossier
foreach ($Liste_Dossiers as $ID => $Dossier)
	{
		$Query[GAL_IDENTIFIANT] = $ID;
		
		$NombreImages = count(glob($Dossier . '/*.' . GAL_EXTENSIONS, GLOB_BRACE)) - count(glob($Dossier."/*".GAL_SUFIX_MINI.'.'.GAL_EXTENSIONS, GLOB_BRACE));
		
		// PHP 4
		/*
		$URL_Reconstruite = "";
		reset($Query);
        foreach($Query as $cle => $valeur){
           $URL_Reconstruite .= $cle.'='.$valeur.'&';
        }
		$URL_Reconstruite = substr($URL_Reconstruite, 0, -1);
		
		echo '<a href="'.substr($_SERVER['REQUEST_URI'], 0, $Pos).'?'.$URL_Reconstruite.'">'.htmlentities(substr(strrchr($Dossier, '/'), 1)).' ('.$NombreImages.')</a><br />'."\n";
		*/
		
		// PHP 5
		echo '<a href="'.substr($_SERVER['REQUEST_URI'], 0, $Pos).'?'.http_build_query($Query).'">'.htmlentities(substr(strrchr($Dossier, '/'), 1)).' ('.$NombreImages.')</a><br />'."\n";
		//
	}
?>
</div>
<div id="galerie">
  <dl id="photo">
    <dt>Galerie Photo</dt>
    <table border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td><dd><img src="<?=GAL_DOSSIER?>defaut.jpg" alt="Galerie Photos" name="big_pict" width="350" id="big_pict" /></dd></td>
      </tr>
    </table>
  </dl>
</div>
<?php
if($SELECT_DOSSIER !== NULL)
{
?>
<div id="galerie">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr align="center" valign="middle">
      <td><ul id="galerie_mini">
          <?php
		//Affichage des photos
		$Liste_Images = glob($SELECT_DOSSIER . '*.' . GAL_EXTENSIONS, GLOB_BRACE );
		foreach ($Liste_Images as $Lien_Image)
		 AjouterImage($Lien_Image);
		?>
        </ul></td>
    </tr>
  </table>
</div>
<?php
}
?>
</body>
</html>
