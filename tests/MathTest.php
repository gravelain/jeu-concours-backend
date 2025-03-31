<?php
// tests/Util/MathTest.php

namespace App\Tests\Util;

use PHPUnit\Framework\TestCase;

class MathTest extends TestCase
{
    public function testAdd()
    {
        // Création d'une instance de la classe Math (simulation)
        $math = new Math();
        
        // Test de la méthode add avec des valeurs connues
        $result = $math->add(2, 3);
        
        // Assertion pour vérifier si le résultat est correct
        $this->assertEquals(5, $result);
    }
}

// Définition de la classe Math directement dans le même fichier (pour l'exemple)
class Math
{
    public function add($a, $b)
    {
        return $a + $b;
    }
}
