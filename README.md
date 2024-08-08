
# Robot harc

A robot harc kis web app doksija.
A feladatott Symfony 7.1 verzióban lett elkészítve.
A PHP verzió: 8.3.10
Adatbázisnak NoSql-t használtam, mivel azt gyorsan lehet használni, a leírásnak jelenleg meg felelt.

# RobotController web

## Végpontok

### 1. Robotok listázása

- **URL**: `/robot`
- **Metódus**: `GET`
- **Leírás**: Lekéri az összes aktív robotot és megjeleníti őket a felhasználói felületen.
- **Válasz**: HTML oldal a robotok listájával.

### 2. Robotok harca

- **URL**: `/robot/fight`
- **Metódus**: `POST`
- **Leírás**: Két robotot választ ki a harcra. A harc eredménye alapján megjeleníti a győztest és a két robotot.
- **Kérések**:
  - **Paraméterek**: `selected_robots` (tömb, két robot azonosítójával).
- **Válasz**: HTML oldal a harc eredményével.
- **Hibakezelés**:
  - Ha nem pontosan két robot van kiválasztva, hibaüzenetet küld.
  - Ha a robotok nem találhatók, hibaüzenetet küld.

### 3. Robot törlése

- **URL**: `/robot/delete/{id}`
- **Metódus**: `POST`
- **Leírás**: Törli a megadott azonosítójú robotot. A robotot nem törli fizikailag, csak `deleted_at` mezőjét állítja be (soft delete).
- **Kérések**:
  - **Paraméterek**: `id` (a törölni kívánt robot azonosítója).
- **Válasz**: Átirányít a robotok listájára, és sikeres törlés esetén flash üzenetet küld.
- **Hibakezelés**:
  - Ha a robot nem található, hibaüzenetet küld.

### 4. Új robot hozzáadása

- **URL**: `/robot/new`
- **Metódus**: `GET` és `POST`
- **Leírás**: Lehetővé teszi új robot hozzáadását. Az új robotot formon keresztül lehet bevinni.
- **Kérések**:
  - **GET**: Megjeleníti az űrlapot az új robot hozzáadásához.
  - **POST**: Az űrlap beküldése után menti az új robotot az adatbázisba.
- **Válasz**: HTML oldal az űrlap megjelenítésével, vagy sikeres hozzáadás után átirányít a robotok listájára.
- **Hibakezelés**:
  - Az űrlap validációs hibák esetén az űrlapot újra megjeleníti.

### 5. Robot módosítása

- **URL**: `/robot/edit/{id}`
- **Metódus**: `GET` és `POST`
- **Leírás**: Lehetővé teszi a meglévő robot adatainak módosítását.
- **Kérések**:
  - **GET**: Megjeleníti a formot a módosításhoz.
  - **POST**: Az űrlap beküldése után frissíti a robotot az adatbázisban.
- **Válasz**: HTML oldal az űrlappal, vagy sikeres módosítás után átirányít a robotok listájára.
- **Hibakezelés**:
  - Ha a robot nem található, hibaüzenetet küld.

## Funkciók

### `determineWinner(Robot $robot1, Robot $robot2): Robot`
Ez a privát metódus meghatározza, hogy a két robot közül melyik nyert. Az erő értéke (power) alapján dönt, és ha az erő egyenlő, az újabb robot nyer.

## Használat

1. **Listázás**: Navigálj a `/robot` URL-re, hogy megtekintsd az aktív robotok listáját.
2. **Harc**: POST kérés a `/robot/fight` URL-re, a kiválasztott robotok azonosítóival.
3. **Törlés**: POST kérés a `/robot/delete/{id}` URL-re, ahol `{id}` a törölni kívánt robot azonosítója.
4. **Új robot**: GET kérés a `/robot/new` URL-re az új robot hozzáadásához, és POST kérés az űrlap beküldésére.
5. **Módosítás**: GET kérés a `/robot/edit/{id}` URL-re a robot adatainak módosításához, és POST kérés az űrlap beküldésére.

A Controller minden végpontja tartalmaz hibakezelést és visszajelzést a felhasználó számára a műveletek sikerességéről vagy hibáiról.

# ApiRobotController

Az `ApiRobotController` egy Symfony API Controller, amely lehetővé teszi két robot közötti harc eredményének lekérését JSON formátumban.

## Végpontok

### 1. Robotok harca

- **URL**: `/api/robot/fight`
- **Metódus**: `GET`
- **Leírás**: Ez a végpont két robotot választ ki a harcra, és visszaadja a győztes robot adatait JSON formátumban.

#### Kérés

- **Paraméterek**:
  - `robot_id1` (kötelező): Az első robot azonosítója.
  - `robot_id2` (kötelező): A második robot azonosítója.

- **Példa kérés URL-re**: /api/robot/fight?robot_id1=1&robot_id2=2
#### Válasz

A válasz JSON formátumban tartalmazza a harc eredményét, beleértve a győztes robot adatait.

- **Sikeres válasz** (`200 OK`):

```json
{
    "winner": {
        "id": 1,
        "name": "Robot1",
        "power": 100
    }
}
```
## Hibás válaszok

- **Ha az azonosítók nem számok** (`400 Bad Request`):

  ```json
  {
      "error": "Két robot azonosítóját kell megadni, numerikus formátumban!"
  }
  ```
- **Ha az egyik vagy mindkét azonosító hiányzik** (`400 Bad Request`):
  ```json
  {
    "error": "Két robot azonosítóját kell megadni!"
  }
  ```
- **Ha a két azonosító megegyezik** (`400 Bad Request`):
  ```json
  {
    "error": "Két robot azonosítóját kell megadni!"
  }
  ```
- **Ha a robotok nem találhatók az adatbázisban, vagy törölve lettek** (`404 Not Found`):
  ```json
    {
    "error": "A kiválasztott robotok nem találhatók!"
    }
  ```
## Funkciók

## determineWinner(Robot $robot1, Robot $robot2): Robot
Ez a privát metódus meghatározza, hogy a két robot közül melyik nyer a harcban. Az erő (power) értéke alapján dönt, és visszaadja a győztest. Jelenlegi logika:
Ha az egyik robot ereje nagyobb, az nyer.
Ha az erők egyenlők, jelenleg nincs további döntés (ezért ebben az esetben további logikát kellene hozzáadni).


### Időbecslés
- Tervezés: 30 perc
- Megvalositás(becslés): 2 óra 
- Megvalositás(végső): 1 óra 40 perc
- Dokumentálás: 20 perc 

### Framework
Alapból a laravelt szeretem, mivel 5 éve abban dolgozok, hobbi projekteket is csinálom benne.
Symfony-t is nagyon szeretem, bár azt inkább komplex rendszerekhez ajánlatos. Mivel Symfony volt preferált így abban csináltam meg a teszt feladatott.