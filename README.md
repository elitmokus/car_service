
Laravel 11 keretrendszert használok (Inertia.js és Vue.js).

## Alkalmazás telepítése

#### Előfeltételek

- [Laravelt futtató környezet](https://laravel.com/docs/11.x/deployment) (pl.: XAMPP, [Herd](https://herd.laravel.com/windows), Laragon)
- NodeJs NPM
- Composer
- MySQL kapcsolat
    - adatbázis név: car_service
    - adatbázis hozzáférés: dbadmini / secret_password

#### Telepítés

Le kell futtatni az install.sh scriptet.


#### Megjegyzések:

- A feladat szerint minden futás elején importálni kell a JSON fájlokat, ha még nincs feltöltve. 
Igyekeztem kikerülni a Middleware használatát, és ServiceProvider-t használni helyette, DE ezzel a módszerrel nem lehet lefuttatni a migrate parancsot. 
Meg lehetett volna csinálni, hogy a ServiceProvider-ben adom meg a szerkezetét, és ott hozom létre a táblákat ha még nincs, de végül maradtam a külön migration fájloknál.
- A fent leírt módszer azt a problémát szüli, hogy figyelmetlen/rosszakaró programozó ki tudja kerülni a Middleware használatát.

- A leírás alapján az ÉN értelmezésemben ilyen felületrendszer alakult ki, ami meglehetősen ROSSZ. 
Az ügyfélhez tartozó autókat külön Page-en jeleníteném meg, ugyanígy az autóhoz tartozó szervizeket.
- A keresés eredményét a listában való szűréssel jelezném, nem pedig a kereső mezők alatt egy külön blokkban.
- A megjelenítés ÍGY meglehetősen nehéz feladat volt számomra, de sokat tanultam ebből is. 
