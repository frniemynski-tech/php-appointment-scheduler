SalonManager - System Rezerwacji API (Projekt Portfolio)

To jest w 100% autorski projekt typu Full-Stack, stworzony jako część mojego portfolio. Aplikacja demonstruje umiejętność budowania bezpiecznego, walidowanego backendu w czystym PHP i PDO, wraz z dedykowanym, responsywnym frontendem.

Projekt implementuje logikę biznesową dla prostego systemu rezerwacji wizyt w salonie fryzjerskim.

Link do Live Demo: [Wstaw tutaj swój link z hostingu, np. 000webhost]

Zastosowane Technologie

Backend:

Czysty PHP (Podejście proceduralne z funkcjami pomocniczymi)

MySQL (Baza danych)

PDO (Bezpieczne połączenie i wykonywanie zapytań)

Frontend:

HTML5

Tailwind CSS (Dla nowoczesnego, responsywnego designu)

Czysty JavaScript (ES6+)

Fetch API (Do komunikacji z backendem)

Kluczowe Funkcjonalności

Pełen CRUD: Użytkownicy mogą tworzyć (Create), odczytywać (Read) i usuwać (Delete) wizyty.

Walidacja po Stronie Serwera: API jest w pełni zabezpieczone przed błędnymi danymi. Implementuje następujące reguły:

Wszystkie pola są wymagane.

Data wizyty nie może być z przeszłości.

Godzina wizyty musi mieścić się w godzinach pracy salonu (09:00 - 18:00).

Format daty i godziny jest sprawdzany (YYYY-MM-DD, HH:MM).

Nazwiska stylisty i klienta nie mogą zawierać cyfr.

Bezpieczeństwo (Ochrona przed SQL Injection): Wszystkie dane wejściowe (POST, DELETE) są przetwarzane wyłącznie przez zapytania parametryzowane (PDO::prepare i PDO::execute).

Architektura "API-First": Backend jest napisany jako API stateless (bezstanowe), które zwraca odpowiedzi w formacie JSON i komunikuje się z dowolnym klientem (w tym przypadku, autorskim frontendem HTML/JS).

Struktura API (Endpointy)

Backend (api.php) obsługuje następujące trasy:

Metoda

Akcja (Parametr ?action=)

Opis

GET

get_appointments

Pobiera listę wszystkich wizyt.

POST

add_appointment

Dodaje nową wizytę. Wymaga JSON w ciele.

DELETE

delete_appointment

Usuwa wizytę. Wymaga &id=X w URL.

Instrukcja Uruchomienia Lokalnego (XAMPP)

Sklonuj Repozytorium:

git clone [TWÓJ ADRES REPOZYTORIUM]


Lub pobierz pliki .zip.

Umieść Pliki:
Umieść cały folder projektu w C:\xampp\htdocs\.

Baza Danych:

Otwórz http://localhost/phpmyadmin/.

Stwórz nową bazę danych o nazwie salon_db i kodowaniu utf8mb4_general_ci.

Wybierz bazę salon_db, przejdź do zakładki SQL i wklej zawartość pliku schema.sql z tego repozytorium, aby utworzyć tabelę appointments.

Konfiguracja:

W głównym folderze projektu znajdź plik config.php.example.

Stwórz jego kopię i zmień nazwę na config.php.

Otwórz config.php i upewnij się, że dane pasują do Twojej lokalnej bazy XAMPP (domyślnie root i puste hasło).

Uruchom:
Otwórz w przeglądarce adres http://localhost/salon_rezerwacje/ (lub nazwę folderu, którego użyłeś). Aplikacja jest gotowa do testowania.