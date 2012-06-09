#!/usr/bin/php
<?php 

/**
 * 
 * 
 * @package    twerfurt
 * @subpackage twitter-bot
 */

require 'tmhOAuth/tmhOAuth.php';
require 'tmhOAuth/tmhUtilities.php';

require 'config.php';

function clog ($string) {
	if (BotConfig::LOG_ENABLED === false) {
		return;
	}
	error_log(date(('Y-m-d h:i:s')) . " [BOT]: ", 3, BotConfig::LOG_FILE_PATH);
    error_log($string, 3, BotConfig::LOG_FILE_PATH);
}

$tmhOAuth = new tmhOAuth(array(
    'consumer_key'    => BotConfig::OAUTH_CONSUMER_KEY,
    'consumer_secret' => BotConfig::OAUTH_CONSUMER_SECRET,
    'user_token'      => BotConfig::OAUTH_USER_TOKEN,
    'user_secret'     => BotConfig::OAUTH_USER_SECRET,
));

$tmhOAuthRetweet = new tmhOAuth(array(
    'consumer_key'    => BotConfig::OAUTH_CONSUMER_KEY,
    'consumer_secret' => BotConfig::OAUTH_CONSUMER_SECRET,
    'user_token'      => BotConfig::OAUTH_USER_TOKEN,
    'user_secret'     => BotConfig::OAUTH_USER_SECRET,
));

$stream_method  = "https://stream.twitter.com/1/statuses/filter.json";
$retweet_method = "http://api.twitter.com/1/statuses/retweet/{id}.json";

function streaming_callback($data, $length, $metrics) {
    global $tmhOAuthRetweet, $retweet_method;

    clog("LENGTH:");
    clog($length);

    clog("METRICS:");
    clog($metrics);

    if ($length == '0') {
        clog("NOP");
    } else {
    	$data = json_decode($data);
    	clog("DATA:");
    	clog($data);

    	// If that tweet was already retweeted we should already have it in our TL
    	if ($data->retweet_count > 0) {
    		return;
    	}

        $tweet_id = $tweet_id->id_str;

        $params = array(
            // RETWEET PARAMS
        );

        $tmhOAuthRetweet->request("POST", str_replace('{id}', $tweet_id, $retweet_method), $params);
        tmhUtilities::pr($tmhOAuthRetweet);

    }
	
    return file_exists(dirname(__FILE__) . '/STOP');
}

clog("INIT STREAM");

$params = array(
    "track" => BotConfig::BOT_KEYWORDS
);
$tmhOAuth->streaming_request('POST', $stream_method, $params, 'streaming_callback', false);

// output any response we get back AFTER the Stream has stopped -- or errors
tmhUtilities::pr($tmhOAuth);