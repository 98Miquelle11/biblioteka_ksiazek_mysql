USE biblioteka;

INSERT INTO status_egzemplarz (id_status_egzemplarz, nazwa) VALUES
(1, 'dostepny'),
(2, 'wypozyczony'),
(3, 'zarezerwowany');

INSERT INTO autor (imie, nazwisko, narodowosc) VALUES
('Boleslaw', 'Prus', 'polska'),
('Adam', 'Mickiewicz', 'polska'),
('Henryk', 'Sienkiewicz', 'polska'),
('Fiodor', 'Dostojewski', 'rosyjska'),
('Michail', 'Bulgakow', 'rosyjska'),
('J.R.R.', 'Tolkien', 'brytyjska'),
('Jane', 'Austen', 'brytyjska'),
('George', 'Orwell', 'brytyjska'),
('Witold', 'Gombrowicz', 'polska'),
('Stanislaw', 'Lem', 'polska'),
('Wladyslaw', 'Reymont', 'polska'),
('Stefan', 'Zeromski', 'polska'),
('Franz', 'Kafka', 'czeska'),
('Antoine', 'de Saint-Exupery', 'francuska'),
('Maria', 'Dabrowska', 'polska'),
('Eliza', 'Orzeszkowa', 'polska'),
('Juliusz', 'Slowacki', 'polska'),
('Aleksander', 'Fredro', 'polska'),
('Charles', 'Dickens', 'brytyjska'),
('Victor', 'Hugo', 'francuska');

INSERT INTO wydawnictwo (nazwa, siedziba) VALUES
('Wydawnictwo Literackie', 'Krakow'),
('Znak', 'Krakow'),
('PWN', 'Warszawa'),
('Czytelnik', 'Warszawa'),
('Agora', 'Warszawa');

INSERT INTO gatunek (nazwa) VALUES
('powiesc'),
('fantastyka'),
('lektura'),
('dramat'),
('science fiction'),
('historyczna'),
('obyczajowa'),
('satyra');

INSERT INTO ksiazka (tytul) VALUES
('Lalka'),
('Pan Tadeusz'),
('Quo Vadis'),
('Zbrodnia i kara'),
('Mistrz i Malgorzata'),
('Hobbit'),
('Duma i uprzedzenie'),
('Rok 1984'),
('Folwark zwierzecy'),
('Ferdydurke'),
('Solaris'),
('Chlopi'),
('Krzyzacy'),
('Przedwiosnie'),
('Proces'),
('Maly Ksiaze'),
('Noce i dnie'),
('Nad Niemnem'),
('Zemsta'),
('Nedznicy');

-- Powiązania książek z autorami
INSERT INTO autor_ksiazka (id_ksiazka, id_autor) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 8),
(10, 9),
(11, 10),
(12, 11),
(13, 3),
(14, 12),
(15, 13),
(16, 14),
(17, 15),
(18, 16),
(19, 18),
(20, 20);

-- Powiązania książek z gatunkami
INSERT INTO gatunek_ksiazka (id_ksiazka, id_gatunek) VALUES
(1, 1),
(1, 7),
(2, 3),
(3, 6),
(4, 1),
(5, 1),
(5, 2),
(6, 2),
(7, 7),
(8, 8),
(9, 8),
(10, 8),
(11, 5),
(12, 7),
(13, 6),
(14, 1),
(15, 1),
(16, 3),
(17, 7),
(18, 7),
(19, 4),
(20, 1);

INSERT INTO wydanie (id_ksiazka, id_wydawnictwo, isbn, rok_wydania, liczba_stron) VALUES
(1, 1, '9780000000001', 1890, 680),
(2, 3, '9780000000002', 1834, 340),
(3, 4, '9780000000003', 1896, 590),
(4, 2, '9780000000004', 1866, 520),
(5, 1, '9780000000005', 1967, 470),
(6, 2, '9780000000006', 1937, 310),
(7, 5, '9780000000007', 1813, 430),
(8, 3, '9780000000008', 1949, 330),
(9, 3, '9780000000009', 1945, 120),
(10, 1, '9780000000010', 1937, 290),
(11, 1, '9780000000011', 1961, 300),
(12, 4, '9780000000012', 1924, 900),
(13, 4, '9780000000013', 1900, 760),
(14, 2, '9780000000014', 1924, 320),
(15, 5, '9780000000015', 1925, 260),
(16, 5, '9780000000016', 1943, 100),
(17, 1, '9780000000017', 1934, 580),
(18, 4, '9780000000018', 1888, 620),
(19, 3, '9780000000019', 1834, 180),
(20, 2, '9780000000020', 1862, 1200);

INSERT INTO egzemplarz (id_wydanie, forma, cena_zakup, uwaga) VALUES
(1, 'fizyczna', 39.90, 'stan dobry'),
(2, 'fizyczna', 29.90, 'nowy egzemplarz'),
(3, 'fizyczna', 34.90, 'stan dobry'),
(4, 'ebook', 19.90, 'wersja elektroniczna'),
(5, 'fizyczna', 44.90, 'stan bardzo dobry'),
(6, 'audiobook', 24.90, 'wersja audio'),
(7, 'fizyczna', 32.90, 'stan dobry'),
(8, 'ebook', 18.90, 'wersja elektroniczna'),
(9, 'fizyczna', 20.00, 'krótka lektura'),
(10, 'fizyczna', 28.00, 'stan dobry'),
(11, 'fizyczna', 35.00, 'science fiction'),
(12, 'fizyczna', 55.00, 'wydanie zbiorcze'),
(13, 'fizyczna', 45.00, 'wydanie szkolne'),
(14, 'ebook', 17.00, 'wersja elektroniczna'),
(15, 'fizyczna', 25.00, 'stan dobry'),
(16, 'fizyczna', 21.00, 'lektura'),
(17, 'fizyczna', 38.00, 'stan dobry'),
(18, 'fizyczna', 36.00, 'stan dobry'),
(19, 'fizyczna', 22.00, 'dramat'),
(20, 'audiobook', 27.00, 'wersja audio');

/* Hasło testowe dla wszystkich kont: password
W aplikacji hasła powinny być generowane przez password_hash() / bcrypt. */
INSERT INTO czytelnik (
    imie,
    nazwisko,
    email,
    password_hash,
    rola,
    telefon,
    saldo
) VALUES
('Admin', 'Systemowy', 'admin@biblioteka.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'admin', '500100100', 0.00),
('Anna', 'Kowalska', 'anna.kowalska@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100101', 0.00),
('Jan', 'Nowak', 'jan.nowak@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100102', 0.00),
('Maria', 'Wisniewska', 'maria.wisniewska@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100103', 0.00),
('Piotr', 'Wojcik', 'piotr.wojcik@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100104', 0.00),
('Katarzyna', 'Kaminska', 'katarzyna.kaminska@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100105', 0.00),
('Tomasz', 'Lewandowski', 'tomasz.lewandowski@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100106', 0.00),
('Alicja', 'Zielinska', 'alicja.zielinska@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100107', 0.00),
('Pawel', 'Szymanski', 'pawel.szymanski@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100108', 0.00),
('Magdalena', 'Wozniak', 'magdalena.wozniak@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100109', 0.00),
('Michal', 'Dabrowski', 'michal.dabrowski@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100110', 0.00),
('Ewa', 'Kozlowska', 'ewa.kozlowska@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100111', 0.00),
('Robert', 'Mazur', 'robert.mazur@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100112', 0.00),
('Natalia', 'Krawczyk', 'natalia.krawczyk@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100113', 0.00),
('Krzysztof', 'Piotrowski', 'krzysztof.piotrowski@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100114', 0.00),
('Karolina', 'Grabowska', 'karolina.grabowska@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100115', 0.00),
('Lukasz', 'Pawlak', 'lukasz.pawlak@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100116', 0.00),
('Monika', 'Michalska', 'monika.michalska@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100117', 0.00),
('Damian', 'Nowicki', 'damian.nowicki@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100118', 0.00),
('Oliwia', 'Adamczyk', 'oliwia.adamczyk@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqxyYfxxHSI0QYe5p/y', 'user', '500100119', 0.00);

-- Triggery automatycznie zmienią status egzemplarzy 1-5 na "wypozyczony".
INSERT INTO wypozyczenie (
    id_egzemplarz,
    id_czytelnik,
    data_wypozyczenie,
    przewidywana_data_zwrot
) VALUES
(1, 2, '2026-01-10', '2026-01-24'),
(2, 3, '2026-01-11', '2026-01-25'),
(3, 4, '2026-01-12', '2026-01-26'),
(4, 5, '2026-01-13', '2026-01-27'),
(5, 6, '2026-01-14', '2026-01-28');

-- Triggery automatycznie zmienią status egzemplarzy 6-8 na "zarezerwowany".
INSERT INTO rezerwacja (
    id_egzemplarz,
    id_czytelnik,
    data_rezerwacja,
    status_rezerwacja
) VALUES
(6, 7, '2026-01-15', 'aktywna'),
(7, 8, '2026-01-15', 'aktywna'),
(8, 9, '2026-01-15', 'aktywna');

-- Triggery automatycznie zwiększą saldo czytelników.
INSERT INTO wplata (
    id_czytelnik,
    kwota,
    data_wplata,
    metoda_platnosc
) VALUES
(2, 20.00, '2026-01-16', 'karta'),
(3, 15.00, '2026-01-16', 'gotowka'),
(4, 30.00, '2026-01-17', 'przelew'),
(5, 10.00, '2026-01-17', 'karta'),
(6, 25.00, '2026-01-18', 'gotowka');