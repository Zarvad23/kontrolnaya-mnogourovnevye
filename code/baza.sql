-- скрипт создания базы данных для доски объявлений
-- тут создаем саму базу, таблицы и закидываем немного тестовых рубрик

CREATE DATABASE IF NOT EXISTS doska_obyav CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE doska_obyav;

-- на всякий случай удаляем старые таблицы если скрипт запускают повторно
DROP TABLE IF EXISTS obyavleniya;
DROP TABLE IF EXISTS rubriki;

-- таблица рубрик (категорий объявлений)
CREATE TABLE rubriki (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nazvanie VARCHAR(100) NOT NULL
);

-- таблица самих объявлений
-- data_podnyatiya - тут храним когда объявление последний раз поднимали,
-- по ней и проверяем что подняли не раньше чем сутки назад
CREATE TABLE obyavleniya (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_rubriki INT NOT NULL,
    zagolovok VARCHAR(255) NOT NULL,
    opisanie TEXT NOT NULL,
    avtor VARCHAR(100) NOT NULL,
    telefon VARCHAR(50),
    data_dobavlen DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_podnyatiya DATETIME DEFAULT NULL
);

-- закидываем несколько рубрик для примера
INSERT INTO rubriki (nazvanie) VALUES
('Недвижимость'),
('Транспорт'),
('Работа'),
('Электроника'),
('Разное');

-- пара тестовых объявлений чтобы сразу было что посмотреть
INSERT INTO obyavleniya (id_rubriki, zagolovok, opisanie, avtor, telefon, data_dobavlen) VALUES
(1, 'Сдам квартиру на длительный срок', 'Сдается 1-комнатная квартира возле центра, есть вся мебель.', 'Ольга', '+7 900 111-22-33', NOW()),
(2, 'Продам велосипед', 'Велосипед горный, б/у 1 год, состояние хорошее.', 'Игорь', '+7 900 222-33-44', NOW()),
(3, 'Требуется репетитор по математике', 'Ищу репетитора для подготовки к ЕГЭ, 2 раза в неделю.', 'Мария', '+7 900 333-44-55', NOW());
