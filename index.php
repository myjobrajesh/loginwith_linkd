<?php
//start session
if(!session_id()){
	session_start();
}

//Include config file && User class
include_once 'configLinkd.php';
include_once 'User.class.php';

$authUrl = $output = '';

//If user already verified 
if(isset($_SESSION['oauth_status']) && $_SESSION['oauth_status'] == 'verified' && !empty($_SESSION['userData'])){
	//Prepare output to show to the user
	$userInfo = $_SESSION['userData'];
	//$output = '<script type="text/javascript" src="//platform-api.sharethis.com/js/sharethis.js#property=5a5dcd6bb4d5b800123095ce&product=inline-share-buttons"></script>';
	$output = '<script type=\'text/javascript\' src=\'//platform-api.sharethis.com/js/sharethis.js#property=5a5dcd6bb4d5b800123095ce&product=inline-share-buttons\' async=\'async\'></script>
        <div class="row">
            <div class="col-sm-9 col-sm-offset-3">
                <div class="col-sm-2">
                    <img src="'.$userInfo['picture'].'" alt="" />
                </div>
                <div class="col-sm-10">
                    <ul class="list-unstyled">
                        <li>
                            <p>'.$userInfo['first_name'].' '.$userInfo['last_name'].'</p>
                        </li>
                        <li>
                            <p>'.$userInfo['email'].'</p>
                        </li>
                        <li>
                            <p>'.$userInfo['locale'].'</p>
                        </li>
                    </ul>
                    <p>
                        <div class="sharethis-inline-share-buttons" style="width: 220px;"></div>
                    </p>
                     <p>
                        
                        <a href="logout.php" class="btn btn-warning">Logout</a>
                        <a href="'.$userInfo['link'].'" target="_blank" class="btn btn-success">View Profile</a>
                       
                        <div class="clearfix"> </div>
                    </p>
                </div>
            </div>
	    </div>';
}elseif((isset($_GET["oauth_init"]) && $_GET["oauth_init"] == 1) || (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']))){
	$client = new oauth_client_class;
	
	$client->client_id = $apiKey;
	$client->client_secret = $apiSecret;
	$client->redirect_uri = $redirectURL;
	$client->scope = $scope;
	$client->debug = false;
	$client->debug_http = true;
	$application_line = __LINE__;
	
	if(strlen($client->client_id) == 0 || strlen($client->client_secret) == 0){
		die('Please go to LinkedIn Apps page https://www.linkedin.com/secure/developer?newapp= , '.
			'create an application, and in the line '.$application_line.
			' set the client_id to Consumer key and client_secret with Consumer secret. '.
			'The Callback URL must be '.$client->redirect_uri.'. Make sure you enable the '.
			'necessary permissions to execute the API calls your application needs.');
	}
	
	//If authentication returns success
	if($success = $client->Initialize()){
		if(($success = $client->Process())){
			if(strlen($client->authorization_error)){
				$client->error = $client->authorization_error;
				$success = false;
			}elseif(strlen($client->access_token)){
				$success = $client->CallAPI('http://api.linkedin.com/v1/people/~:(id,email-address,first-name,last-name,location,picture-url,public-profile-url,formatted-name)',
               'GET',
				array('format'=>'json'),
				array('FailOnAccessError'=>true), $userInfo);
			}
		}
		$success = $client->Finalize($success);
	}
	
	if($client->exit) exit;
	
	if($success){
		//Initialize User class
		$user = new User();
		//Insert or update user data to the database
		$fname = $userInfo->firstName;
		$lname = $userInfo->lastName;
		$inUserData = array(
			'oauth_provider'=> 'linkedin',
			'oauth_uid'     => $userInfo->id,
			'first_name'    => $fname,
			'last_name'     => $lname,
			'email'         => $userInfo->emailAddress,
			'gender'        => '',
			'locale'        => $userInfo->location->name,
			'picture'       => $userInfo->pictureUrl,
			'link'          => $userInfo->publicProfileUrl,
			'username'		=> ''
		);
		
		$userData = $user->checkUser($inUserData);
		
		//Storing user data into session
		$_SESSION['userData'] = $userData;
		$_SESSION['oauth_status'] = 'verified';
		
		//Redirect the user back to the same page
		header('Location: ./');
	}else{
		 $output = '<h3 style="color:red">Error connecting to LinkedIn! try again later!</h3>';
	}
}elseif(isset($_GET["oauth_problem"]) && $_GET["oauth_problem"] <> ""){
	$output = '<h3 style="color:red">'.$_GET["oauth_problem"].'</h3>';
}else{
	$authUrl = '?oauth_init=1';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login with LinkedIn using PHP</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

</head>
<body>
<div class="container">
<h1 class="text-center">Welcome to test linked </h1>
<?php echo $output; ?>
<?php
if(!empty($authUrl)){
	echo '<div class="text-center"><a href="'.$authUrl.'"><button class="btn btn-primary">Sign In with Linkedin</button></a></div>';
}
?>
</div>
</body>
</html>