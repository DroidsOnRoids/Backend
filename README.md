To bardzo proste API zawiera 3 podstawowe funkcje: upload image, get images i remove image.

# Podstawowe informacje
URL:
```
https://serene-escarpment-58247.herokuapp.com
```

<br />

# Upload image
Tutaj uploadujemy obrazek na serwer. Jeśli podamy parametr `to_userId` to wyślemy obrazek tylko do konrketnego użytkownika. Jeśli nie podamy, obrazek otrzymają wszyscy.

### Zapytanie
Typ: **POST**
URL: `https://serene-escarpment-58247.herokuapp.com/upload`
Parametry (w ciele zapytania):
`file` - **(Obowiązkowy)** Obrazek, który wrzucamy.
`from_userId` - **(Obowiązkowy)** ID użytkownika, który wysłał dany obrazek
`to_userId` - _(Opcjonalny)_ ID użytkownika, do którego wysyłamy zdjęcie.
<br />
### Odpowiedź
W zależności od tego czy upload się udał, wystąpi albo success, albo error. Odpowiedź success:
```
{
  "Success": "Image uploaded correctly."
}
```

Odpowiedź error (przykładowa, kiedy nie podamy parametru `from_userId`:
```
{
  "error": "You didn't specify parameter from_userId."
}
```

<br />

# Get images
Dostajemy listę obrazków wrzuconych dla danego użytkownika. Jeśli nie podamy parametru `to_userId` w URL, to dostaniemy obrazki tylko te, które były wrzucane do wszystkich.

### Zapytanie

Typ: **GET**
URL: `https://serene-escarpment-58247.herokuapp.com/get/{to_userId}`
Parametry:
`to_userId` - _(Opcjonalny)_ To jedyny parametr, który znajduje się w URL a nie w ciele zapytania. Należy go dołączyć jak w przykładzie na dole.

Przykłady URL:
```
https://serene-escarpment-58247.herokuapp.com/get // images that were sent to all users
https://serene-escarpment-58247.herokuapp.com/get/11 // images for user with id 11
```
<br />
###Odpowiedź:

W odpowiedzi znajduje się tablica o nazwie `images`, która trzyma wszystkie obrazki dla wykonanego requestu. Jeden obrazek jest obiektem, który posiada:
`url` - Jest to pełny URL do obrazka
`file_name` - Nazwa pliku, która jest potrzebna do wywołania zapytania usunięcia obrazka.
`from_userId` - ID użytkownika, który wysłał ten obrazek
`to` - Tutaj w zależności od tego, czy obrazek został wysłany do konkretnego użytkownika czy do wszystkich, będzie albo ID użytkownika, albo 0, które oznacza, że zdjęcie zostało wysłane do wszystkich.

Przykład odpowiedzi:
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
