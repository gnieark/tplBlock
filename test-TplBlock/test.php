<?php

include("../class.TplBlock.php");

$tpl = new TplBlock();

//simples vars

$tpl->add_vars(array(
    "name" => "Gnieark",
    "title" => "Monsieur",
    "firstname" => "Grouik"
    )
  );

$primes = array(1,2,3,5,7,11);

// a sub bloc
foreach($primes as $prime){
  $tplPrime = new TplBlock('primes');
  $tplPrime->add_vars(array('number'  => $prime));
  $tpl->add_sub_block($tplPrime);
}

// test sub - sub blocs
for ($i = 2; $i < 121; $i++){
    
    $tplNumber = new TplBlock('number');
    $tplNumber->add_vars( array("value" => $i));
    $index = 1;
    $number = $i;
    while ( $number > 1 && $index < count($primes)){
        if($number % $primes[$index] == 0){
            $number = $number / $primes[$index];
            $tplDivisor = new TplBlock("divisor");
            $tplDivisor->add_vars( array("value" => $primes[$index]));
            $tplNumber->add_sub_block($tplDivisor);
        }else{
            $index++;
        }
    }
    $tpl->add_sub_block($tplNumber);
}



echo $tpl->apply_tpl_file("sample.txt",false);