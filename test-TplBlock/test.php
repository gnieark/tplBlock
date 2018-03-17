<?php

include("../class.TplBlock.php");

$tpl = new TplBlock();
$tpl->add_vars(array(
    "name" => "Gnieark",
    "title" => "Monsieur",
    "firstname" => "Grouik"
    )
  );

$primes = array(1,2,3,5,7,11);

foreach($primes as $prime){
  $tplPrime = new TplBlock('primes');
  $tplPrime->add_vars(array('number'  => $prime));
  $tpl->add_sub_block($tplPrime);


}




echo $tpl->apply_tpl_file("sample.txt");