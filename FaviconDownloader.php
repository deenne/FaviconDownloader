<?php // CLASS
    class FaviconDownloader
    {
        protected $debug = [
            "data" => "",
            "style" => ""
        ];

        public function getIcon($originURL, $destIcon, $params=[])
        {
            $originURLDebug = "<a href='$originURL' style='color: #fff;'>$originURL</a>";

            $isOk = false;
            $debugAll = 0;
            $forceMetaIcon = 0;
            $forceBasePathIcon = 0;
            $debugStyle = "";
            $debugInfo = "";
            
            if (isset($params['debugAll'])) {
                $debugAll = $params['debugAll'];
            }
            if (isset($params['forceMetaIcon'])) {
                $forceMetaIcon = $params['forceMetaIcon'];
            }
            if (isset($params['forceBasePathIcon'])) {
                $forceBasePathIcon = $params['forceBasePathIcon'];
            }

            if ((
                empty($this->checkIcon($destIcon))
                && strpos($originURL, "http://localhost/") === false
                && strpos($originURL, "http://127.0.0.1/") === false
                && strpos($originURL, "https://localhost/") === false
                && strpos($originURL, "https://127.0.0.1/") === false
                ) || $debugAll
            )
            {
                // Essaye de récupérer l'icon en lisant le html
                if ($isOk != true || $debugAll)
                {
                    // Obtention URL du Favoris
                    $url = $originURL;

                    // Vérifie si lien local
                    if (
                        strpos($url, "http://localhost/") === false
                        && strpos($url, "http://127.0.0.1/") === false
                        && strpos($url, "https://localhost/") === false
                        && strpos($url, "https://127.0.0.1/") === false
                    )
                    {
                        // Nettoyage URL du Favoris
                        $cleanURL = $this->cleanURL($url);

                        // Obtention URL destination du favoris
                        $tmp = $this->followURLPage($cleanURL);

                        // Véfification URL destination du favoris
                        if ($tmp != false && $tmp != "" && !empty(fopen($tmp, 'r')))
                        {
                            $url = $tmp;

                            $fileContent = file_get_contents($url);

                            $matches = array();

                            preg_match('/<link[^>]*rel=("|\')[^>\'"]*icon("|\')[^>]*href=("|\')([^>"\']*)("|\')/i', $fileContent, $matches);
                            if (count($matches) > 4)
                            { // <link rel="icon" type="image/png" href="http://example.com/icon.png" />
                                $iconURL = trim($matches[4]);
                            }

                            preg_match('/<link[^>]*href=("|\')([^>]*)("|\')[^>]*rel=("|\')[^>]*icon("|\')/i', $fileContent, $matches);
                            if (count($matches) > 2)
                            { // <link type="image/png" href="http://example.com/icon.png" rel="icon" />
                                $iconURL = trim($matches[2]);
                            }

                            preg_match('/<meta[^>]*property=("|\')[^>]*image("|\')[^>]content=("|\')([^>"\']*)("|\')[^>]/i', $fileContent, $matches);
                            if (count($matches) > 4 && (empty($iconURL) || $forceMetaIcon))
                            { // <meta property="og:image" content="http://example.com/icon.png" /> 

                                if (!empty($iconURL)) 
                                {
                                    $addDebug = "\n\nIMAGE ALTERNATIVE: ".$iconURL;
                                }

                                $tmp = trim($matches[4]);
                                if (!empty($tmp))
                                {
                                    $iconURL = $tmp;
                                }
                            }

                            preg_match('/<meta[^>]content=("|\')([^>"\']*)("|\')[^>]*property=("|\')[^>]*image("|\')[^>]/i', $fileContent, $matches);
                            if (count($matches) > 2 && (empty($iconURL) || $forceMetaIcon))
                            { // <meta content="http://example.com/icon.png" property="og:image" /> 

                                if (!empty($iconURL)) 
                                {
                                    $addDebug = "\n\nIMAGE ALTERNATIVE: ".$iconURL;
                                }

                                $tmp = trim($matches[2]);
                                if (!empty($tmp))
                                {
                                    $iconURL = $tmp;
                                }
                            }


                            if (!empty($iconURL))
                            { // Si un lien est extrait de l'url

                                $fullPath = $this->mergeURL($url, $iconURL);

                                if (!empty($fullPath))
                                { // Si un chemin corrigé existe dl du favicon

                                    file_put_contents($destIcon, fopen($fullPath, 'r'));

                                    if (file_exists($destIcon) && exif_imagetype($destIcon))
                                    {
                                        $isOk = true;

                                        $debugStyle = "VALID";
                                        $debugInfo = "URL : ".$originURLDebug."\nPAGE URL: ".$url."\nICON URL DISTANT: ".$fullPath."\nICON VALIDE";
                                    }
                                    else
                                    { // ICON NON VALIDE
                                        $debugStyle = "ALERT";
                                        $debugInfo = "URL: ".$originURLDebug."\nPAGE URL: ".$url."\nICON URL: ".$iconURL."\nICON URL ABSOLU: ".$fullPath."\nICON NON VALIDE";
                                    }
                                }
                                else
                                { // URL NON MERGEE
                                    $debugStyle = "INFO";
                                    $debugInfo = "URL: ".$originURLDebug."\nPAGE URL: ".$url."\nICON URL: ".$iconURL."\nURL NON MERGEE";
                                }
                            }
                            else
                            { // ICON NON EXTRAIT
                                $debugStyle = "INFO";
                                $debugInfo = "URL: ".$originURLDebug."\nPAGE URL: ".$url."\nICON NON EXTRAIT";
                            }
                        }
                        else
                        { //URL NON SUIVIE
                            $debugStyle = "INFO";
                            $debugInfo = "URL: ".$originURLDebug."\nCLEAN URL:".$cleanURL."\nURL NON SUIVIE";
                        }
                    }
                    else
                    { // LIEN LOCAL
                        $debugStyle = 'INFO';
                        $debugInfo = "URL: ".$originURLDebug."\nLIEN LOCAL";
                    }
                }

                // Essaye de récupérer l'icon à la racine, dans le dossier courant, etc...
                if ($isOk != true || $forceBasePathIcon)
                {
                    $isOk != false;

                    $faviconsPriority = [
                        "./favicon-64x64.png",
                        "./favicon-64x64.ico",
                        "../favicon-64x64.png",
                        "../favicon-64x64.ico",
                        "/favicon-64x64.png",
                        "/favicon-64x64.ico",

                        "./favicon-32x32.png",
                        "./favicon-32x32.ico",
                        "../favicon-32x32.png",
                        "../favicon-32x32.ico",
                        "/favicon-32x32.png",
                        "/favicon-32x32.ico",

                        "./favicon.png",
                        "./favicon.ico",
                        "../favicon.png",
                        "../favicon.ico",
                        "/favicon.png",
                        "/favicon.ico",

                        "./favicon-16x16.png",
                        "./favicon-16x16.ico",
                        "../favicon-16x16.png",
                        "../favicon-16x16.ico",
                        "/favicon-16x16.png",
                        "/favicon-16x16.ico",
                    ];

                    foreach ($faviconsPriority as $favName)
                    {
                        if ($isOk != true)
                        {
                            $url = $this->followURLPage($originURL);

                            if (!empty($url)) 
                            {
                                $tmp = $this->mergeURL($url, $favName);
                            }
                            else 
                            {
                                $tmp = $this->mergeURL($originURL, $favName);
                            }

                            if ($tmp != false && $tmp != "" && !empty(fopen($tmp, 'r')))
                            {
                                file_put_contents($destIcon, fopen($tmp, 'r'));

                                if (file_exists($destIcon) && exif_imagetype($destIcon))
                                {
                                    $debugStyle = "VALID";
                                    $debugInfo = "URL : ".$originURLDebug."\nPAGE URL: ".$url."\nICON URL DISTANT: ".$tmp."\nICON VALIDE\n\nMETHODE: ".$favName;

                                    $isOk = true;
                                }
                            }
                        }
                    }
                }

                // Enregistrement des infos de débug
                if (!empty($addDebug)) 
                {
                    $this->debug = [
                        "data" => $debugInfo.$addDebug,
                        "style" => $debugStyle
                    ];
                }
                else 
                {
                    $this->debug = [
                        "data" => $debugInfo,
                        "style" => $debugStyle
                    ];
                }

                // Return de l'icon || false
                if (file_exists($destIcon) && exif_imagetype($destIcon))
                {
                    $newIcon = $this->cropIcon($destIcon);

                    unlink($destIcon);

                    return $newIcon;
                }
                else
                {
                    unlink($destIcon);

                    return false;
                }
            }
            else
            {
                // Enregistrement des infos de débug
                if (!$this->checkIcon($destIcon)) 
                {
                    $this->debug = [
                        "data" => "URL: ".$originURLDebug."\nLIEN LOCAL",
                        "style" => "INFO"
                    ];
                }
                else
                {
                    $this->debug = [
                        "data" => "URL: ".$originURLDebug."\nICON DEJA CHARGE",
                        "style" => "VALID"
                    ];
                }

                // Return de l'icon || false
                return $this->checkIcon($destIcon);
            }
        }

        /* Retour : URL ou null */
        public function followURLPage($url)
        {
            $fp = fopen($url, 'r');

            $meta_data = stream_get_meta_data($fp);
            foreach($meta_data['wrapper_data'] as $response)
            {
                if (substr(strtolower($response), 0, 10) == 'location: ')
                { // modification de $url avec la nouvelle URL

                    $urls[] = substr($response, 10);
                }
            }

            if (!empty($urls))
            {
                for ($i=0; $i < count($urls); $i++)
                {
                    if (stripos($urls[$i], "://") !== false)
                    {
                        $url = $urls[$i];
                    }
                    else
                    {
                        $url = $this->mergeURL($url, $urls[$i]);
                    }
                }
            }

            $fileContent = file_get_contents($url);

            if (!empty($fileContent))
            {
                // Vérification meta de redirection
                preg_match('/<meta.*?http-equiv=("|\').*?refresh("|\').*?content=("|\')(.*?)("|\')/i', $fileContent, $matches);
                if (count($matches) > 4)
                {
                    $u = explode("url=", $matches[4]);

                    $url = $this->mergeURL($url, $u[1]);

                    // $url = $this->followURLPage($u);
                }
            }

            return $url;
        }

        /* Retour : URL ou null */
        public function mergeURL($url_1, $url_2)
        {
            $fullPath = null;

            if ($url_2[0] == '.' && $url_2[1] == '.' && $url_2[2] == '/')
            { // Chemin commencant par "../"

                $url_2 = str_replace("../", "", $url_2);

                if (strpos($url_1, "https://") !== false)
                {
                    $url_1 = str_replace("https://", "", $url_1);
                    $newUrl = "https://";
                }
                elseif (strpos($url_1, "http://") !== false)
                {
                    $url_1 = str_replace("http://", "", $url_1);
                    $newUrl = "http://";
                }
    
                $tableURL = explode("/", $url_1);

                $decal = 1;
                if (
                    strpos(strtolower($tableURL[count($tableURL) -1]), ".html") !== false 
                    || strpos(strtolower($tableURL[count($tableURL) -1]), ".php") !== false
                ) 
                {
                    $decal = 2;
                }
                elseif (
                    strpos(strtolower($tableURL[count($tableURL) -2]), ".html") !== false 
                    || strpos(strtolower($tableURL[count($tableURL) -2]), ".php") !== false
                ) 
                {
                    $decal = 3;
                }

                for ($i=0; $i < count($tableURL) - $decal; $i++) { 
                    $newUrl .= $tableURL[$i]."/";
                }

                $url_1 = $newUrl;

                $fullPath = $this->mergeURL($url_1, $url_2);
            }
            elseif ($url_2[0] == '.' && $url_2[1] == '/')
            // MODIF: SUPPRESSION DES INDEX
            { // Chemin commencant par "./"

                $url_2 = str_replace("./", "", $url_2);

                if (substr($url_1, -1) != "/") 
                {
                    $url_1 .= "/";
                }

                $fullPath = $url_1 . $url_2;
            }
            elseif ($url_2[0] == '/' && $url_2[1] == '/')
            { // Chemin commencant par "//"

                $fullPath = "https:" . $url_2;
            }
            elseif ($url_2[0] == '/')
            { // Chemin commencant par "/"

                if (strpos($url_1, "https://") !== false)
                {
                    $url_1 = str_replace("https://", "", $url_1);
                    $add = "https://";
                }
                elseif (strpos($url_1, "http://") !== false)
                {
                    $url_1 = str_replace("http://", "", $url_1);
                    $add = "http://";
                }

                $newUrl = explode("/", $url_1);
                $url_1 = $add . $newUrl[0];

                $fullPath = $add . $newUrl[0] . $url_2;
            }
            elseif ( ($url_2[0] == 'h' && $url_2[1] == 't' && $url_2[2] == 't' && $url_2[3] == 'p' && $url_2[4] == 's' && $url_2[5] == ':' && $url_2[6] == '/' && $url_2[7] == '/') || ($url_2[0] == 'h' && $url_2[1] == 't' && $url_2[2] == 't' && $url_2[3] == 'p' && $url_2[4] == ':' && $url_2[5] == '/' && $url_2[6] == '/') )
            { // Chemin Absolu

                $fullPath = $url_2;
            }
            else
            {
                if (substr($url_1, -1) != "/") 
                {
                    $url_1 .= "/";
                }

                $fullPath = $url_1 . $url_2;
            }

            return $fullPath;
        }

        public function cleanURL($url)
        {
            if (strpos($url, "https://") !== false)
            {
                $url = str_replace("https://", "", $url);
                $add = "https://";
            }
            elseif (strpos($url, "http://") !== false)
            {
                $url = str_replace("http://", "", $url);
                $add = "http://";
            }


            $param = explode('?', $url);
            $u = explode('/', $param[0]);

            for ($i=0; $i < count($u); $i++)
            {
                $u[$i] = urlencode($u[$i]);
            }
            $url = implode('/', $u);

            if (!empty($param[1])) { $url .= '?'.$param[1]; }


            $url = $add.$url;

            $url = str_replace("%3A", ":", $url);

            return $url;
        }

        public function cropIcon($iconPath)
        {
            try 
            {
                $mime = image_type_to_mime_type(exif_imagetype($iconPath));

                if ($mime == "image/png") 
                {
                    $photo = imagecreatefrompng($iconPath);
                }
                elseif ($mime == "image/jpeg")
                {
                    $photo = imagecreatefromjpeg($iconPath);
                }
                elseif ($mime == "image/vnd.microsoft.icon") 
                {
                    rename($iconPath, $iconPath.".ico");
                    
                    return $iconPath.".ico";
                }

                if (!empty($photo)) 
                {
                    $ratio = imagesy($photo) / imagesx($photo);
                    if ($ratio > 1) 
                    {
                        $largeur = imagesx($photo);
                        $hauteur = $largeur;
                        
                        $x = 0;
                        $y = (imagesy($photo) - $hauteur) /2;
    
                        $photo = imagecrop(
                            $photo, 
                            [
                                'x' => $x, 
                                'y' => $y, 
                                'width' => $largeur, 
                                'height' => $hauteur
                            ]
                        );
                    } 
                    elseif ($ratio < 1) 
                    {
                        $hauteur = imagesy($photo);
                        $largeur = $hauteur;
                        
                        $x = (imagesx($photo) - $largeur) /2;
                        $y = 0;
    
                        $photo = imagecrop(
                            $photo, 
                            [
                                'x' => $x, 
                                'y' => $y, 
                                'width' => $largeur, 
                                'height' => $hauteur
                            ]
                        );
                    }
                    elseif ($mime == "image/png") 
                    {
                        rename($iconPath, $iconPath.".png");
                        
                        return $iconPath.".png";
                    }
        
                    if (imagesx($photo) > 512) {
                        $photo = imagescale($photo, 512);
                    }
        
                    if ($mime == "image/png") 
                    {
                        imagepng($photo, $iconPath.".png", 0);
                    
                        return $iconPath.".png";
                    }
                    elseif ($mime == "image/jpeg")
                    {
                        imagejpeg($photo, $iconPath.".jpeg", 100);
                    
                        return $iconPath.".jpeg";
                    }
                }
            } 
            catch (\Throwable $th) 
            {
                // dump($th);
            }
        }

        public function checkIcon($iconPath)
        {
            if (file_exists($iconPath.".png") && exif_imagetype($iconPath.".png")) 
            {
                return $iconPath.".png";
            }
            if (file_exists($iconPath.".jpeg") && exif_imagetype($iconPath.".jpeg")) 
            {
                return $iconPath.".jpeg";
            }
            if (file_exists($iconPath.".ico") && exif_imagetype($iconPath.".ico")) 
            {
                return $iconPath.".ico";
            }

            return false;
        }

        public function getDebug()
        {
            return $this->debug;
        }
    }
?>