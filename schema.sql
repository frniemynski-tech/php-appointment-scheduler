/**
 * Schemat bazy danych dla aplikacji "SalonManager"
 *
 * Tabela 'appointments' przechowuje informacje o zaplanowanych
 * wizytach u fryzjera/stylisty.
 */
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Zastąpiliśmy 'drive_date' i 'drive_time'
    visit_date DATE NOT NULL,
    visit_time TIME NOT NULL,
    
    -- Zastąpiliśmy 'instructor' i 'student'
    stylist VARCHAR(255) NOT NULL,
    client_name VARCHAR(255) NOT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

/**
 * OPCJONALNIE: Tabela 'stylists'
 * (Jeśli chcesz rozbudować projekt o Relacje - Pomysł 3, który omawialiśmy)
 * Na razie trzymamy się prostej wersji.
 */