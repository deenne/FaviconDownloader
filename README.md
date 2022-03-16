# FaviconDownloader
FaviconDownloader est une class PHP vous permettant de télécharger le favicon d'une page web à partir d'une URL.

----

## Usage:

### Initialisation:
Pour initialiser la class vous devez ajouter un *require* au début de votre fichier et créer une instance.
~~~
/*
* CHEMIN A ADAPTER SELON VOTRE STRUCTURE DE FICHIER
*/
require "./FaviconDownloader.php";

$fd = new FaviconDownloader();
~~~

### Téléchargement d'un favicon:
~~~
/*
* (string) $url
*       Correspond à l'URL dont on souhaite obtenir le favicon
*
* (string) $destination
*       Chemi
*
* (array) [OPTIONNEL] $params
*/
$fav = $fd->getIcon($url, $destination, $params);

if (!empty($fav))
{
    // Favicon téléchargé.
}
~~~
