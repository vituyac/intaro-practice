-- Каталог
CREATE TABLE section (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL UNIQUE,
    parent_id INTEGER,
    FOREIGN KEY (parent_id) REFERENCES Section(id) ON DELETE SET NULL
);

-- Товары
CREATE TABLE product (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    brand VARCHAR(255) NOT NULL,
    model VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INTEGER REFERENCES Section(id) NOT NULL,
    UNIQUE (brand, model)
);

-- Торговые предложения
CREATE TABLE offer (
    id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES product(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    image TEXT,
    price DECIMAL(10, 2) NOT NULL,
    color VARCHAR(100),
    discount INTEGER DEFAULT 0,
    is_popular BOOLEAN DEFAULT FALSE,
    is_on_sale BOOLEAN DEFAULT FALSE
);
