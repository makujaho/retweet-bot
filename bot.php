#!/usr/bin/php
<?php 
/**
 * This bot will retweet the keywords marked in bot config.
 * 
 * You should run it from the command line. If you are on windows, you might
 * need to change some things on your config and this file(esp. the first
 * line, which is the shebang that tells the computer to execute it with PHP)
 * 
 * @package    twerfurt
 * @subpackage twitter-bot
 * @author     Maik Kulbe <info@linux-web-development.de>
 * @license    see LICENCE file
 */

// The tmhOAuth twitter API
require_once 'tmhOAuth/tmhOAuth.php';
require_once 'tmhOAuth/tmhUtilities.php';

require_once 'config.php';
require_once 'util.php';

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

    BotUtil::clog("LENGTH:", BotConfig::LOG_LEVEL_DEBUG);
    BotUtil::clog($length, BotConfig::LOG_LEVEL_DEBUG);

    BotUtil::clog("METRICS:", BotConfig::LOG_LEVEL_DEBUG);
    BotUtil::clog($metrics, BotConfig::LOG_LEVEL_DEBUG);

    if ($length == '0') {
        BotUtil::clog("NOP", BotConfig::LOG_LEVEL_PRODUCTION);
    } else {
    	$data = json_decode($data);

    	BotUtil::clog("RETWEET", BotConfig::LOG_LEVEL_PRODUCTION);
    	BotUtil::clog("DATA:", BotConfig::LOG_LEVEL_DEBUG);
    	BotUtil::clog($data, BotConfig::LOG_LEVEL_DEBUG);

    	// If that tweet was already retweeted we should already have it in our TL
    	if ($data->retweeted === true || strpos($data->text, 'RT @') === 0) {
    		return;
    	}

        $tweet_id = $data->id_str;
        $params   = array(
            // RETWEET PARAMS
        );

        $tmhOAuthRetweet->request("POST", str_replace('{id}', $tweet_id, $retweet_method), $params);
    }

    return file_exists(dirname(__FILE__) . '/STOP');
}

BotUtil::clog("INIT STREAM", BotConfig::LOG_LEVEL_DEBUG);

$params = array(
    "track" => BotConfig::BOT_KEYWORDS
);
$tmhOAuth->streaming_request('POST', $stream_method, $params, 'streaming_callback', false);

// output any response we get back AFTER the Stream has stopped -- or errors
BotUtil::clog($tmhOAuth, BotConfig::LOG_LEVEL_DEBUG);
