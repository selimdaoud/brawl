<?php
require __DIR__ . '/vendor/autoload.php';


echo file_get_contents( "1.html" );


/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Sheets API PHP Quickstart');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {

            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}


// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Sheets($client);

// Prints the names and majors of students in a sample spreadsheet:
// https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
$spreadsheetId = '1BpfuDt4Mi0cNGZXO5PrYXK0dxeXXzS2Po2priL6dL10';

$numdrills= "Elliot!B1";

$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$ndrills = $response->getValues();

$rangeFrom= "A3";
$rangeTo= "E".$ndrills;

$range = "Elliot!$rangeFrom:$rangeTo";

$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

if (empty($values)) {
    print "No data found.\n";
} else {
    foreach ($values as $row) {
        // Print columns A and E, which correspond to indices 0 and 4.
$pct=$row[1];
$drill=$row[0];
$desc=$row[2];
$unit=$row[4];
$imageID=rand(1, 5);

echo<<<HTML
<!-------------------->

<div class="_3WKUITiAjaJPn_hnPmN0mw N_Pv8Kwlye2Y1d33BS7k4 _3VmG_IWjij1rn0cKw_KDic" icon="1.png">
  <div class="_2VFrzaoF9fqfXSrIdzwpaa">
    <img src="./index_files/$imageID.png" class="x5sNwD5jA8P2pks09tchU">
  </div>
  <div class="_3lMfMVxY-knKo2dnVHMCWG _21sSMvccqXG6cJU-5FNqzv" style="color: rgb(255, 255, 255); font-size: 14px;">
$drill
  </div>
  <div class="mo25VS9slOfRz6jng3WTf"><!----><div class="_3lMfMVxY-knKo2dnVHMCWG _21sSMvccqXG6cJU-5FNqzv" style="color: rgb(255, 255, 255); font-size: 12px;">
 $pct$unit
  </div>
  </div>
</div>
<!-------------------->

HTML;


    }
}





echo file_get_contents( "2.html" );
