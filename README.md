# SongDraft – strona promocyjna + panel admina

## Technologie
- PHP (frontend + zaplecze)
- HTML5 / CSS3 / JavaScript
- MySQL (do zarządzania np. przez phpMyAdmin)

## Instalacja
1. Skopiuj pliki na serwer PHP (np. Apache + PHP 8+).
2. Utwórz bazę i tabele, importując `database.sql` przez phpMyAdmin.
3. W razie potrzeby zmień dane dostępowe DB w `includes/config.php`.
4. Uruchom stronę `index.php`.

## Panel administracyjny
- URL: `/admin/login.php`
- Domyślne dane: `admin` / `songdraft123`

W panelu można:
- zmienić nazwę i treści główne strony,
- podejrzeć statystyki wizyt,
- przeglądać ostatnie zapytania z formularza.
