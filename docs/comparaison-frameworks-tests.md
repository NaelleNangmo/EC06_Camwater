# Comparaison PHPUnit / atoum / SimpleTest

## Vue d'ensemble

| Critère | PHPUnit | atoum | SimpleTest |
|---|---|---|---|
| Maturité | Très mature (2004) | Moderne (2011) | Ancien (2000) |
| Maintenance | Active | Active | Ralentie |
| Intégration Laravel | Native | Manuelle | Manuelle |
| Installation | `phpunit/phpunit` | `atoum/atoum` | `simpletest/simpletest` |

---

## Syntaxe comparée

### PHPUnit
```php
class FactureTest extends TestCase
{
    public function test_calcul_domestique(): void
    {
        $this->assertSame(2800, Facture::calculerMontant(8, 'Domestique'));
    }

    public function test_exception_consommation_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Facture::calculerMontant(-5, 'Domestique');
    }
}
```

### atoum
```php
// namespace Tests\Atoum\App\Models — @namespace Tests\Atoum
class Facture extends atoum\test
{
    public function testCalculDomestique()
    {
        $this->integer(\App\Models\Facture::calculerMontant(8, 'Domestique'))
             ->isEqualTo(2800);
    }

    public function testExceptionConsommationNegative()
    {
        $this->exception(function () {
            \App\Models\Facture::calculerMontant(-5, 'Domestique');
        })->isInstanceOf(\InvalidArgumentException::class);
    }
}
```

### SimpleTest
```php
class FactureSimpleTest extends UnitTestCase
{
    public function testCalculDomestique()
    {
        $this->assertEqual(2800, Facture::calculerMontant(8, 'Domestique'));
    }

    public function testExceptionConsommationNegative()
    {
        try {
            Facture::calculerMontant(-5, 'Domestique');
            $this->fail('Exception attendue');
        } catch (\InvalidArgumentException $e) {
            $this->pass();
        }
    }
}
```

---

## Points clés

### PHPUnit
- Standard de facto en PHP, intégré nativement dans Laravel (`php artisan test`)
- Assertions classiques `assert*`, lisibles et bien documentées
- Support complet : mocks, data providers, coverage, groupes
- Convention de nommage libre (`test_` ou `@test`)

### atoum
- API fluide et chaînable : `$this->integer(...)->isEqualTo(...)`
- Isolation forte : chaque méthode de test tourne dans un sous-processus séparé
- Convention stricte : le namespace de la classe de test doit mapper vers la classe testée
- Nécessite un bootstrap manuel pour Laravel

### SimpleTest
- Syntaxe proche de JUnit/PHPUnit (`assertEqual`, `assertTrue`)
- Pas de sous-processus — exécution directe, plus simple à déboguer
- Inclut un navigateur web headless pour les tests fonctionnels
- Maintenance ralentie, peu adapté aux projets modernes
- Pas de gestion native des exceptions (try/catch manuel)

---

## Quand choisir quoi ?

- **PHPUnit** : toujours, pour un projet Laravel — c'est le choix naturel
- **atoum** : si on veut une API fluide et une isolation maximale entre tests
- **SimpleTest** : héritage ou projets legacy uniquement
