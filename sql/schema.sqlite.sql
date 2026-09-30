-- Schéma Sargora (SQLite)
CREATE TABLE IF NOT EXISTS categories (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  slug TEXT NOT NULL UNIQUE,
  name TEXT NOT NULL,
  description TEXT,
  sort INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS products (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  slug TEXT NOT NULL UNIQUE,
  category_id INTEGER NOT NULL REFERENCES categories(id),
  name TEXT NOT NULL,
  tagline TEXT,
  description TEXT,
  features TEXT,
  options TEXT NOT NULL,
  badge TEXT,
  theme TEXT NOT NULL DEFAULT 'gold',
  glyph TEXT,
  delivery TEXT,
  featured INTEGER NOT NULL DEFAULT 0,
  in_stock INTEGER NOT NULL DEFAULT 1,
  active INTEGER NOT NULL DEFAULT 1,
  sort INTEGER NOT NULL DEFAULT 0,
  created_at TEXT NOT NULL,
  updated_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_products_cat ON products(category_id);

CREATE TABLE IF NOT EXISTS orders (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  ref TEXT NOT NULL UNIQUE,
  access_key TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'pending',
  customer_name TEXT NOT NULL,
  email TEXT NOT NULL,
  phone TEXT NOT NULL,
  note TEXT,
  payment_method TEXT NOT NULL,
  total INTEGER NOT NULL,
  items_count INTEGER NOT NULL,
  delivery_info TEXT,
  admin_note TEXT,
  ip TEXT,
  created_at TEXT NOT NULL,
  updated_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status);

CREATE TABLE IF NOT EXISTS order_items (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
  product_id INTEGER,
  product_name TEXT NOT NULL,
  option_label TEXT,
  unit_price INTEGER NOT NULL,
  qty INTEGER NOT NULL,
  line_total INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_items_order ON order_items(order_id);

CREATE TABLE IF NOT EXISTS messages (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  kind TEXT NOT NULL DEFAULT 'devis',
  name TEXT NOT NULL,
  email TEXT NOT NULL,
  phone TEXT,
  company TEXT,
  services TEXT,
  budget TEXT,
  timeline TEXT,
  message TEXT NOT NULL,
  is_read INTEGER NOT NULL DEFAULT 0,
  ip TEXT,
  created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS subscribers (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  email TEXT NOT NULL UNIQUE,
  created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS admins (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  email TEXT NOT NULL UNIQUE,
  name TEXT NOT NULL,
  password_hash TEXT NOT NULL,
  last_login TEXT,
  created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS login_attempts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  ip TEXT NOT NULL,
  at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_attempts_ip ON login_attempts(ip, at);
