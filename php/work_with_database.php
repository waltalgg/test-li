<?php

require_once($_SERVER['DOCUMENT_ROOT'].'\php\config_database.php');
class WorkWithDatabase
{
	private static function TryConnectToDB()
	{
		try
		{
			$authDB = ConfigDatabase::AuthDB();
			$pdo = new PDO('mysql:host='.$authDB['host'].';dbname='.$authDB['table'], $authDB['login'], $authDB['password']);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $pdo;
		}
		catch(PDOException $e)
		{
			echo "Ошибка подключения: " . $e->getMessage();
		}
	}

	public static function ConnectToDB()
	{
		return self::TryConnectToDB();
	}

}