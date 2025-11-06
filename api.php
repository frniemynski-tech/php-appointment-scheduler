<?php
/**
 * =================================================================
 * API dla aplikacji "SalonManager" (Portfolio Projekt 1)
 * =================================================================
 *
 * Ten plik jest w 100% autorskim API (backend),
 * napisanym w czystym PHP z użyciem PDO.
 *
 * Jest to adaptacja sprawdzonego wzorca z projektu "planjazd.pl",
 * przeniesiona na nową logikę biznesową (Salon Fryzjerski).
 *
 * --- Dokumentacja Endpointów ---
 *
 * 1. GET ?action=get_appointments
 * - Zwraca: JSON [ { ... }, { ... } ] - tablicę wszystkich wizyt.
 *
 * 2. POST ?action=add_appointment
 * - Oczekuje: JSON { "visit_date": "...", "visit_time": "...", "stylist": "...", "client_name": "..." }
 * - Zwraca (Sukces): JSON { "status": "success", "data": {nowy_obiekt} } (Kod 201)
 * - Zwraca (Błąd): JSON { "status": "error", "message": "..." } (Kod 422)
 *
 * 3. DELETE ?action=delete_appointment&id=X
 * - Oczekuje: ID w URL.
 * - Zwraca (Sukces): JSON { "status": "success", "message": "..." } (Kod 200)
 *
 */

// --- 1. Konfiguracja ---
// Ładujemy nasz nowy config.php (który łączy się z 'salon_db')
require_once 'config.php';
header('Content-Type: application/json');

// --- 2. Funkcje pomocnicze ---

/**
 * Wysyła odpowiedź JSON i kończy działanie skryptu.
 */
function send_json($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Walidacja danych WIZYTY (ADAPTACJA z 'validate_drive_data')
 *
 * @param array $input_data Dane wejściowe (z json_decode).
 * @return array Tablica ['errors' => [], 'data' => []]
 */
function validate_appointment_data($input_data) {
    
    // TŁUMACZENIE: Oczekujemy teraz kluczy pasujących do salonu
    $clean_data = [
        'visit_date'  => trim($input_data['visit_date'] ?? ''),
        'visit_time'  => trim($input_data['visit_time'] ?? ''),
        'stylist'     => trim($input_data['stylist'] ?? ''),
        'client_name' => trim($input_data['client_name'] ?? '')
    ];
    
    $errors = [];

    // --- Reguła 1: Żadne pole nie może być puste ---
    if (empty($clean_data['visit_date'])) { $errors[] = "Data wizyty jest wymagana."; }
    if (empty($clean_data['visit_time'])) { $errors[] = "Godzina wizyty jest wymagana."; }
    if (empty($clean_data['stylist'])) { $errors[] = "Stylista jest wymagany."; }
    if (empty($clean_data['client_name'])) { $errors[] = "Imię klienta jest wymagane."; }

    // --- Reguła 2: Data nie może być z przeszłości ---
    if (!empty($clean_data['visit_date'])) {
        try {
            $today = new DateTime('today'); 
            $selectedDate = new DateTime($clean_data['visit_date']);
            if ($selectedDate < $today) {
                $errors[] = "Data wizyty nie może być z przeszłości.";
            }
        } catch (Exception $e) {
            $errors[] = "Nieprawidłowy format daty. Oczekiwano RRRR-MM-DD.";
        }
    }

    // --- Reguła 3: Godziny robocze (np. 09:00 - 18:00) ---
    if (!empty($clean_data['visit_time'])) {
        if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $clean_data['visit_time'])) {
            $errors[] = "Nieprawidłowy format godziny (oczekiwano HH:MM).";
        } else {
            // TŁUMACZENIE: Zmieniamy godziny robocze, aby pasowały do salonu
            if ($clean_data['visit_time'] < '09:00' || $clean_data['visit_time'] > '18:00') {
                $errors[] = "Godzina wizyty musi być w godzinach otwarcia salonu (09:00 - 18:00).";
            }
        }
    }
    
    // --- Reguła 4: Nazwiska nie mogą zawierać cyfr ---
    if (!empty($clean_data['stylist']) && preg_match('/\d/', $clean_data['stylist'])) {
        $errors[] = "Nazwisko stylisty nie może zawierać cyfr.";
    }
    if (!empty($clean_data['client_name']) && preg_match('/\d/', $clean_data['client_name'])) {
        $errors[] = "Imię klienta nie może zawierać cyfr.";
    }
    
    return [
        'errors' => $errors,
        'data'   => $clean_data
    ];
}

// --- 3. Główna logika aplikacji (Router) ---
try {
    // Połączenie z bazą (używa stałych z config.php)
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, PDO_OPTIONS);

    // Odczytanie metody i akcji
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';

    // ===============
    // Trasa 1: GET ?action=get_appointments (Pobierz wizyty)
    // ===============
    if ($method === 'GET' && $action === 'get_appointments') {
        
        // TŁUMACZENIE: Używamy nowej tabeli 'appointments' i nowych kolumn
        $stmt = $pdo->query("SELECT id, visit_date, visit_time, stylist, client_name 
                            FROM appointments 
                            ORDER BY visit_date ASC, visit_time ASC");
        
        $appointments = $stmt->fetchAll();
        send_json($appointments, 200);

    // ===============
    // Trasa 2: POST ?action=add_appointment (Dodaj wizytę)
    // ===============
    } elseif ($method === 'POST' && $action === 'add_appointment') {
        
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data === null) {
            send_json(['status' => 'error', 'message' => 'Nieprawidłowy format danych JSON.'], 400);
        }

        // Używamy naszej nowej, dedykowanej funkcji walidacyjnej
        $validation_result = validate_appointment_data($data);
        $errors = $validation_result['errors'];
        $validated_data = $validation_result['data'];

        if (!empty($errors)) {
            send_json(['status' => 'error', 'message' => $errors[0]], 422); // Błąd walidacji
        }

        // TŁUMACZENIE: Używamy nowej tabeli i kolumn do zapisu
        $stmt = $pdo->prepare("INSERT INTO appointments (visit_date, visit_time, stylist, client_name) 
                             VALUES (:visit_date, :visit_time, :stylist, :client_name)");
        
        // Wykonujemy zapytanie z oczyszczonymi danymi
        $stmt->execute([
            'visit_date'  => $validated_data['visit_date'],
            'visit_time'  => $validated_data['visit_time'],
            'stylist'     => $validated_data['stylist'],
            'client_name' => $validated_data['client_name']
        ]);
        
        // Przygotowujemy odpowiedź dla frontendu (musi dostać nowy obiekt)
        $new_id = $pdo->lastInsertId();
        $new_appointment = [
            'id'          => (int)$new_id,
            'visit_date'  => $validated_data['visit_date'],
            'visit_time'  => $validated_data['visit_time'],
            'stylist'     => $validated_data['stylist'],
            'client_name' => $validated_data['client_name']
        ];
        
        send_json(['status' => 'success', 'data' => $new_appointment], 201); // 201 Utworzono

    // ===============
    // Trasa 3: DELETE ?action=delete_appointment&id=X (Usuń wizytę)
    // ===============
    } elseif ($method === 'DELETE' && $action === 'delete_appointment') {
        
        $id = $_GET['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            send_json(['status' => 'error', 'message' => 'Brak lub nieprawidłowe ID wizyty.'], 400);
        }

        // TŁUMACZENIE: Używamy nowej tabeli 'appointments'
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = :id");
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            send_json(['status' => 'success', 'message' => 'Wizyta została pomyślnie anulowana.']);
        } else {
            send_json(['status' => 'error', 'message' => 'Nie znaleziono wizyty o podanym ID.'], 404);
        }

    // ===============
    // Trasa 4: Nieprawidłowe żądanie
    // ===============
    } else {
        send_json(['status' => 'error', 'message' => 'Nieprawidłowe żądanie API. Dostępne akcje: get_appointments, add_appointment, delete_appointment'], 400);
    }

// --- 4. Obsługa Błędów Globalnych ---
} catch (PDOException $e) {
    error_log('Błąd PDO: ' . $e->getMessage());
    send_json(['status' => 'error', 'message' => 'Błąd serwera: Wystąpił problem z bazą danych.'], 500);
} catch (Exception $e) {
    error_log('Błąd ogólny: ' . $e->getMessage());
    send_json(['status' => 'error', 'message' => 'Błąd serwera: Wystąpił nieoczekiwany błąd.'], 500);
}

?>