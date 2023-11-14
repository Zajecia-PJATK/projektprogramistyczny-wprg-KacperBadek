SELECT id_produkt, nazwa, cena, opis, zdjecie FROM produkty
SELECT id_kategoria, nazwa_kategoria FROM kategorie
SELECT rodzaj_klienta FROM uzytkownicy WHERE id_uzytkownik = :id
SELECT id_produkt, nazwa, cena, opis, zdjecie FROM produkty WHERE id_produkt = :id
SELECT hash_haslo, id_uzytkownik FROM uzytkownicy WHERE email = :email
SELECT email FROM uzytkownicy WHERE email = :email
SELECT imie, nazwisko, email FROM uzytkownicy WHERE id_uzytkownik = :id
SELECT stan_magazynu FROM produkty WHERE id_produkt = :id
SELECT AVG(liczba_gwiazdek) AS srednia FROM opinie WHERE id_produkt = :id
SELECT liczba_gwiazdek, opinia, id_uzytkownik, data_wystawienia_opinii FROM opinie WHERE id_produkt = :id
SELECT imie FROM uzytkownicy WHERE id_uzytkownik = :user
SELECT id_produkt, nazwa, cena, opis, zdjecie FROM produkty WHERE (nazwa LIKE :szukajParam OR opis LIKE :szukajParam OR cena LIKE :szukajParam) AND id_kategoria = :kategoria
SELECT id_produkt, nazwa, cena, opis, zdjecie FROM produkty WHERE nazwa LIKE :szukajParam OR opis LIKE :szukajParam OR cena LIKE :szukajParam
SELECT cena FROM produkty WHERE id_produkt = :produktId
SELECT id_adres FROM adres WHERE miasto = :miasto AND ulica = :ulica AND kod_pocztowy = :kod_pocztowy AND nr_domu = :nr_domu AND nr_mieszkania = :nr_mieszkania
SELECT * FROM kody_rabatowe
SELECT * FROM opinie
SELECT * FROM reklamacje
SELECT * FROM uzytkownicy
SELECT * FROM zamowienia
SELECT produkty.*, kategorie.nazwa_kategoria FROM kategorie INNER JOIN produkty ON kategorie.id_kategoria = produkty.id_kategoria ORDER BY produkty.id_produkt
SELECT * FROM produkty
SELECT id_kategoria, nazwa_kategoria FROM kategorie

INSERT INTO uzytkownicy (imie, nazwisko, email, hash_haslo, rodzaj_klienta) VALUES (:imie, :nazwisko, :email, :hash, 'klient')
INSERT INTO opinie (id_produkt, id_uzytkownik, opinia, liczba_gwiazdek, data_wystawienia_opinii) VALUES (:id, :user, :opinion, :stars, '$today')
INSERT INTO adres(miasto, ulica, kod_pocztowy, nr_domu, nr_mieszkania) VALUES(:miasto, :ulica, :kod_pocztowy, :nr_domu, :nr_mieszkania)
INSERT INTO zamowienia(id_adres, id_uzytkownik, typ_platnosci, zaplacona_suma, dane_kontaktowe, data_zamowienia) VALUES(:id_adres, :id_uzytkownik, :typ_platnosci, :zaplacona_suma, :dane_kontaktowe, '$today')
INSERT INTO kody_rabatowe(kod, obnizka_procent) VALUES(:kod, :obnizka)
INSERT INTO uzytkownicy (imie, nazwisko, email, hash_haslo, rodzaj_klienta) VALUES (:imie, :nazwisko, :email, :hash, :rodzaj)
INSERT INTO zamowienia_produkty(id_zamowienia, id_produkt, ilosc) VALUES(:id_zamowienia, :id_produkt, :ilosc)
INSERT INTO kategorie(nazwa_kategoria, id_rodzic_kategoria) VALUES(:nazwa, :rodzic)
INSERT INTO uzytkownicy (imie, nazwisko, email, hash_haslo, rodzaj_klienta) VALUES (:imie, :nazwisko, :email, :hash, :rodzaj)
INSERT INTO PRODUKTY(nazwa, cena, opis, zdjecie, stan_magazynu, id_kategoria) VALUES(:nazwa, :cena, :opis, :zdjecie, :stan, :kategoria)

UPDATE produkty SET stan_magazynu = :warehouseState WHERE id_produkt = :id_produkt
UPDATE kategorie SET nazwa_kategoria = :nazwa, id_rodzic_kategoria = :rodzic WHERE id_kategoria = :id
UPDATE kody_rabatowe SET kod = :kod, obnizka_procent = :obnizka WHERE id_kod = :id
UPDATE opinie SET opinia = :opinia, liczba_gwiazdek = :gwiazdki, data_wystawienia_opinii = :data WHERE id_opinia = :id
UPDATE uzytkownicy SET imie = :imie, nazwisko = :nazwisko, email = :email, rodzaj_klienta = :rodzaj WHERE id_uzytkownik = :id
UPDATE uzytkownicy SET imie = :imie, nazwisko = :nazwisko, email = :email, hash_haslo = :haslo , rodzaj_klienta = :rodzaj WHERE id_uzytkownik = :id
UPDATE zamowienia SET stan_zamowienia = :stan WHERE id_zamowienia = :id
UPDATE produkty SET nazwa = :nazwa, cena = :cena, opis = :opis, zdjecie = :zdjecie, stan_magazynu = :stan, id_kategoria = :kategoria WHERE id_produkt = :id

DELETE FROM kody_rabatowe WHERE id_kod = :id
DELETE FROM opinie WHERE id_opinia = :id
DELETE FROM zamowienia WHERE id_zamowienia = :id