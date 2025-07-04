-- Создание таблицы Section (раздел каталога)
CREATE TABLE Section (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL UNIQUE,
    parent_id INTEGER,
    FOREIGN KEY (parent_id) REFERENCES Section(id) ON DELETE SET NULL
);


