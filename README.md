# 🧠 Aplikacja Fiszkowa – Nauka z użyciem mnemotechniki

## 📌 Opis przeznaczenia aplikacji

Aplikacja internetowa wspomagająca naukę słownictwa i innych informacji (np. dat, pojęć) przy pomocy systemu fiszek oraz prostych testów mnemotechnicznych. Użytkownik może tworzyć własne zestawy fiszek, zapamiętywać je i testować swoją wiedzę na różne sposoby.

## 👥 Typy użytkowników i uwierzytelnianie

Aplikacja obsługuje 3 poziomy uwierzytelniania:
- **Gość** – może przeglądać publiczne zestawy fiszek;
- **Zarejestrowany użytkownik** – może tworzyć własne zestawy, korzystać z testów i przeglądać historię wyników;
- **Administrator** – może zarządzać użytkownikami (edycja danych, aktywacja/dezaktywacja kont) i przeglądać wszystkie zestawy.

## ⚙️ Wymagania sprzętowe i programowe

- Serwer Linux z Apache
- PHP 8.0+
- MySQL 5.7+
- Przeglądarka obsługująca HTML5

## 🔒 Bezpieczeństwo

- Hasła przechowywane w postaci haszy (password_hash)
- Sesje zabezpieczone przed dostępem gościa do panelu użytkownika
- Trójstopniowe role z podziałem uprawnień

## 💾 Instalacja

1. Sklonuj repozytorium na serwer z Apache.
2. W katalogu głównym ustaw połączenie z bazą danych w pliku `includes/db.php`.
3. Zaimportuj strukturę bazy danych z pliku `database.sql`.
4. Uruchom aplikację przez przeglądarkę pod adresem np. `localhost/fiszki`.

## 🗺️ Schemat stron

- `/register.php` – rejestracja
- `/login.php` – logowanie
- `/dashboard.php` – panel użytkownika
- `/sets.php` – lista zestawów
- `/flashcards.php` – zarządzanie fiszkami
- `/tests/run.php` – test sekwencji
- `/tests/input.php` – test otwartej odpowiedzi
- `/tests/results.php` – historia wyników
- `/admin/users.php` – panel administracyjny

## 🧩 Schemat bazy danych

- **users** (id, first_name, last_name, email, password_hash, role, active)
- **sets** (id, user_id, title, description, is_public)
- **flashcards** (id, set_id, front_text, back_text, position)
- **tests** (id, user_id, set_id, test_type, created_at)
- **results** (id, test_id, score, total, completed_at)

## 👨‍🏫 Instrukcja użytkownika

1. Zarejestruj się lub zaloguj.
2. Utwórz własny zestaw fiszek (np. "Angielski: Podstawy").
3. Dodaj do niego karty z hasłem (np. "dog") i tłumaczeniem (np. "pies").
4. Uruchom test:
   - test sekwencji – zapamiętywanie kolejności
   - test otwarty – wpisywanie tłumaczeń
5. Przeglądaj wyniki w zakładce `Twoje testy`.

## 👨‍🔧 Autorzy
- Imię Nazwisko (Student Wydziału Informatyki, UŁ)
- Rok: 2025
