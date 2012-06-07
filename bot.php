<?php 

/**
 * 
 * 
 * @package    twerfurt
 * @subpackage twitter-bot
 */

require_once 'config.php';
require '../tmhOAuth.php';
require '../tmhUtilities.php';

$tmhOAuth = new tmhOAuth(array(
		'consumer_key'    => BotConfig::$OAUTH_CONSUMER_KEY,
		'consumer_secret' => BotConfig::$OAUTH_CONSUMER_SECRET,
		'user_token'      => BotConfig::$OAUTH_USER_TOKEN,
		'user_secret'     => BotConfig::$OAUTH_USER_SECRET,
));

$tmhOAuthRetweet = new tmhOAuth(array(
		'consumer_key'    => BotConfig::$OAUTH_CONSUMER_KEY,
		'consumer_secret' => BotConfig::$OAUTH_CONSUMER_SECRET,
		'user_token'      => BotConfig::$OAUTH_USER_TOKEN,
		'user_secret'     => BotConfig::$OAUTH_USER_SECRET,
));

function streaming_callback($data, $length, $metrics) {
	global $tmhOAuthRetweet;
	
	$params = array(
			// RETWEET PARAMS
	);

	$tmhOAuthRetweet->request("POST", $retweetMethod, $params);
	tmhUtilities::pr($tmhOAuth);
	echo $data .PHP_EOL;
	return file_exists(dirname(__FILE__) . '/STOP');
}

$method = "https://stream.twitter.com/1/statuses/filter.json";
$params = array(
		"track" => jaBotConfig::BOT_KEYWORDS
);
$tmhOAuth->streaming_request('POST', $method, $params, 'streaming_callback', false);

// output any response we get back AFTER the Stream has stopped -- or errors
tmhUtilities::pr($tmhOAuth);