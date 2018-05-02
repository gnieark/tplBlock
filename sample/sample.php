<?php
/**
 * Gnieark’s TplBlock sample.
 *
 * PHP version 5
 *
 * @category Template
 * @package  TplBlock
 * @author   gnieark <gnieark@tinad.fr>
 * @license  GNU General Public License V3
 * @link     https://github.com/gnieark/tplBlock/
 */
require_once __DIR__."/../autoload.php";

const PRIMES = [ 1, 2, 3, 5, 7, 11 ];

/**
 * Find divisors of a number.
 *
 * It works as long as the number is less than the last PRIMES number squared.
 *
 * @param int $number The number to find divisors for.
 *
 * @return array An array of divisors.
 */
function findDivisors(int $number)
{
    $divisors = [];
    $index    = 1;
    while ($number > 1 and $index < count(PRIMES)) {
        if ($number % PRIMES[$index] != 0) {
            $index++;
            continue;
        }
        
        $number     = $number / PRIMES[$index];
        $divisors[] = PRIMES[$index];
    }

    return $divisors;
}

$variables = [
    "name"      => "Gnieark",
    "title"     => "Monsieur",
    "firstname" => "Grouik",
];

// Simples vars.
$template = (new TplBlock())->addVars($variables);

// A sub bloc.
foreach (PRIMES as $prime) {
    $template->addSubBlock(
        (new TplBlock("primes"))->addVars([ "number" => $prime ])
    );
}

// Find highest number for which we can find divisors.
$lastNumber = pow(PRIMES[count(PRIMES) - 1], 2);

// Test sub - sub blocs.
for ($i = 2; $i <= $lastNumber; $i++) {
    $templateNumber = (new TplBlock("number"))->addVars([ "value" => $i ]);

    foreach (findDivisors($i) as $divisor) {
        $templateNumber->addSubBlock(
            (new TplBlock("divisor"))->addVars([ "value" => $divisor ])
        );
    }

    $template->addSubBlock($templateNumber);
}

echo $template->applyTplFile("tpl.txt");
