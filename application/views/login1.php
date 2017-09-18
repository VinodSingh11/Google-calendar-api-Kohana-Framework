
<?php


/*  GOOGLE LOGIN BASIC - Tutorial
 *  file            - index.php
 *  Developer       - Krishna Teja G S
 *  Website         - http://packetcode.com/apps/google-login/
 *  Date            - 28th Aug 2015
 *  license         - GNU General Public License version 2 or later
*/
// REQUIREMENTS - PHP v5.3 or later
// Note: The PHP client library requires that PHP has curl extensions configured. 
/*
 * DEFINITIONS
 *
 * load the autoload file
 * define the constants client id,secret and redirect url
 * start the session
 */
 
require_once __DIR__ . '/gplus-lib/vendor/autoload.php';
const CLIENT_ID ='845385795459-chnponaaie8mlukkc6oq2rrh41o3mtrv.apps.googleusercontent.com';
const CLIENT_SECRET = 'hoe26b9Waf2VXnWPXWgYHNJ2';
const REDIRECT_URI = 'http://localhost:8080/googlecal/Sucess';
const APPLICATION_NAME = "googlecal";
session_start();
/* 
 * INITIALIZATION
 *
 * Create a google client object
 * set the id,secret and redirect uri
 * set the scope variables if required
 * create google plus object
 */
$client = new Google_Client();
$guzzleClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));

  $client->setHttpClient($guzzleClient);
  $client->setApplicationName(APPLICATION_NAME);
$client->setClientId(CLIENT_ID);
$client->setClientSecret(CLIENT_SECRET);
$client->setRedirectUri(REDIRECT_URI);
$client->addScope(Google_Service_Calendar::CALENDAR_READONLY);

$clientp= new Google_Client();
$guzzleClientp = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));

  $clientp->setHttpClient($guzzleClientp);
  $clientp->setApplicationName(APPLICATION_NAME);
$clientp->setClientId(CLIENT_ID);
$clientp->setClientSecret(CLIENT_SECRET);
$clientp->setRedirectUri(REDIRECT_URI);
$clientp->setScopes('email');
$plus = new Google_Service_Plus($clientp);

/*
 * PROCESS
 *
 * A. Pre-check for logout
 * B. Authentication and Access token
 * C. Retrive Data
 */
/* 
 * A. PRE-CHECK FOR LOGOUT
 * 
 * Unset the session variable in order to logout if already logged in    
 */
if (isset($_REQUEST['logout'])) {
   session_unset();
}
/* 
 * B. AUTHORIZATION AND ACCESS TOKEN
 *
 * If the request is a return url from the google server then
 *  1. authenticate code
 *  2. get the access token and store in session
 *  3. redirect to same url to eleminate the url varaibles sent by google
 */
if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}
/* 
 * C. RETRIVE DATA
 * 
 * If access token if available in session 
 * load it to the client object and access the required profile data
 */
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
  $service = new Google_Service_Calendar($client);
 
  $calendarId = 'primary';
$optParams = array(
  'maxResults' => 10,
  'orderBy' => 'startTime',
  'singleEvents' => TRUE,
  'timeMin' => date('c'),
  'alwaysIncludeEmail'=>TRUE,
);		 
		$results = $service->events->listEvents($calendarId, $optParams);
	//	$me = $plus->people->get('me');
if (count($results->getItems()) == 0) {
  print "No upcoming events found.\n";
} else {
  print "Upcoming events:\n";
  $x=1;
  echo "<br>";?><table border="1"><tr><th>NO.</th><th>Email</th><th>Event</th><th>Start Time</th></tr>
  <?php foreach ($results->getItems() as $event) {
    $start = $event->start->dateTime;
	$email = $event->organizer->email;
	$event1=	$event->getSummary();
    if (empty($start)) {
      $start = $event->start->date;
	  $email = $event->organizer->email;
	  $event1=	$event->getSummary();
							  
    }?>
	<tr><td><?php echo $x."-";?></td><td><?php echo $email;?></td><td><?php echo $event1;?></td><td><?php echo $start ;?></td></tr>
	<?php //echo $email;
	//printf("%s", $event->);
    //printf("%s %s (%s)\n",$email, $event->getSummary(), $start);
	echo "<br>";
	$x++;
  }echo "</table>";
}


} else {
  // get the login url   
  $authUrl = $client->createAuthUrl();
}
?>

<!-- HTML CODE with Embeded PHP-->
<div>
    <?php
    /*
     * If login url is there then display login button
     * else print the retieved data
    */
    if (isset($authUrl)) {?>
        <a class='login' href=<?php echo $authUrl?>><img src="<?php echo URL::base(); ?>googlebtn.png" height='50px' /></a>;
	<?php
    } else {
        
        echo "<a class='logout' href='?logout'><button>Logout</button></a>";
    }
    ?>
</div>