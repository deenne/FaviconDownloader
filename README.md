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
*       IMPORTANT: Cette dernière doit commencer par http:// ou https://
*
* (string) $destination
*       Chemin vers la destination du favicon, incluant le nom de ce dernier sans extension.
*       Cette dernière sera définie automatiquement pour correspondre à l'extension du favicon téléchargé.
*       Ex #1: 
*           Si vous souhaitez que le fichier se nomme "favicon.ext" dans le dossier courant, la variable $destination aura pour valeur "./favicon"
*       Ex #2:
*           Si vous souhaitez que le fichier se nomme "google.ext" dans un sous dossier, la variable $destination aura pour valeur "./sous/dossier/google"
*
* (array) [OPTIONNEL] $params
*       Permet de configuerer différents paramètres de la récupération de l'icon.
*       Paramètres disponibles:
*           - (bool) renewIcon:
*                   Par défaut à 0
*                   0 : Force le remplacement si un fichier existe déjà
*                   1 : Force le renouvellement de l'icon
*
*           - (bool) forceMetaIcon
*                   Par défaut à 0
*                   0 : Priorise l'icon fourni dans la balise <link rel="icon" href="favicon.ico">
*                   1 : Priorise l'image fournie dans la balise <meta property="og:image" content="favicon.ico">
*                       C'est à dire l'image d'apperçu pour les réseaux sociaux qui peut avoir de l'intérêt si elle varie selon la page du site fourni
*
*           - (bool) forceBasePathIcon
*                   Par défaut à 0
*                   0 : Priorise l'icon fourni dans la balise <link rel="icon" href="favicon.ico">
*                       (Ou l'image fournie dans la balise <meta property="og:image" content="favicon.ico"> si le paramètre forceMetaIcon à une valeur de 1)
*                   1 : Priorise la recherche de l'icon à la raçine du site ou dans le dossier courant de l'url fournie.
*                   Ex #1: https://website.com/favicon.ico
*                   Ex #2: https://website.com/sous/dossier/favicon.ico
*
* Retourne le chemin vers le favicone téléchargé avec son extension
*/

$fav = $fd->getIcon($url, $destination, $params);

if (!empty($fav))
{
    // Favicon téléchargé.
    
    echo '<img src="' . $fav . '" alt="favicon">';
}
~~~
