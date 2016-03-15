To bardzo proste API zawiera 3 podstawowe funkcje: upload image, get images i remove image.

# Podstawowe informacje
URL:
```
https://serene-escarpment-58247.herokuapp.com
```

<br />

# Upload image
Tutaj uploadujemy obrazek na serwer. Jeśli podamy parametr `to_userId` to wyślemy obrazek tylko do konkretnego użytkownika. Jeśli nie podamy to obrazek otrzymają wszyscy.

### Zapytanie

Typ: **POST**<br />
URL: `https://serene-escarpment-58247.herokuapp.com/upload`<br />
Parametry (w ciele zapytania):<br />
`file` - **(Obowiązkowy)** Obrazek, który wrzucamy.<br />
`from_userId` - **(Obowiązkowy)** ID użytkownika, który wysłał dany obrazek<br />
`to_userId` - _(Opcjonalny)_ ID użytkownika, do którego wysyłamy zdjęcie.<br />
<br />
### Odpowiedź

W zależności od tego czy upload się udał, wystąpi albo success, albo error. Odpowiedź success:<br />
```
{
  "Success": "Image uploaded correctly."
}
```
<br />
Odpowiedź error (przykładowa, kiedy nie podamy parametru `from_userId`:<br />
```
{
  "error": "You didn't specify parameter from_userId."
}
```

<br />

# Get images
Dostajemy listę obrazków wrzuconych dla danego użytkownika. Jeśli nie podamy parametru `to_userId` w URL, to dostaniemy obrazki tylko te, które były wrzucane do wszystkich.

### Zapytanie

Typ: **GET**<br />
URL: `https://serene-escarpment-58247.herokuapp.com/get/{to_userId}`<br />
Parametry:<br />
`to_userId` - _(Opcjonalny)_ To jedyny parametr, który znajduje się w URL a nie w ciele zapytania. Należy go dołączyć jak w przykładzie na dole.
<br /><br />
Przykłady URL:<br />
```
https://serene-escarpment-58247.herokuapp.com/get // images that were sent to all users
https://serene-escarpment-58247.herokuapp.com/get/11 // images for user with id 11
```

### Odpowiedź

W odpowiedzi znajduje się tablica o nazwie `images`, która trzyma wszystkie obrazki dla wykonanego requestu. Jeden obrazek jest obiektem, który posiada:<br />
`url` - Jest to pełny URL do obrazka<br />
`file_name` - Nazwa pliku, która jest potrzebna do wywołania zapytania usunięcia obrazka.<br />
`from_userId` - ID użytkownika, który wysłał ten obrazek<br />
`to` - Tutaj w zależności od tego, czy obrazek został wysłany do konkretnego użytkownika czy do wszystkich, będzie albo ID użytkownika, albo 0, które oznacza, że zdjęcie zostało wysłane do wszystkich.<br />
`date` - Data kiedy obrazek został przesłany (a w zasadzie zapisany na serwerze).<br />

Przykład odpowiedzi:<br />
```
{
  "images": [
    {
      "url": "https://serene-escarpment-58247.herokuapp.com/images/all/1_2016.03.15_13.35.43_a5ebcb05b8233c8ada8425689055d29564f47bf6.jpg",
      "file_name": "1_2016.03.15_13.35.43_a5ebcb05b8233c8ada8425689055d29564f47bf6.jpg",
      "from_userId": "1",
      "to": 0,
      "date": "2016-03-15 13:35:43"
    }
  ]
}
```
<br />

# Remove image
Tutaj możemy usunąć obrazek na podstawie nazwy obrazka oraz użytkownika, do którego dany obrazek został wysłany.

### Zapytanie

Typ: **POST**<br />
URL: `https://serene-escarpment-58247.herokuapp.com/remove`<br />
Parametry:<br />
`file_name` - **(Obowiązkowy)** Obowiązkowy parametr<br />
`to_userId` - _(Opcjonalny)_ Jeżeli podamy tu ID większe od 0, to oznacza to, że chcemy usunąć obrazek dla danego użytkownika (o ID `to_userId` o danej nazwie (`file_name`).
<br />
### Odpowiedź

W zależności od tego czy upload się udał, wystąpi albo success, albo error. Odpowiedź success:
```
{
  "Success": "Image removed correctly"
}
```

Odpowiedź error (przykładowa, kiedy nie znajdzie nam takiego obrazka dla podanych parametrów):
```
{
  "error": "There is no file with given name for given user."
}
```
