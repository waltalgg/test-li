-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.2
-- Время создания: Сен 29 2024 г., 20:38
-- Версия сервера: 8.2.0
-- Версия PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `libase`
--

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` bigint NOT NULL,
  `username` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `reserv` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `reserv`) VALUES
(4, 'Lois', '$2y$10$sZYjOgYxtxO5wpPkHMpYn.XjJH/LHUOWaYakYPR9VUEUIoaqVojWi', NULL),
(5, 'Meg', '$2y$10$wvooGjm8elcTLidGjCvocO6rjc20XVWkzWPoobzdB/uQlnDeH/z8a', NULL),
(6, 'Chris', '$2y$10$fBdnivyDbtXPAJidYhqXcOObC7wOZXEoGtI8QD07SuIxYqyJSWd62', NULL),
(7, 'Stewie', '$2y$10$Z49jR4QD/PqMQBwsehnV2eKHg0fJNx3X0C00uwPSl/zZVpB8/r6ua', NULL),
(8, 'Brian', '$2y$10$NQT/l7H9cVu6J/PCVoKT3uCKXRP/I6TGtT.ywoz4eqQeaHSyUiLri', NULL),
(9, 'Peter', '$2y$10$eswr67m4tSzXUMQyHKsW1eBxd.95m4TMg/BegWRvkyjAX44lgpp/a', NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
