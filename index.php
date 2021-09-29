<?php

class EgyszerusitettPontszamitoKalkulator
{
	const KOTELEZO_TARGYAK = ['magyar nyelv és irodalom', 'történelem', 'matematika'];
	const SUBJECT_LEVELS = ['közép', 'emelt'];
	const TOBBLETPONT = [
		'NYELVVIZSGA' => [
			'B2' => 28,
			'C1' => 40
		],
		'EMELT_SZINT' => 50,
		'MAXIMUM' => 100
	];

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

	protected function nyelvvizsgaTobbletpontSzamitas()
	{
		$osszTobbletpont = 0;
		$nyelvvizsga_tobbletpont = [];
		foreach( $this->erettsegi_eredmeny['tobbletpontok'] as $eredmeny_key=>$eredmeny_value )
		{
			$nyelvvizsga_tobbletpont[ $eredmeny_value['nyelv'] ] =
				(!isset( $nyelvvizsga_tobbletpont[ $eredmeny_value['nyelv'] ] ) ?
					self::TOBBLETPONT['NYELVVIZSGA'][ $eredmeny_value['tipus'] ] :
						( $nyelvvizsga_tobbletpont[ $eredmeny_value['nyelv'] ] > self::TOBBLETPONT['NYELVVIZSGA'][ $eredmeny_value['tipus'] ] ? $nyelvvizsga_tobbletpont[ $eredmeny_value['nyelv'] ] : self::TOBBLETPONT['NYELVVIZSGA'][ $eredmeny_value['tipus'] ])
				);
		}
		foreach( $nyelvvizsga_tobbletpont as $adott_nyelv_tobbletpont )
		{
			$osszTobbletpont += $adott_nyelv_tobbletpont;
		}
		return $osszTobbletpont;
	}

	protected function valaszthatokLegjobbEredmenye($valaszthatok, $eredmeny)
	{
		$legjobberedmeny = 0;
		foreach( $eredmeny as $key => $targy )
		{
			if( in_array( $targy['nev'], $valaszthatok ) )
			{
				if( $legjobberedmeny < (int)$targy['eredmeny'] )
				{
					$legjobberedmeny = (int)$targy['eredmeny'];
				}
			}
		}
		return $legjobberedmeny;
	}

	protected function emeltszintuTobbletpontszam()
	{
		$emeltszintutobbletpont = 0;
		foreach( $this->erettsegi_eredmeny['erettsegi-eredmenyek'] as $eredmenyek )
		{
			if( $eredmenyek['tipus'] === 'emelt' )
			{
				$emeltszintutobbletpont += self::TOBBLETPONT['EMELT_SZINT'];
			}
		}
		return $emeltszintutobbletpont;
	}


	protected function kotelezoEredmenye($id, $array) {
		foreach ($array as $key => $val) {
			if ($val['nev'] === $id) {
				return (int)$val['eredmeny'];
			}
		}
		return 'nincs kötelező tárgyból érettségi';
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

		$kotelezo_eredmenye = 2*( $this->kotelezoEredmenye( $this->szakok[ $this->erettsegi_eredmeny['valasztott-szak']['szak'] ] ['kotelezo']['name'], $this->erettsegi_eredmeny['erettsegi-eredmenyek'] ) );

		$valaszthato_eredmenye = 2*( $this->valaszthatokLegjobbEredmenye( $this->szakok[ $this->erettsegi_eredmeny['valasztott-szak']['szak'] ] ['valaszthato'], $this->erettsegi_eredmeny['erettsegi-eredmenyek'] ) );

		$osszeredmeny = $kotelezo_eredmenye+$valaszthato_eredmenye;

		$nyelvvizsga_tobbletpont = $this->nyelvvizsgaTobbletpontSzamitas();

		$emeltszintu_tobbletpontszam = $this->emeltszintuTobbletpontszam();

		$osszTobbletpont = ( 100 > $nyelvvizsga_tobbletpont+$emeltszintu_tobbletpontszam ? $nyelvvizsga_tobbletpont+$emeltszintu_tobbletpontszam : 100);

		//echo $this->calculatePontszam();
		return $osszeredmeny+$osszTobbletpont;
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
}