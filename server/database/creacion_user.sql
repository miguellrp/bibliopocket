USE bibliopocketDB;
DROP USER IF EXISTS probaBiblio@'localhost';
CREATE USER probaBiblio@'localhost' IDENTIFIED BY "pass1234";
GRANT ALL PRIVILEGES ON bibliopocketdb.* TO probaBiblio@'localhost';