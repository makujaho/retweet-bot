<?php 

require_once 'config.php';

/**
 * Bot Util class with helper functions for the bot.
 * 
 * @package    twerfurt
 * @subpackage twitter-bot
 * @author     Maik Kulbe <info@linux-web-development.de>
 * @license    see LICENCE file
 */
class BotUtil {

    /**
     * Logging function
     * 
     * @param unknown_type $string
     * @param unknown_type $log_level
     */
    public static function clog ($string, $log_level = BotConfig::LOG_LEVEL_PRODUCTION, $log_destination = BotConfig::LOG_FILE_PATH ) {
        if (BotConfig::LOG_ENABLED === false || BotConfig::LOG_LEVEL >= $log_level) {
            return;
        }
    
        error_log(date(('Y-m-d h:i:s')) . " [BOT]: ", 3, $log_destination);
        if (is_string($string)) {
            error_log($string . PHP_EOL, 3, $log_destination);
        } else {
            error_log(var_export($string, true) . PHP_EOL, 3, $log_destination);
        }
    }    
}