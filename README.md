# Biblioteka Książek

Aplikacja webowa umożliwiająca użytkownikom przeglądanie katalogu książek, wyszukiwanie i filtrowanie pozycji, rezerwowanie egzemplarzy oraz zarządzanie własnym kontem. Administrator ma dostęp do dodatkowego panelu umożliwiającego zarządzanie biblioteką.

## Technologie

- PHP wersja 8.0 lub nowsza,
- MySQL / MariaDB,
- HTML5,
- CSS3,
- JavaScript,
- Apache,
- XAMPP.

## Główne funkcjonalności

- Rejestracja, logowanie i wylogowanie użytkowników,
- Role użytkownika i administratora,
- Panel użytkownika,
- Edycja danych użytkownika,
- Zmiana hasła,
- Katalog książek,
- Wyszukiwanie książek po tytule i autorze,
- Filtrowanie książek według gatunku, roku wydania i formy egzemplarza,
- Podgląd szczegółów książki,
- Podgląd dostępnych egzemplarzy,
- Rezerwowanie egzemplarzy,
- Historia wypożyczeń i rezerwacji użytkownika,
- Panel administratora ze statystykami,
- Zarządzanie książkami,
- Zarządzanie autorami,
- Zarządzanie wydawnictwami,
- Zarządzanie egzemplarzami,
- Obsługa wypożyczeń i zwrotów,
- Automatyczna aktualizacja statusu egzemplarzy,
- Automatyczne naliczanie kar za opóźnione zwroty,
- Rejestrowanie zmian statusu egzemplarzy,
- Tymczasowa blokada konta po kilku nieudanych próbach logowania.

## Uruchomienie

1. Pobierz lub sklonuj repozytorium.

2. Umieść projekt w katalogu `htdocs` XAMPP.

   Katalog projektu powinien nazywać się:

   ```text
   biblioteka_ksiazek_mysql
   ```

   Przykładowa lokalizacja:

   ```text
   C:\xampp\htdocs\biblioteka_ksiazek_mysql
   ```

3. Uruchom **Apache** i **MySQL** w XAMPP.

4. Otwórz phpMyAdmin:

   ```text
   http://localhost/phpmyadmin
   ```

5. Zaimportuj pliki bazy danych w podanej kolejności:

   ```text
   database/schema.sql
   database/seed.sql
   ```


6. Skopiuj plik:

   ```text
   config/.env.example
   ```

   i zapisz jego kopię jako:

   ```text
   config/.env
   ```

7. Ustaw dane połączenia z bazą danych w pliku `config/.env`.

   Dla standardowej konfiguracji XAMPP:

   ```env
   DB_HOST=localhost
   DB_NAME=biblioteka
   DB_USER=root
   DB_PASS=""
   DB_CHARSET=utf8mb4
   APP_URL=http://localhost/biblioteka_ksiazek_mysql/public
   APP_ENV=local
   ```

8. Otwórz aplikację w przeglądarce:

   ```text
   http://localhost/biblioteka_ksiazek_mysql/public/
   ```


## Dane logowania

Po zaimportowaniu pliku `database/seed.sql` dostępne są następujące konta testowe.

### Administrator

```text
Email: admin@biblioteka.local
Hasło: password
```

### Użytkownik

```text
Email: anna.kowalska@example.com
Hasło: password
```

Dane logowania służą wyłącznie do lokalnego uruchamiania i testowania projektu.
