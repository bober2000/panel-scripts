<?php
class CreateDbTest extends UnitTestCase {

    function setUp() { // Метод setUp вызывается каждый раз перед выполнением следующей тестовой функции это удобно для инициализации нужных переменных и пр.

    }
    function tearDown() { // Метод tearDown вызывается каждый раз после выполнения следующей тестовой функции это может быть удобно для очистки уже ненужных переменных и пр.

    }
    function testDBCreate() { // Все имена тестовых методов начинаются с test. Дальше может идти название тестируемого метода
      $cmd='php create_db.php -n testdatabase';
      exec($cmd,$aResult, $return_val);
      $this->assertEqual($return_val,1,"Test creating DB failed");
    }
}