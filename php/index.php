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

	/**
	 * Создает нового пользователя.
	 *
	 * @return void
	 */
	private static function AddUser()
	{
		$data = json_decode(file_get_contents('php://input'), true);
		if(self::CheckJSON($data) === false)
		{
			return;
		}

		$sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
		$stmt = self::$connect->prepare($sql);

		$stmt->bindValue(':username', mysqli_real_escape_string($data['username']), PDO::PARAM_STR);
		$stmt->bindValue(':password', password_hash($data['password'], PASSWORD_BCRYPT), PDO::PARAM_STR);

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

	/**
	 * Обновляет информацию о пользователе.
	 *
	 * @param array $paramsList Массив параметров запроса.
	 * @param int $paramsList[2] - id пользователя.
	 *
	 * @return void
	 */

	private static function UpdateUser($paramsList)
	{
		$data = json_decode(file_get_contents('php://input'), true);
		if(self::CheckErrorsUsersAPI($paramsList, true) === false || self::SearchID(intval($paramsList[2])) === false || self::CheckJSON($data) === false) return;

		$sql = "SELECT * FROM users WHERE username = :username";
		$stmt = self::$connect->prepare($sql);
		$stmt->bindValue(':username', mysqli_real_escape_string($data['username']), PDO::PARAM_STR);

		if ($stmt->rowCount() > 0)
		{
			http_response_code(403);
			echo json_encode(['error' => 'Такой пользователь уже существует']);
			return;
		}

		$sql = "UPDATE users SET username = :username, password = :password WHERE id = :id";
		$stmt = self::$connect->prepare($sql);
		$stmt->bindValue(':username', mysqli_real_escape_string($data['username']), PDO::PARAM_STR);
		$stmt->bindValue(':password',password_hash($data['password']), PDO::PARAM_STR);
		$stmt->bindValue(':id', intval($paramsList[2]), PDO::PARAM_INT);

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

	/**
	 * Удаляет пользователя.
	 *
	 * @param array $paramsList Массив параметров запроса.
	 * @param int $paramsList[2] - id пользователя.
	 *
	 * @return void
	 */

	private static function DeleteUser($paramsList)
	{
		if(self::CheckErrorsUsersAPI($paramsList) === false || self::SearchID(intval($paramsList[2])) === false) return;

		$sql = "DELETE FROM users WHERE id = :id";
		$stmt = self::$connect->prepare($sql);
		$stmt->bindValue(':id', intval($paramsList[2]), PDO::PARAM_INT);

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

	/**
	 * Аутентификация пользователя.
	 *
	 * @return void
	 */
	private static function AuthUser()
	{
		$data = json_decode(file_get_contents('php://input'), true);
		if(self::CheckJSON($data) === false) return;

		$sql = "SELECT * FROM users WHERE username = :username AND password = :password";
		$stmt = self::$connect->prepare($sql);
		$stmt->bindValue(':username', mysqli_real_escape_string($data['username']), PDO::PARAM_STR);
		$stmt->bindValue(':password', password_hash($data['password']), PDO::PARAM_STR);
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

	/**
	 * Возвращает информацию о пользователе.
	 *
	 * @param array $paramsList Массив параметров запроса.
	 * @param int $paramsList[2] - id пользователя.
	 *
	 * @return void
	 */
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

	/**
	 * Проверяет, существует ли пользователь с указанным ID.
	 *
	 * @param int $userID ID пользователя.
	 *
	 * @return bool True, если пользователь существует, иначе False.
	 */
	private static function SearchID($userID)
	{
		$sql = "SELECT * FROM users WHERE id = :id";
		$stmt = self::$connect->prepare($sql);
		$stmt->bindValue(':id', $userID, PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->rowCount() == 0)
		{
			http_response_code(404);
			echo json_encode(['error' => 'Пользователь не найден']);
			return false;
		}
		return true;
	}

	/**
	 * Проверяет, валидны ли параметры запроса и существует ли пользователь.
	 *
	 * @param array $paramsList Массив параметров запроса.
	 * @param int $paramsList[2] - id пользователя.
	 *
	 * @return bool True, если параметры валидны и пользователь существует, иначе False.
	 */
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

	/**
	 * Проверяет, являются ли входные данные массивом данных и содержит ли он все необходимые поля.
	 *
	 * @param array $data Входные данные в формате JSON.
	 *
	 * @return bool True, если данные JSON-валидны и содержат все необходимые поля, иначе False.
	 */
	private static function CheckJSON($data)
	{
		if (is_array($data) === false || empty($data))
		{
			http_response_code(400);
			echo json_encode(['error' => 'Некорректный запрос']);
			return false;
		}

		if (isset($data['username']) && isset($data['password']))
		{
			return true;
		}
		else
		{
			http_response_code(400);
			echo json_encode(['error' => 'Не все данные отправлены']);
			return false;
		}
	}

	/**
	 * Обработка запросов к API для работы с базой пользователей.
	 *
	 * @param array $paramsList Массив параметров запроса.
	 *
	 * @return void
	 */

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

	/**
	 * Обработчик API-запросов.
	 *
	 * @param string $handler Строка, содержащая путь к API в формате: <br> ../php/$paramsList[0]/$paramsList[1]/$paramsList[2]
	 *
	 * @return void
	 */

	public static function APIHandler($handler)
	{
		self::$connect = WorkWithDatabase::ConnectToDB();
		$paramsList = explode('/', $handler);
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

if(isset($_GET['w']))
{
	API::APIHandler($_GET['w']);
}


