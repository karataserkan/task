<?php
namespace app\helpers;

/**
* CurrencyHelper includes currency operations.
*/
class CurrencyHelper
{
	/**
	 * Formats currency. Rounds to the smallest currency item
	 *
	 * @param float $value Amount
	 * @param string $currency Currency of amount
	 *
	 * @return string
	 */
	public static function format($value, $currency)
	{
		//smallest currency item differs in currencies.
		switch ($currency) {
			case 'JPY':
				$result = number_format(ceil($value), 0,'.','');
				break;
			
			default:
				$result = number_format(round(ceil($value*100)/100, 2), 2, '.', '');
				break;
		}
		return $result;
	}

	/**
	 * Convert amount in currencies.
	 *
	 * @param string $from
	 * @param string $to
	 * @param float $amount
	 *
	 * @return float
	 */
	public static function convert($from, $to, $amount)
	{
		//get specified currency rates
		$rates = self::getRates();

		if (isset($rates[$from.$to])) {
			return $amount * $rates[$from.$to];
		}elseif (isset($rates[$to.$from])) {
			return $amount / $rates[$to.$from];
		}else{
			//if conversation rates not specified, convert using EUR
			return self::convert('EUR', $to, self::convert($from, 'EUR', $amount));
		}
	}

	/**
	 * Gets specified currency rates.
	 *
	 * @return array
	 */
	public static function getRates()
	{
		$config = $GLOBALS['config'];
		if (!isset($config['rates'])) {
			throw new \OutOfRangeException("Please specify currency conversation rates!", 1);
		}

		return $config['rates'];
	}
}