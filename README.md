# FaviconDownloader
FaviconDownloader est une class PHP vous permettant de télécharger le favicon d'une page web.

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
* $url
* $dest
* $params
*/
$fav = $fd->getIcon($url, $dest, $params);

if (!empty($fav))
{
    // Favicon téléchargé.
}
~~~
