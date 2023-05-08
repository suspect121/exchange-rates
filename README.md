<h1 align="center">Exchange Rates</h1>
<p align="center"><strong>Kursy walut w relacji do PLN</strong></p>

Exchange Rates jest projektem, oferującym tabelę kursów najważniejszych walut w relacji do PLN. Wszystkie dane uzyskiwane są z API NBP, tabeli A.

## Podstawowe funkcjonalności
- Uzyskiwanie i prezentowanie kursów walut z dnia dzisiejszego
- Uzyskiwanie i prezentowanie archiwalnych kursów walut z dnia wybranego przez użytkownika
- Gromadzenie już uzyskanych danych w celu ograniczenia wykorzystania API NBP

## Założenia techniczne
- Wykorzystanie frameworka Symfony 6.2, bazy danych MySQL i PHP 8.2
- Minimalizacja ilości zapytań do bazy danych
- Minimalizacja ilości żądań do API NBP
- Walidacja danych wprowadzonych przez użytkownika
- Walidacja danych, które zostały uzyskane z API NBP
- Podział gromadzonych danych na dwie tabele
  - currency - tabela przechowująca kody i pełne nazwy walut
  - currency_rate - tabela przechowująca kursy walut wraz z datą ich opublikowania
- Wykorzystanie UUID jako klucza podstawowego tabel
