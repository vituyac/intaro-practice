CREATE TABLE IF NOT EXISTS sections (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL UNIQUE,
    parent_id INTEGER,
    FOREIGN KEY (parent_id) REFERENCES sections(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    brand VARCHAR(255) NOT NULL,
    model VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INTEGER REFERENCES sections(id) NOT NULL,
    UNIQUE (brand, model)
);

CREATE TABLE IF NOT EXISTS offers (
    id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    image TEXT,
    price DECIMAL(10, 2) NOT NULL,
    color VARCHAR(100),
    discount INTEGER DEFAULT 0,
    is_popular BOOLEAN DEFAULT FALSE,
    is_on_sale BOOLEAN DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    external_id INTEGER,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);
