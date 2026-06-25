CREATE DATABASE IF NOT EXISTS biblioteka
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE biblioteka;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS log;
DROP TABLE IF EXISTS rezerwacja;
DROP TABLE IF EXISTS wplata;
DROP TABLE IF EXISTS wypozyczenie;
DROP TABLE IF EXISTS egzemplarz;
DROP TABLE IF EXISTS status_egzemplarz;
DROP TABLE IF EXISTS wydanie;
DROP TABLE IF EXISTS gatunek_ksiazka;
DROP TABLE IF EXISTS autor_ksiazka;
DROP TABLE IF EXISTS ksiazka;
DROP TABLE IF EXISTS gatunek;
DROP TABLE IF EXISTS wydawnictwo;
DROP TABLE IF EXISTS czytelnik;
DROP TABLE IF EXISTS autor;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE autor (
    id_autor INT AUTO_INCREMENT PRIMARY KEY,
    imie VARCHAR(50) NOT NULL,
    nazwisko VARCHAR(50) NOT NULL,
    narodowosc VARCHAR(50)
);

CREATE TABLE wydawnictwo (
    id_wydawnictwo INT AUTO_INCREMENT PRIMARY KEY,
    nazwa VARCHAR(100) NOT NULL UNIQUE,
    siedziba VARCHAR(100)
);

CREATE TABLE gatunek (
    id_gatunek INT AUTO_INCREMENT PRIMARY KEY,
    nazwa VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE ksiazka (
    id_ksiazka INT AUTO_INCREMENT PRIMARY KEY,
    tytul VARCHAR(255) NOT NULL
);

-- Tabela łącząca
CREATE TABLE autor_ksiazka (
    id_ksiazka INT NOT NULL,
    id_autor INT NOT NULL,
    PRIMARY KEY (id_ksiazka, id_autor),
    FOREIGN KEY (id_ksiazka) REFERENCES ksiazka(id_ksiazka) ON DELETE CASCADE,
    FOREIGN KEY (id_autor) REFERENCES autor(id_autor) ON DELETE CASCADE
);

-- Tabela łącząca
CREATE TABLE gatunek_ksiazka (
    id_ksiazka INT NOT NULL,
    id_gatunek INT NOT NULL,
    PRIMARY KEY (id_ksiazka, id_gatunek),
    FOREIGN KEY (id_ksiazka) REFERENCES ksiazka(id_ksiazka) ON DELETE CASCADE,
    FOREIGN KEY (id_gatunek) REFERENCES gatunek(id_gatunek) ON DELETE CASCADE
);

CREATE TABLE wydanie (
    id_wydanie INT AUTO_INCREMENT PRIMARY KEY,
    id_ksiazka INT NOT NULL,
    id_wydawnictwo INT,
    isbn VARCHAR(13) UNIQUE,
    rok_wydania INT,
    liczba_stron INT,
    FOREIGN KEY (id_ksiazka) REFERENCES ksiazka(id_ksiazka) ON DELETE CASCADE,
    FOREIGN KEY (id_wydawnictwo) REFERENCES wydawnictwo(id_wydawnictwo) ON DELETE SET NULL,
    CHECK (rok_wydania IS NULL OR rok_wydania BETWEEN 1000 AND 2100),
    CHECK (liczba_stron IS NULL OR liczba_stron > 0)
);

-- Tabela słownikowa
CREATE TABLE status_egzemplarz (
    id_status_egzemplarz INT AUTO_INCREMENT PRIMARY KEY,
    nazwa VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE egzemplarz (
    id_egzemplarz INT AUTO_INCREMENT PRIMARY KEY,
    id_wydanie INT NOT NULL,
    forma ENUM('fizyczna', 'ebook', 'audiobook') NOT NULL,
    id_status_egzemplarz INT NOT NULL DEFAULT 1,
    cena_zakup DECIMAL(6, 2),
    uwaga VARCHAR(255),
    FOREIGN KEY (id_wydanie) REFERENCES wydanie(id_wydanie) ON DELETE CASCADE,
    FOREIGN KEY (id_status_egzemplarz) REFERENCES status_egzemplarz(id_status_egzemplarz) ON DELETE RESTRICT,
    CHECK (cena_zakup IS NULL OR cena_zakup >= 0)
);

-- Ta tabela obsługuje także konta użytkowników, logowanie i role user/admin.
CREATE TABLE czytelnik (
    id_czytelnik INT AUTO_INCREMENT PRIMARY KEY,
    imie VARCHAR(50) NOT NULL,
    nazwisko VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rola ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    telefon VARCHAR(15),
    saldo DECIMAL(6, 2) NOT NULL DEFAULT 0.00,
    nieudane_logowania TINYINT UNSIGNED NOT NULL DEFAULT 0,
    blokada_do DATETIME NULL,
    data_rejestracja DATE DEFAULT (CURRENT_DATE)
);

CREATE TABLE wypozyczenie (
    id_wypozyczenie INT AUTO_INCREMENT PRIMARY KEY,
    id_egzemplarz INT NOT NULL,
    id_czytelnik INT NOT NULL,
    data_wypozyczenie DATE DEFAULT (CURRENT_DATE),
    przewidywana_data_zwrot DATE NOT NULL,
    rzeczywista_data_zwrot DATE DEFAULT NULL,
    FOREIGN KEY (id_egzemplarz) REFERENCES egzemplarz(id_egzemplarz) ON DELETE CASCADE,
    FOREIGN KEY (id_czytelnik) REFERENCES czytelnik(id_czytelnik) ON DELETE CASCADE,
    CHECK (rzeczywista_data_zwrot IS NULL OR rzeczywista_data_zwrot >= data_wypozyczenie)
);

CREATE TABLE wplata (
    id_wplata INT AUTO_INCREMENT PRIMARY KEY,
    id_czytelnik INT NOT NULL,
    kwota DECIMAL(6, 2) NOT NULL,
    data_wplata DATE DEFAULT (CURRENT_DATE),
    metoda_platnosc ENUM('gotowka', 'karta', 'przelew') NOT NULL,
    FOREIGN KEY (id_czytelnik) REFERENCES czytelnik(id_czytelnik) ON DELETE CASCADE,
    CHECK (kwota > 0)
);

CREATE TABLE rezerwacja (
    id_rezerwacja INT AUTO_INCREMENT PRIMARY KEY,
    id_egzemplarz INT NOT NULL,
    id_czytelnik INT NOT NULL,
    data_rezerwacja DATE DEFAULT (CURRENT_DATE),
    status_rezerwacja ENUM('aktywna', 'zrealizowana', 'anulowana') DEFAULT 'aktywna',
    FOREIGN KEY (id_egzemplarz) REFERENCES egzemplarz(id_egzemplarz) ON DELETE CASCADE,
    FOREIGN KEY (id_czytelnik) REFERENCES czytelnik(id_czytelnik) ON DELETE CASCADE
);

CREATE TABLE log (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_egzemplarz INT NOT NULL,
    id_stary_status INT,
    id_nowy_status INT NOT NULL,
    data_modyfikacja TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_egzemplarz) REFERENCES egzemplarz(id_egzemplarz) ON DELETE CASCADE,
    FOREIGN KEY (id_stary_status) REFERENCES status_egzemplarz(id_status_egzemplarz) ON DELETE SET NULL,
    FOREIGN KEY (id_nowy_status) REFERENCES status_egzemplarz(id_status_egzemplarz) ON DELETE RESTRICT
);

-- Indeksy wymagane dla wyszukiwania i sortowania
CREATE INDEX idx_autor_nazwisko ON autor(nazwisko);
CREATE INDEX idx_ksiazka_tytul ON ksiazka(tytul);
CREATE INDEX idx_wydawnictwo_nazwa ON wydawnictwo(nazwa);
CREATE INDEX idx_wydanie_rok ON wydanie(rok_wydania);
CREATE INDEX idx_egzemplarz_status ON egzemplarz(id_status_egzemplarz);
CREATE INDEX idx_egzemplarz_forma ON egzemplarz(forma);
CREATE INDEX idx_czytelnik_nazwisko ON czytelnik(nazwisko);
CREATE INDEX idx_wypozyczenie_data ON wypozyczenie(data_wypozyczenie);
CREATE INDEX idx_rezerwacja_status ON rezerwacja(status_rezerwacja);

DELIMITER $$

-- Walidacja przed wypożyczeniem
CREATE TRIGGER przed_dodaniem_wypozyczenie_walidacja
BEFORE INSERT ON wypozyczenie
FOR EACH ROW
BEGIN
    DECLARE aktualny_status INT;
    DECLARE obecne_saldo DECIMAL(6, 2);

    SELECT id_status_egzemplarz
    INTO aktualny_status
    FROM egzemplarz
    WHERE id_egzemplarz = NEW.id_egzemplarz;

    SELECT saldo
    INTO obecne_saldo
    FROM czytelnik
    WHERE id_czytelnik = NEW.id_czytelnik;

    IF aktualny_status <> 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Blad: Ten egzemplarz nie jest dostepny.';
    END IF;

    IF obecne_saldo < -10.00 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Blad: Czytelnik ma zablokowane konto z powodu zadluzenia.';
    END IF;

    IF NEW.przewidywana_data_zwrot <= NEW.data_wypozyczenie THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Blad: Data zwrotu musi byc pozniejsza niz data wypozyczenia.';
    END IF;
END$$

-- Po wypożyczeniu egzemplarz dostaje status "wypozyczony"
CREATE TRIGGER po_dodaniu_wypozyczenie
AFTER INSERT ON wypozyczenie
FOR EACH ROW
BEGIN
    UPDATE egzemplarz
    SET id_status_egzemplarz = 2
    WHERE id_egzemplarz = NEW.id_egzemplarz;
END$$

-- Po zwrocie egzemplarz wraca do statusu "dostepny", a kara nalicza się automatycznie
CREATE TRIGGER po_aktualizacji_wypozyczenie
AFTER UPDATE ON wypozyczenie
FOR EACH ROW
BEGIN
    DECLARE dni_spoznienia INT;
    DECLARE kwota_kara DECIMAL(6, 2);

    IF NEW.rzeczywista_data_zwrot IS NOT NULL
       AND OLD.rzeczywista_data_zwrot IS NULL THEN

        IF NEW.rzeczywista_data_zwrot > NEW.przewidywana_data_zwrot THEN
            SET dni_spoznienia = DATEDIFF(NEW.rzeczywista_data_zwrot, NEW.przewidywana_data_zwrot);
            SET kwota_kara = dni_spoznienia * 0.20;

            UPDATE czytelnik
            SET saldo = saldo - kwota_kara
            WHERE id_czytelnik = NEW.id_czytelnik;
        END IF;

        UPDATE egzemplarz
        SET id_status_egzemplarz = 1
        WHERE id_egzemplarz = NEW.id_egzemplarz;
    END IF;
END$$

-- Walidacja przed rezerwacją
CREATE TRIGGER przed_dodaniem_rezerwacja_walidacja
BEFORE INSERT ON rezerwacja
FOR EACH ROW
BEGIN
    DECLARE aktualny_status INT;

    SELECT id_status_egzemplarz
    INTO aktualny_status
    FROM egzemplarz
    WHERE id_egzemplarz = NEW.id_egzemplarz;

    IF aktualny_status <> 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Blad: Mozna rezerwowac tylko dostepny egzemplarz.';
    END IF;
END$$

-- Po rezerwacji egzemplarz dostaje status "zarezerwowany"
CREATE TRIGGER po_dodaniu_rezerwacja
AFTER INSERT ON rezerwacja
FOR EACH ROW
BEGIN
    IF NEW.status_rezerwacja = 'aktywna' THEN
        UPDATE egzemplarz
        SET id_status_egzemplarz = 3
        WHERE id_egzemplarz = NEW.id_egzemplarz;
    END IF;
END$$

-- Po wpłacie zwiększa się saldo czytelnika
CREATE TRIGGER po_dodaniu_wplata
AFTER INSERT ON wplata
FOR EACH ROW
BEGIN
    UPDATE czytelnik
    SET saldo = saldo + NEW.kwota
    WHERE id_czytelnik = NEW.id_czytelnik;
END$$

-- Logowanie dodania egzemplarza
CREATE TRIGGER po_dodaniu_egzemplarz
AFTER INSERT ON egzemplarz
FOR EACH ROW
BEGIN
    INSERT INTO log (id_egzemplarz, id_stary_status, id_nowy_status)
    VALUES (NEW.id_egzemplarz, NULL, NEW.id_status_egzemplarz);
END$$

-- Logowanie zmiany statusu egzemplarza
CREATE TRIGGER po_zmianie_status_egzemplarz
AFTER UPDATE ON egzemplarz
FOR EACH ROW
BEGIN
    IF OLD.id_status_egzemplarz <> NEW.id_status_egzemplarz THEN
        INSERT INTO log (id_egzemplarz, id_stary_status, id_nowy_status)
        VALUES (NEW.id_egzemplarz, OLD.id_status_egzemplarz, NEW.id_status_egzemplarz);
    END IF;
END$$

DELIMITER ;