<?php

namespace Freya\Security;

class Util
{
    protected static function stripSlashesIfMagicQuotes($rawData)
    {
        return (get_magic_quotes_gpc()) ? self::stripSlashes($rawData) : $rawData;
    }

    protected static function stripSlashes($rawData)
    {
        return is_array($rawData) ? array_map(array('self', 'stripSlashes'), $rawData) : stripslashes($rawData);
    }
    
    public static function sanitizeHtml($input)
    {
		return htmlspecialchars($input, ENT_QUOTES);
	}
}
