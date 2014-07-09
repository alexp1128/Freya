<?php
/**
 * Slim - a micro PHP 5 framework
 *
 * @author      Josh Lockhart <info@slimframework.com>
 * @copyright   2011 Josh Lockhart
 * @link        http://www.slimframework.com
 * @license     http://www.slimframework.com/license
 * @version     2.4.2
 * @package     Freya
 */

namespace Freya\Security;

class Security
{
    // BCrypt Functions
	public static function bcrypt($input, $rounds = 12) {
		$hash = (CRYPT_BLOWFISH == 1)
			? crypt($input, self::getSalt($rounds))
			: crypt($input);
		
		if (strlen($hash) > 13) {
			return $hash;
		}
		
		return false;
	}
	
	public static function verifyHash($input, $existingHash) {
		$hash = crypt($input, $existingHash);

		return $hash === $existingHash;
	}

	// Private Functions
	private static function getSalt($rounds) {
		$salt	= sprintf('$2a$%02d$', $rounds);
		$bytes	= self::getRandomBytes(16);
		$salt	= $salt . self::encodeBytes($bytes);
		
		return $salt;
	}
	
	public static function getRandomBytes($count) {
		$bytes = '';
		
		if (function_exists('openssl_random_pseudo_bytes') && (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) {
			$bytes = openssl_random_pseudo_bytes($count);
		}
		
		if ($bytes === '' && is_readable('/dev/urandom/') && ($hRand = @fopen('/dev/urandom', 'rb')) !== FALSE) {
			$bytes = fread($hRand, $count);
			fclose($hRand);
		}
		
		if (strlen($bytes) < $count) {
			$bytes = '';
			
			if ($randomState === NULL) {
				$randomState = microtime();
				
				if(function_exists('getmypid')) {
					$randomState .= getmypid();
				}
			}
			
			for ($i = 0; $i < $count; $i += 16) {
				$randomState = md5(microtime() . $randomState);
				
				if (PHP_VERSION >= '5') {
					$bytes .= md5($randomState, true);
				} else {
					$bytes .= pack('H*', md5($randomState));
				}
			}
			
			$bytes = substr($bytes, 0, $count);
		}
		
		return $bytes;
	}
	
	public static function encodeBytes($input, $length = 16) {
		$itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		
		$output = '';
		$i = 0;
		
		while(1) {
			$c1 = ord($input[$i++]);
			$output .= $itoa64[$c1 >> 2];
			$c1 = ($c1 & 0x03) << 4;

			if ($i >= $length) {
				break;
			}
		}
		
		return $output;
	}
	
	private static function secureChecksum($str) {
		return md5(sha1($str . Freya::$SETTINGS['SECRETKEY']));
	}
}
*/