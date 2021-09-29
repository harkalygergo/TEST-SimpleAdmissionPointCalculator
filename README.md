# Oktatási Hivatal Házi feladat 2021.09.27.

## Usage

Create a new object from EgyszerusitettPontszamitoKalkulator class, for example: `new EgyszerusitettPontszamitoKalkulator();`, and call `calculateResult()` function with input array parameter, like: `(new EgyszerusitettPontszamitoKalkulator())->calculateResult( $exampleData );`

Output will be the result with points or an error string.

## Test

Attached `homework_input.php` can be used with `test.php` and output should be:

```
int(470)
int(476)
string(39) "nincs kötelező tárgyból érettségi"
string(64) "magyar nyelv és irodalom tárgyból elért 20% alatti eredmény"
```