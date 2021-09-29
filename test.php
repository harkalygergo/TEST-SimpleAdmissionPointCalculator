<?php
include_once('index.php');
include('homework_input.php');
echo '<pre>';
$EgyszerusitettPontszamitoKalkulator = new EgyszerusitettPontszamitoKalkulator();
// output: 470 (370 alappont + 100 többletpont)
var_dump( $EgyszerusitettPontszamitoKalkulator->calculateResult( $exampleData ) );
// output: 476 (376 alappont + 100 többletpont)
var_dump( $EgyszerusitettPontszamitoKalkulator->calculateResult( $exampleData1 ) );
// output: hiba, nem lehetséges a pontszámítás a kötelező érettségi tárgyak hiánya miatt
var_dump( $EgyszerusitettPontszamitoKalkulator->calculateResult( $exampleData2 ) );
// output: hiba, nem lehetséges a pontszámítás a magyar nyelv és irodalom tárgyból elért 20% alatti eredmény miatt
var_dump( $EgyszerusitettPontszamitoKalkulator->calculateResult( $exampleData3 ) );
echo '</pre>';
