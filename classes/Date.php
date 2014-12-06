<?php

/********************************************************************
 *
 *  File's name: Date.php
 *  Script's author: Filip Markiewicz (www.filipmarkiewicz.pl)
 *
 *  Created: 22.11.2012r.
 *  Last modificated: 24.11.2014r.
 *
 ********************************************************************/

class Date {
	static public $polish_months = array(1 => 'stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia');
	
	protected $day, $month, $year, $hour, $minute, $second;
	
	/**
	 *
	 * @param $day Integer
	 *        $month Integer from 1 to 12
	 *        $year Integer
	 *        $hour Integer
	 *        $minute Integer
	 *        $second Integer
	 *
	**/
	public function __construct($day, $month, $year, $hour = null, $minute = null, $second = null) {
		if($month > 12 || $month < 0) {
			throw new Exception('Miesiąc musi być mniejszy/równy 12 i wiekszy od 0.');	
		}
		
		$this->day = $day;
		$this->month = $month;
		$this->year = $year;
		$this->hour = $hour;
		$this->minute = $minute;
		$this->second = $second;
	}
	
	public function __toString() {
		return $this->getDate();	
	}	
	
	/**
	 *
	 * @param $time DATETIME or quantity of seconds since 01.01.1970
	 *
	 * Returns Date object created using time given in the argument or false, when $time is incorrect type.
	 *
	**/
	static public function create($time) {
		if(preg_match('/^[0-9]$/', $time)) {
 			$time = date('Y-m-d H:i:s', $time);
 		}
 		else if(preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/", $time) || preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $time)) {
			$exploded_date = explode(' ', $time);

			$date = explode('-', $exploded_date[0]);

			$day = (int) $date[2];	
			$month = (int) $date[1];
			$year = (int) $date[0];

			if(isset($exploded_date[1])) {
				$hours = explode(':', $exploded_date[1]);
				$hour = (int) $hours[0];
				$minute = (int) $hours[1];
				$second = (int) $hours[2];
 			}
 			else {
 				$hour = null;
 				$minute = null;
 				$second = null;	
 			}
 			
			return new self($day, $month, $year, $hour, $minute, $second); 			
 		}
 		else {
 			return false;	
 		}	
	}
	
	/**
	 *
	 * Returns a day with preceding zero.
	 *
	**/
	public function getDay() {
		return AddZeros($this->day);	
	}
	
	/**
	 *
	 * Returns a number of month (1-12).
	 *
	**/
	public function getMonth() {
		return $this->month;	
	}
	
	/**
	 *
	 * Returns a year.
	 *
	**/
	public function getYear() {
		return $this->year;	
	}
	
	/**
	 *
	 * Returns an hour with preceding zero or false, when hour was not given to the object.
	 *
	**/
	public function getHour() {
		if(!is_null($this->hour))
		{
			return AddZeros($this->hour);	
		} 	
		else
		{
			return false;	
		}
	}
	
	/**
	 *
	 * Returns a minute with preceding zero or false, when it is empty. 
	 *
	**/
	public function getMinute() {
		if(!is_null($this->minute))
		{
			return AddZeros($this->minute);	
		}	
		else
		{
			return false;	
		}
	}	
	
	/**
	 *
	 * Returns a second with preceding zero or false, when it is empty.
	 *
	**/
	public function getSecond() {
		if(!is_null($this->second))
		{
			return AddZeros($this->second);	
		}	
		else
		{
			return false;	
		}
	}
	
	/**
	 *
	 * @param $flag Bollean
	 *
	 * Returns an hour with minutes (HH:MM) or false, when the hour was not given to the object. If $flag is the true, it also adds seconds.
	 *
	**/
	public function getTime($flag = false) {
		if($time = $this->getHour())
		{
			if($this->getMinute()) {
				$time .= ":".$this->getMinute();	
			}
			else {
				$time .= ":00";	
			}
			
			if($flag && $second = $this->getSecond()) {
				$time .= ':'.AddZeros($second);	
			}
			
			return $time;
		}
		else {
			return false;	
		}
	}

	/**
	 *
	 * Returns the date and time (without seconds) with month written words and also after the year is the abbreviation "r." (from Polish "rok" [year]), therefore: DD month YYYYr., HH:MM.
	 *
	**/
	public function getDate() {
		$day = ($this->getDay() > 0) ? $this->getDay().' ' : null;
		$month = ($this->month > 0) ? self::$polish_months[$this->month] : null;
		$year = $this->getYear().'r.';
		
		$date = $day.$month.' '.$year;
		
		if($time = $this->getTime())
		{
			$date .=	', '.$time;
		}	 
		
		return $date;
	}
	
	/**
	 *
	 * Returns short date in fallowing shape: DD.MM.YYYYr.
	 *
	**/
	public function getShortly() {
		$date[] = ($this->getDay() > 0) ? AddZeros($this->getDay()) : null;
		$date[] = AddZeros($this->getMonth());
		$date[] = $this->getYear()."r.";
		
		$shortly = implode('.', $date);
		
		return $shortly;
	}
	
	/**
	 *
	 * Returns date as DateTime type, so in this shape: YYYY-MM-DD HH:MM:SS.
	 *
	**/
	public function get() {
		$date = AddZeros($this->getYear(), 4).'-'.AddZeros($this->getMonth()).'-'.AddZeros($this->getDay()).' ';
		
		$date .= ($hour = $this->getHour()) ? AddZeros($hour) : "00";
		$date .= ':';
		$date .= ($minute = $this->getMinute()) ? AddZeros($minute) : "00";
		$date .= ':';
		$date .= ($second = $this->getSecond()) ? AddZeros($second) : "00";
		
		return $date;
	}

	/**
	 *
	 * Returns the date as unix timestamp (a number of seconds since 01.01.1970).
	 *
	**/
	public function getUnixTimestamp() {
		return mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year);
	}
}

?>