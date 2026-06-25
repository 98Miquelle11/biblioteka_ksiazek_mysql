### Jak np używać PDO w zapytaniach

$stmt = $pdo->prepare("SELECT * FROM czytelnik WHERE email = :email");
$stmt->execute([
    'email' => $email,
]);

$user = $stmt->fetch();


### Jak np nie używać

$sql = "SELECT * FROM czytelnik WHERE email = '$email'";



# Biblioteka MySQL

Projekt systemu bibliotecznego wykonany w PHP, MySQL, HTML, CSS i JavaScript.

## Wymagania

- XAMPP
- PHP
- MySQL / MariaDB
- phpMyAdmin
- Visual Studio Code
- Git
- GitHub
- Chrome

## Uruchomienie

1. Skopiuj projekt do folderu `htdocs`.
2. Uruchom Apache i MySQL w XAMPP.
3. Wejdź do phpMyAdmin:
   `http://localhost/phpmyadmin`
4. Zaimportuj:
   - `database/schema.sql`
   - `database/seed.sql`
5. Skopiuj plik:
   `config/.env.example`
   jako:
   `config/.env`
6. Ustaw dane bazy w `.env`.
7. Otwórz aplikację:
   `http://localhost/biblioteka-mysql/public`

## Dane logowania

Administrator:

- email: admin@biblioteka.local
- hasło: password

Użytkownik:

- email: anna.kowalska@example.com
- hasło: password

## Funkcjonalności

- rejestracja i logowanie
- panel użytkownika
- zmiana danych i hasła
- katalog książek
- wyszukiwanie i filtrowanie
- paginacja
- rezerwacje
- panel administratora
- CRUD książek
- CRUD autorów
- CRUD wydawnictw
- CRUD egzemplarzy
- statystyki