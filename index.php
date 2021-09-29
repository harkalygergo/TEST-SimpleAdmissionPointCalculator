<?php

class EgyszerusitettPontszamitoKalkulator
{
	const KOTELEZO_TARGYAK = ['magyar nyelv és irodalom', 'történelem', 'matematika'];
	const SUBJECT_LEVELS = ['közép', 'emelt'];
	const NYELVVIZSGA_B2 = 28;
	const NYELVVIZSGA_C1 = 40;
	const EMELT_SZINT = 50;
	const TOBBLETPONT_MAXIMUM = 100;

	private $szakok;
	private $erettsegi_eredmeny;

	public function __construct()
	{
		$this->szakok = [
			'Programtervező informatikus'=> [
				'kotelezo' => [ 'name' => 'matematika', 'level' => ['közép', 'emelt'] ],
				'valaszthato' => ['biológia', 'fizika', 'informatika', 'kémia']
			],
			'Anglisztika'=> [
				'kotelezo' => [ 'name' => 'angol', 'level' => ['emelt'] ],
				'valaszthato' => ['francia', 'német', 'olasz', 'orosz', 'spanyol', 'történelem']
			]
		];
	}

	public function calculateResult( $data )
	{
		$this->erettsegi_eredmeny = $data;

		if( !$this->testKotelezoErettsegiTantargyMegvan() )
		{
			return 'nincs kötelező tárgyból érettségi';
		}

		if( !$this->testValaszthatoTantargyMegvan() )
		{
			return 'nincs meg a választható tárgy';
		}

		if( $this->testKotelezoErettsegiTantargySzazalek() !== NULL )
		{
			return $this->testKotelezoErettsegiTantargySzazalek();
		}

		//$this->showConstant();

		echo $this->calculatePontszam();
		return 'lefutott';
	}

	protected function calculatePontszam()
	{
		$sum = 0;
		foreach( $this->erettsegi_eredmeny['erettsegi-eredmenyek'] as $erettsegi_eredmenyek )
		{
			$sum += (int)$erettsegi_eredmenyek['eredmeny'];
		}
		return $sum*2;
	}

	protected function testKotelezoErettsegiTantargySzazalek()
	{
		foreach( $this->erettsegi_eredmeny['erettsegi-eredmenyek'] as $erettsegi_eredmenyek )
		{
			if( (int)$erettsegi_eredmenyek['eredmeny'] < 20 )
			{
				return $erettsegi_eredmenyek['nev'].' tárgyból elért 20% alatti eredmény';
			}
		}
	}

	protected function testKotelezoErettsegiTantargyMegvan()
	{
		$maxCountRequiredSubjects = count( self::KOTELEZO_TARGYAK );
		$RequiredSubjectCounter = 0;
		foreach( $this->erettsegi_eredmeny['erettsegi-eredmenyek'] as $erettsegi_eredmenyek )
		{
			if( in_array( $erettsegi_eredmenyek['nev'], self::KOTELEZO_TARGYAK ) )
			{
				$RequiredSubjectCounter++;
			}
		}
		if( $maxCountRequiredSubjects<=$RequiredSubjectCounter )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	protected function testValaszthatoTantargyMegvan()
	{
		$OptinalSubjectFound = false;
		foreach( $this->erettsegi_eredmeny['erettsegi-eredmenyek'] as $erettsegi_eredmenyek )
		{
			if( in_array( $erettsegi_eredmenyek['nev'], $this->szakok[ $this->erettsegi_eredmeny['valasztott-szak']['szak'] ] ['valaszthato'] ) )
			{
				$OptinalSubjectFound = true;
			}
		}
		if( !$OptinalSubjectFound )
		{
			return false;
		}
		return true;
	}

	protected function showConstant()
	{
		foreach( self::KOTELEZO_TARGYAK as $k )
		{
			echo "$k\n";
		}
	}
}


// output: 470 (370 alappont + 100 többletpont)
$exampleData0 = [
	'valasztott-szak' => [
		'egyetem' => 'ELTE',
		'kar' => 'IK',
		'szak' => 'Programtervező informatikus',
	],
	'erettsegi-eredmenyek' => [
		[
			'nev' => 'magyar nyelv és irodalom',
			'tipus' => 'közép',
			'eredmeny' => '70%',
		],
		[
			'nev' => 'történelem',
			'tipus' => 'közép',
			'eredmeny' => '80%',
		],
		[
			'nev' => 'matematika',
			'tipus' => 'emelt',
			'eredmeny' => '90%',
		],
		[
			'nev' => 'angol nyelv',
			'tipus' => 'közép',
			'eredmeny' => '94%',
		],
		[
			'nev' => 'informatika',
			'tipus' => 'közép',
			'eredmeny' => '95%',
		],
	],
	'tobbletpontok' => [
		[
			'kategoria' => 'Nyelvvizsga',
			'tipus' => 'B2',
			'nyelv' => 'angol',
		],
		[
			'kategoria' => 'Nyelvvizsga',
			'tipus' => 'C1',
			'nyelv' => 'német',
		],
	],
];

// output: 476 (376 alappont + 100 többletpont)
$exampleData1 = [
	'valasztott-szak' => [
		'egyetem' => 'ELTE',
		'kar' => 'IK',
		'szak' => 'Programtervező informatikus',
	],
	'erettsegi-eredmenyek' => [
		[
			'nev' => 'magyar nyelv és irodalom',
			'tipus' => 'közép',
			'eredmeny' => '70%',
		],
		[
			'nev' => 'történelem',
			'tipus' => 'közép',
			'eredmeny' => '80%',
		],
		[
			'nev' => 'matematika',
			'tipus' => 'emelt',
			'eredmeny' => '90%',
		],
		[
			'nev' => 'angol nyelv',
			'tipus' => 'közép',
			'eredmeny' => '94%',
		],
		[
			'nev' => 'informatika',
			'tipus' => 'közép',
			'eredmeny' => '95%',
		],
		[
			'nev' => 'fizika',
			'tipus' => 'közép',
			'eredmeny' => '98%',
		],
	],
	'tobbletpontok' => [
		[
			'kategoria' => 'Nyelvvizsga',
			'tipus' => 'B2',
			'nyelv' => 'angol',
		],
		[
			'kategoria' => 'Nyelvvizsga',
			'tipus' => 'C1',
			'nyelv' => 'német',
		],
	],
];

// output: hiba, nem lehetséges a pontszámítás a kötelező érettségi tárgyak hiánya miatt
$exampleData2 = [
	'valasztott-szak' => [
		'egyetem' => 'ELTE',
		'kar' => 'IK',
		'szak' => 'Programtervező informatikus',
	],
	'erettsegi-eredmenyek' => [
		[
			'nev' => 'matematika',
			'tipus' => 'emelt',
			'eredmeny' => '90%',
		],
		[
			'nev' => 'angol nyelv',
			'tipus' => 'közép',
			'eredmeny' => '94%',
		],
		[
			'nev' => 'informatika',
			'tipus' => 'közép',
			'eredmeny' => '95%',
		],
	],
	'tobbletpontok' => [
		[
			'kategoria' => 'Nyelvvizsga',
			'tipus' => 'B2',
			'nyelv' => 'angol',
		],
		[
			'kategoria' => 'Nyelvvizsga',
			'tipus' => 'C1',
			'nyelv' => 'német',
		],
	],
];

// output: hiba, nem lehetséges a pontszámítás a magyar nyelv és irodalom tárgyból elért 20% alatti eredmény miatt
$exampleData3 = [
	'valasztott-szak' => [
		'egyetem' => 'ELTE',
		'kar' => 'IK',
		'szak' => 'Programtervező informatikus',
	],
	'erettsegi-eredmenyek' => [
		[
			'nev' => 'magyar nyelv és irodalom',
			'tipus' => 'közép',
			'eredmeny' => '15%',
		],
		[
			'nev' => 'történelem',
			'tipus' => 'közép',
			'eredmeny' => '80%',
		],
		[
			'nev' => 'matematika',
			'tipus' => 'emelt',
			'eredmeny' => '90%',
		],
		[
			'nev' => 'angol nyelv',
			'tipus' => 'közép',
			'eredmeny' => '94%',
		],
		[
			'nev' => 'informatika',
			'tipus' => 'közép',
			'eredmeny' => '95%',
		],
	],
	'tobbletpontok' => [
		[
			'kategoria' => 'Nyelvvizsga',
			'tipus' => 'B2',
			'nyelv' => 'angol',
		],
		[
			'kategoria' => 'Nyelvvizsga',
			'tipus' => 'C1',
			'nyelv' => 'német',
		],
	],
];

echo '<pre>';
$EgyszerusitettPontszamitoKalkulator = new EgyszerusitettPontszamitoKalkulator();
// output: 470 (370 alappont + 100 többletpont)
var_dump( $EgyszerusitettPontszamitoKalkulator->calculateResult( $exampleData0 ) );
// output: 476 (376 alappont + 100 többletpont)
var_dump( $EgyszerusitettPontszamitoKalkulator->calculateResult( $exampleData1 ) );
// output: hiba, nem lehetséges a pontszámítás a kötelező érettségi tárgyak hiánya miatt
var_dump( $EgyszerusitettPontszamitoKalkulator->calculateResult( $exampleData2 ) );
// output: hiba, nem lehetséges a pontszámítás a magyar nyelv és irodalom tárgyból elért 20% alatti eredmény miatt
var_dump( $EgyszerusitettPontszamitoKalkulator->calculateResult( $exampleData3 ) );
echo '</pre>';
