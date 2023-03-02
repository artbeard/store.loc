-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 02 2023 г., 20:25
-- Версия сервера: 8.0.29
-- Версия PHP: 8.1.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- База данных: `store_test`
--

-- --------------------------------------------------------

--
-- Структура таблицы `balance`
--

CREATE TABLE `balance` (
                           `id` int NOT NULL,
                           `product_id` int DEFAULT NULL,
                           `amount` int NOT NULL,
                           `cost` double NOT NULL,
                           `balance_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `preorder`
--

CREATE TABLE `preorder` (
                            `id` int NOT NULL,
                            `product_id` int DEFAULT NULL,
                            `document_prop` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `amount` int NOT NULL,
                            `ordered_at` date NOT NULL,
                            `price` double DEFAULT NULL,
                            `sent_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `product`
--

CREATE TABLE `product` (
                           `id` int NOT NULL,
                           `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `statement`
--

CREATE TABLE `statement` (
                             `id` int NOT NULL,
                             `product_id` int DEFAULT NULL,
                             `document_prop` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                             `post_type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
                             `posted_at` date NOT NULL,
                             `amount` int NOT NULL,
                             `cost` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `balance`
--
ALTER TABLE `balance`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_ACF41FFE4584665A` (`product_id`);

--
-- Индексы таблицы `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
    ADD PRIMARY KEY (`version`);

--
-- Индексы таблицы `preorder`
--
ALTER TABLE `preorder`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_D9B775974584665A` (`product_id`);

--
-- Индексы таблицы `product`
--
ALTER TABLE `product`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `statement`
--
ALTER TABLE `statement`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C0DB51764584665A` (`product_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `balance`
--
ALTER TABLE `balance`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `preorder`
--
ALTER TABLE `preorder`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `product`
--
ALTER TABLE `product`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `statement`
--
ALTER TABLE `statement`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `balance`
--
ALTER TABLE `balance`
    ADD CONSTRAINT `FK_ACF41FFE4584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- Ограничения внешнего ключа таблицы `preorder`
--
ALTER TABLE `preorder`
    ADD CONSTRAINT `FK_D9B775974584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- Ограничения внешнего ключа таблицы `statement`
--
ALTER TABLE `statement`
    ADD CONSTRAINT `FK_C0DB51764584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);
COMMIT;
