
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
    //test from a file
    public function testParseFromFile(){
        file_put_contents("temp.txt","Hello {{name}}");
        $tpl = new TplBlock();
        $tpl->add_vars(array(
            "name" => "Gnieark",
            "title" => "Monsieur",
            "firstname" => "Grouik"
            )
          );
          $this->assertEquals("Hello Gnieark", $tpl->apply_tpl_file("temp.txt"));
          unlink("temp.txt");
    }

    //test blocs
    public function testBlocs(){
        $str = "
            Bhah blah wpooie456
            <!-- BEGIN bloc -->
                have to be shown
            <!-- END bloc -->
            <!-- BEGIN blocTwo -->
                WONT to be shown
            <!-- END blocTwo -->
        ";
        $tpl = new TplBlock();
        $tpl2 = new TplBlock("bloc");
        $tpl->add_sub_block($tpl2);
        $str = $tpl->apply_tpl_str($str);
        $this->assertContains('have',$str);
        $this->assertFalse(strpos("WONT",$str));
    }

    //test if error on blocks names WTF
    /**
      * @expectedException InvalidTemplateException
      */
    public function testIfErrorOnForbiddenName(){
        $tpl = new TplBlock("kjsd54 65");
    }

    //test if error on blocks names WTF
    /**
      * @expectedException InvalidTemplateException
      */
    public function testIfErrorOnForbiddenNameAgain(){
        $tpl = new TplBlock("kjsd54.5");
    }


}