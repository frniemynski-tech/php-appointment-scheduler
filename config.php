<?php
/**
 * Plik konfiguracyjny dla aplikacji "SalonManager"
 *
 * Przechowuje stałe dane dostępowe do bazy danych (DSN, użytkownik, hasło)
 * oraz inne ustawienia aplikacji (np. strefa czasowa).
 */

// 1. Ustawienie domyślnej strefy czasowej (kluczowe dla walidacji dat)
date_default_timezone_set('Europe/Warsaw');

// 2. Definicja stałych połączenia z bazą danych MySQL (PDO)

// Host bazy danych (XAMPP)
define('DB_HOST', 'localhost');

// === KLUCZOWA ZMIANA ===
// Nazwa bazy danych (ta, którą właśnie utworzyliśmy)
define('DB_NAME', 'salon_db');
// =======================

// Nazwa użytkownika bazy danych (domyślna dla XAMPP)
define('DB_USER', 'root');

// Hasło użytkownika bazy danych (domyślne dla XAMPP jest puste)
define('DB_PASS', '');

// 3. Opcje konfiguracyjne dla PDO
// Zapewniają odpowiedni tryb błędów i kodowanie
define('PDO_OPTIONS', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_general_ci'
]);

?>