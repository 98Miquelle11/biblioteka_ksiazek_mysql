# Biblioteka Książek

Projekt systemu bibliotecznego wykonany w PHP, MySQL, HTML, CSS i JavaScript.

## Wymagania

- XAMPP,
- PHP,
- MySQL / MariaDB,
- phpMyAdmin,
- Visual Studio Code,
- Git,
- GitHub,
- Chrome.

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

- Rejestracja i logowanie,
- Panel użytkownika,
- Zmiana danych i hasła,
- Katalog książek,
- Wyszukiwanie i filtrowanie,
- Paginacja,
- Rezerwacje,
- Panel administratora,
- CRUD książek,
- CRUD autorów,
- CRUD wydawnictw,
- CRUD egzemplarzy,
- Statystyki.
