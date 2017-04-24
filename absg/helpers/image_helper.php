<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! class_exists('ImageFactory'))
{

	abstract class Image 
	{
		protected $cheminComplet; 
		protected $typeMime;
		protected $largeur;
		protected $hauteur;
		
		protected function __construct($file) 
		{
			if (!$tab = getimagesize($file)) 
			{
				return false;
			}
			else 
			{
				$this->cheminComplet = $file;
				$this->largeur = $tab[0];
				$this->hauteur = $tab[1];
			}
		
		}
		
		public function getNomFichierAvecExtension() 
		{
			return basename($this->cheminComplet);
		}
		
		abstract function makeExtension();
		// rien ici, la methode getNomFichierSansExtension() est implementee dans 
		// les classes derivees
		
		
		abstract function getNomFichierSansExtension();
		// rien ici, la methode getNomFichierSansExtension() est implementee dans 
		// les classes derivees
		
		
		public function hauteur() { return $this->hauteur; }
		public function largeur() { return $this->largeur; }


		protected function redimmensionner($ratio, $nouveauNomComplet="", $type)
		{
			// code pour redimensionner une image JPEG
			// $finale est le nom absolu d fichier de l'image reduite
			
			$fonction = "imagecreatefrom" . $type;
			
			// Image source
			$image   = $fonction($this->cheminComplet); 
			$largeur = $this->largeur;
			$hauteur = $this->hauteur;
			
			// Nouvelle image creer (redimensionnee -> cree un nouveau fichier)
			$newLargeur = (int) ($largeur * $ratio);
			$newHauteur = (int) ($hauteur * $ratio);
			$newImage   = imagecreatetruecolor($newLargeur, $newHauteur);
			
			// redimmensionnent
			imagecopyresampled ($newImage, $image, 0, 0, 0, 0, $newLargeur, $newHauteur, $largeur, $hauteur);
			$finale = ($nouveauNomComplet=="" ? $this->cheminComplet : $nouveauNomComplet);
			$fonction = "image" . $type;
			$fonction($newImage,$finale);
			imagedestroy($newImage);
			imagedestroy($image);
			
		}
	}


	class ImageJpeg extends Image
	{
		public function __construct($file)
		{
			$this->typeMime = "image/jpeg";
			parent::__construct($file);
		}
		
		public function makeExtension()
		{
			return ".jpg";
		}
		
		public function getNomFichierSansExtension()
		{
			// �am�iorer pour traiter le cas avec l'extension ".jpeg"
			return basename($this->cheminComplet, ".jpg");
		}
		
		
		public function redimmensionner($ratio, $nouveauNomComplet="") 
		{
			// code pour redimensionner une image JPEG
			//  $finale est le nom absolu d fichier de l'image réuite
			// LARGEUR_ICONE ou LARGEUR_WEB est la largeur voulue pour l'image réuite
			// améliorer si on a une image trés haute et peu large, elle n'est pas redimensionné...
			parent::redimmensionner($ratio, $nouveauNomComplet, "jpeg");
		}
	}





	class ImagePng extends Image
	{
		public function __construct($file)
		{
			$this->typeMime = "image/png";
			parent::__construct($file);
		}
		
		public function makeExtension() 
		{
			return ".png";
		}
		
		public function getNomFichierSansExtension() 
		{
			return basename($this->cheminComplet, ".png");
		}
		
		public function redimmensionner($ratio, $nouveauNomComplet="") 
		{
			// code pour redimensionner une image PNG
			//  $finale est le nom absolu d fichier de l'image r�uite
			// LARGEUR_ICONE ou LARGEUR_WEB est la largeur voulue pour l'image r�uite
			// �am�iorer si on a une image tr� haute et peu large, elle n'est pas redimensionn�...
			parent::redimmensionner($ratio, $nouveauNomComplet, "png");
		}
	}


	class ImageFactory {

	  static function factory($file) {

	    // $type = mime_content_type($file); 
	    // pb avec mime_content_type sur mon pc
	    // j'utilise donc getimagesize
	    $type = getimagesize($file); 

	    switch($type["mime"]) {
	    case "image/jpeg" :
	      return new ImageJpeg($file);
	      break;

	    case "image/png" :
	      return new ImagePng($file);
	      break;

	    default : 
	      return false;
	    }
	  }
	}

}

