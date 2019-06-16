CREATE USER 'laravel'@'127.0.0.1' IDENTIFIED WITH mysql_native_password BY 'w65p2QMBTCAp';
GRANT ALL ON *.* TO 'laravel'@'127.0.0.1' WITH GRANT OPTION;

CREATE USER 'laravel'@'localhost' IDENTIFIED WITH mysql_native_password BY 'w65p2QMBTCAp';
GRANT ALL ON *.* TO 'laravel'@'localhost' WITH GRANT OPTION;

FLUSH PRIVILEGES;

