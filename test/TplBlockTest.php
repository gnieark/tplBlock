
<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__.'/../class.TplBlock.php';

class TplBlockTest extends TestCase{
    /**
      * @expectedException InvalidTemplateException
      */
    public function testSendEmptyNameOnSubFunction(){
        $tpl = new TplBlock();
        $subTpl = new TplBlock();
        $tpl->add_sub_block($subTpl);    
    }

    public function testsimpleVar(){
        $tpl = new TplBlock();
        $tpl->add_vars(array(
            "name" => "Gnieark",
            "title" => "Monsieur",
            "firstname" => "Grouik"
            )
          );
        $this->assertEquals("Hello Gnieark", $tpl->apply_tpl_str("Hello {{name}}"));    
    }

}