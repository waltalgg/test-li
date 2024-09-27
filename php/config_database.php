<?php

class ConfigDatabase
{
	private static $host = 'MySQL-8.2';
	private static $table = 'libase';
	private static $login = 'root';
	private static $password = '';


	/*
	 *
	 */
	public static function AuthDB()
	{
		$getInfo = [];
		$getInfo['host'] = self::$host;
		$getInfo['table'] = self::$table;
		$getInfo['login'] = self::$login;
		$getInfo['password'] = self::$password;
		return $getInfo;
	}


}