                               
<?php
/*---------------------------------------------------------------*/
/*
    Titre : Connexion à l'API de Twitter                                                                                 
                                                                                                                          
    URL   : https://phpsources.net/code_s.php?id=1116
    Date édition     : 30 Sept 2019                                                                                       
    Date mise à jour : 06 Oct 2019                                                                                       
    Rapport de la maj:                                                                                                    
    - fonctionnement du code vérifié                                                                                    
    Date mise à jour : 15 Déc 2020                                                                                       
    Rapport de la maj:                                                                                                    
    - ajout d'une démo                                                                                                   
*/
/*---------------------------------------------------------------*/

ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
    
require "twitteroauth-master/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

define("CONSUMER_KEY","d87uXvxbvDY6vX77Fozo4JHgt");    // Key
define("CONSUMER_SECRET","uQWpBhuFrdVP6ePAHJxMqIOOhvBz5dbkiFipi7jah9ICkP6Bcp"); // key secret
$access_token = "3500224815-vQyXoa7FlahLieexkC66JbIBLtVXrRi4oyy97kY";           // token
$access_token_secret = "41jvx4vJdbHWwv3eUGmsaOSLRQrFOY0xkMX9gGq7jbUFo";    // toekn secret

$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token, 
$access_token_secret);

$TwitterAppName = "FrenchSet"; // Twitter App Name
$NombreDeTweets = 2;  // Le nombre de tweets a remonter

// on va lire quelques tweets
$tweets = $connection->get('statuses/user_timeline', ['screen_name' => 
$TwitterAppName, 'count' => $NombreDeTweets]);

echo json_encode($tweets);

?>


