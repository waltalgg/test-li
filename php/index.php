<?php

require_once($_SERVER['DOCUMENT_ROOT'].'\php\work_with_database.php');

header('Content-type: json/application');
header('Access-control-Allow-Origin: *');
header('Access-control-Allow-Headers: *');
header('Access-control-Allow-Methods: *');
header('Access-control-Allow-Credentials: true');

class API
{
	private static $connect;

	private static function AddUser()
	{
		$data = json_decode(file_get_contents('php://input'), true);
		if (!isset($data['username']) && !isset($data['password']))
		{
			http_response_code(400);
			echo json_encode(['error' => 'Недостаточно данных для создания пользователя']);
			return;
		}

		$sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
		$stmt = self::$connect->prepare($sql);

		$stmt->bindValue(':username', $data['username'], PDO::PARAM_STR);
		$stmt->bindValue(':password', $data['password'], PDO::PARAM_STR);

		if ($stmt->execute())
		{
			http_response_code(201);
			echo json_encode(['message' => 'Пользователь успешно создан']);
		}
		else
		{
			http_response_code(500);
			echo json_encode(['error' => 'Ошибка при создании пользователя']);
		}
	}

	private static function UpdateUser($paramsList)
	{
		if(self::CheckErrorsUsersAPI($paramsList) === false)
		{
			return;
		}

		if (!isset($data['username']) || !isset($data['password']))
		{
			http_response_code(400);
			echo json_encode(['error' => 'Нет данных для обновления пользователя']);
			return;
		}

		$userID = intval($paramsList[2]);

		// Первый запрос на поиск по ID
		$data = json_decode(file_get_contents('php://input'), true);
		$sql = "SELECT * FROM users WHERE id = :id";
		$stmt = self::$connect->prepare($sql);
		$stmt->bindValue(':id', $userID, PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->rowCount() == 0)
		{
			http_response_code(404);
			echo json_encode(['error' => 'Пользователь не найден']);
			return;
		}

		// Второй запрос уже на Update
		$sql = "UPDATE users SET username = :username, password = :password WHERE id = :id";
		$stmt = self::$connect->prepare($sql);
		$stmt->bindValue(':username', $data['username'], PDO::PARAM_STR);
		$stmt->bindValue(':password', $data['password'], PDO::PARAM_STR);
		$stmt->bindValue(':id', $userID, PDO::PARAM_INT);

		if ($stmt->execute())
		{
			http_response_code(200);
			echo json_encode(['message' => 'Пользователь успешно обновлен']);
		}
		else
		{
			http_response_code(500);
			echo json_encode(['error' => 'Ошибка при обновлении пользователя']);
		}
	}

	private static function DeleteUser($paramList)
	{
		$userID = ''; // TODO: Заглушка
		$sql = "SELECT * FROM users WHERE id = :id";
		$stmt = self::$connect->prepare($sql);
		$stmt->bindValue(':id', $userID, PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->rowCount() == 0) {
			http_response_code(404);
			echo json_encode(['error' => 'Пользователь не найден']);
			return;
		}

		$sql = "DELETE FROM users WHERE id = :id";
		$stmt = self::$connect->prepare($sql);
		$stmt->bindValue(':id', $userID, PDO::PARAM_INT);

		if ($stmt->execute())
		{
			http_response_code(200);
			echo json_encode(['message' => 'Пользователь успешно удален']);
		}
		else
		{
			http_response_code(500);
			echo json_encode(['error' => 'Ошибка при удалении пользователя']);
		}
	}

	private static function AuthUser()
	{

		$data = json_decode(file_get_contents('php://input'), true);
		if (!isset($data['username']) || !isset($data['password']))
		{
			http_response_code(400);
			echo json_encode(['error' => 'Недостаточно данных для авторизации']);
			return;
		}

		$sql = "SELECT * FROM users WHERE username = :username AND password = :password";
		$stmt = self::$connect->prepare($sql);

		$stmt->bindValue(':username', $data['username'], PDO::PARAM_STR);
		$stmt->bindValue(':password', $data['password'], PDO::PARAM_STR);


		$stmt->execute();

		if ($stmt->rowCount() == 1)
		{
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			http_response_code(200);
			echo json_encode(['message' => 'Авторизация успешна', 'user' => $user]);
		}
		else
		{
			http_response_code(401);
			echo json_encode(['error' => 'Неверный логин или пароль']);

		}
	}

	private static function GetUser($paramsList)
	{
		if(self::CheckErrorsUsersAPI($paramsList) === false)
		{
			return;
		}

		$userID = intval($paramsList[2]);

		$sql = "SELECT * FROM users WHERE id = :id";
		$stmt = self::$connect->prepare($sql);
		$stmt->bindValue(':id', $userID, PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->rowCount() == 0)
		{
			http_response_code(404);
			echo json_encode(['error' => 'Пользователь не найден']);
			return;
		}

		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		http_response_code(200);
		echo json_encode($user);
	}

    private static function GetAllUser()
	{
		$sql = "SELECT * FROM users";
		$stmt = self::$connect->prepare($sql);
		$stmt->execute();

		if ($stmt->rowCount() == 0)
		{
			http_response_code(404);
			echo json_encode(['error' => 'Пользователей нет в базе данных']);
			return;
		}

		$usersList = $stmt->fetch(PDO::FETCH_ASSOC);
		http_response_code(200);
		echo json_encode($usersList);
    }

	private static function CheckErrorsUsersAPI($paramsList)
	{
		if (!isset($paramsList[2]))
		{
			http_response_code(400);
			echo json_encode(['message' => 'Пользователь не указан']);
			return false;
		}
		if (!is_numeric($paramsList[2]))
		{
			http_response_code(400);
			echo json_encode(['message' => 'Неверно указан идентификатор пользователя']);
			return false;
		}

		if (intval($paramsList[2])=== 0 && intval($paramsList[2]) < 0) // intval() возвращает 0, если переменная не является числом
		{
			http_response_code(400);
			echo json_encode(['message' => 'Неверно указан идентификатор пользователя']);
			return false;
		}

		return true;
	}

	private static function APIUsers($paramsList)
	{
        // Если запрос в формате php/users , то выводим всех пользователей
        if(!isset($paramsList[1]))
        {
			self::GetAllUser();
            return;
		}

		switch ($paramsList[1])
		{
			case 'add':
				self::AddUser();
				break;

			case 'update':
				self::UpdateUser($paramsList);
				break;

			case 'delete':
				self::DeleteUser($paramsList);
				break;

			case 'auth':
				self::AuthUser();
				break;

			case 'get':
				self::GetUser($paramsList);
				break;

			default: http_response_code(404);
				echo json_encode(['error' => 'Неверный метод']);
				break;
		}
	}
	public static function APIHandler($handler)
	{
		self::$connect = WorkWithDatabase::ConnectToDB();
		$paramsList = explode('/', $handler); // Разбиение строки в формате: ../php/$paramsList[0]/$paramsList[1]/$paramsList[2]
		switch ($paramsList[0]) {
			case 'users':
				self::APIUsers($paramsList);
				break;
			/*
			 *  ...
			 */
		}
	}
}


// ...
if(isset($_GET['w']))
{
	API::APIHandler($_GET['w']);
}


