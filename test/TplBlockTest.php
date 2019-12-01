<?php
/**
 * Gnieark’s TplBlock unit tests.
 *
 * PHP version 5
 *
 * @category Template
 * @package  TplBlock
 * @author   gnieark <gnieark@tinad.fr>
 * @license  GNU General Public License V3
 * @link     https://github.com/gnieark/tplBlock/
 */

use PHPUnit\Framework\TestCase;

/**
 * The TplBlockTest class.
 *
 * @category Template
 * @package  TplBlock
 * @author   gnieark <gnieark@tinad.fr>
 * @license  GNU General Public License V3
 * @link     https://github.com/gnieark/tplBlock/
 */
class TplBlockTest extends TestCase
{
    /**
     * A template cannot accept a sub template with no name.
     *
     * @return void
     *
     * 
     */
    public function testSendEmptyNameOnSubFunction()
    {
        $this->expectException(UnexpectedValueException::class);
        $template    = new TplBlock();
        $subTemplate = new TplBlock();

        $template->addSubBlock($subTemplate);
    }

    /**
     * Verify that variable replacement takes place.
     *
     * @return void
     */
    public function testSimpleVar()
    {
        $template = new TplBlock();

        $variables = [
            "name"      => "Gnieark",
            "title"     => "Monsieur",
            "firstname" => "Grouik",
        ];

        $actual = $template
            ->addVars($variables)
            ->applyTplStr("Hello {{name}}");

        $this->assertEquals("Hello Gnieark", $actual);
    }

    /**
     * Test from a file.
     *
     * @return void
     */
    public function testParseFromFile()
    {
        file_put_contents("temp.txt", "Hello {{name}}");

        $template = new TplBlock();

        $variables = [
            "name"      => "Gnieark",
            "title"     => "Monsieur",
            "firstname" => "Grouik",
        ];

        $actual = $template
            ->addVars($variables)
            ->applyTplFile("temp.txt");

        $this->assertEquals("Hello Gnieark", $actual);

        unlink("temp.txt");
    }

    /**
     * Test blocs.
     *
     * @return void
     */
    public function testBlocs()
    {
        $model = "
            Bhah blah wpooie456
            <!-- BEGIN bloc -->
                have to be shown
            <!-- END bloc -->
            <!-- BEGIN blocTwo -->
                WONT to be shown
            <!-- END blocTwo -->
        ";

        $template = new TplBlock();

        $actual = $template
            ->addSubBlock(new TplBlock("bloc"))
            ->applyTplStr($model);

        $this->assertStringContainsString("have", $actual);
        $this->assertFalse(strpos("WONT", $actual));
    }

      /**
     * Test blocs with tabs spaces etc..
     *
     * @return void
     */
    public function testBlocsWithsWeirdSpaces()
    {
        $model = "
            Bhah blah wpooie456
            <!-- BEGIN      bloc     -->
                have to be shown
            <!-- END     bloc                           -->
            <!-- BEGIN                   blocTwo                -->
                WONT to be shown
            <!-- END                                blocTwo -->
        ";

        $template = new TplBlock();

        $actual = $template
            ->addSubBlock(new TplBlock("bloc"))
            ->applyTplStr($model);

        $this->assertStringContainsString("have", $actual);
        $this->assertFalse(strpos("WONT", $actual));
    }

    /**
     * Test if error on blocks names WTF.
     *
     * @return void
     *
     * 
     */
    public function testIfErrorOnForbiddenName()
    {
        $this->expectException(UnexpectedValueException::class);
        new TplBlock("kjsd54 65");
    }

    /**
     * Test if error on blocks names WTF.
     *
     * @return void
     *
     * 
     */
    public function testIfErrorOnForbiddenNameAgain()
    {
        $this->expectException(UnexpectedValueException::class);
        new TplBlock("kjsd54.5");
    }
    
    public function testIfRemoveNonGivenVarsWorks(){
        
        $tpl = new TplBlock();
        $resultWithReplace = $tpl
        ->doReplaceNonGivenVars()
        ->applyTplStr("Hello {{name}}");

        $resultWithoutReplace = $tpl
        ->dontReplaceNonGivenVars()
        ->applyTplStr("Hello {{name}}");


        $this->assertStringContainsString("name",$resultWithoutReplace);
        $this->assertFalse(strpos("name", $resultWithReplace));
    
        
    }
    /**
     * Test if error on non consistent tpl.
     * 
     */
    public function testNonConsistentTemplate(){
        $this->expectException(UnexpectedValueException::class);
        $str = "
        Bhah blah wpooie456
        <!-- BEGIN bloc -->
            have to be shown
        <!-- BEGIN blocTwo -->
            SHOULD be shown
        <!-- END bloc -->
            WONT to be shown
        <!-- END blocTwo -->";
        $tpl = new TplBlock();
        $tpl->applyTplStr($str);
    }
    public function testNonConsistentTemplateNonStrictMode(){
        $str = "
        Bhah blah wpooie456
        <!-- BEGIN bloc -->
            have to be shown
        <!-- BEGIN blocTwo -->
            SHOULD be shown
        <!-- END bloc -->
            WONT to be shown
        <!-- END blocTwo -->";
        $tpl = new TplBlock();
        $tpl-> dontStrictMode();
        $this->assertStringContainsString("wpooie456",$tpl-> applyTplStr($str));

    }

    public function testaddSubBlocsDefinitions(){
        $model = "
{{simpleVar}}
<!-- BEGIN bloc -->
{{bloc.simpleVar}}
<!-- BEGIN bloc.subBloc -->
{{bloc.subBloc.simpleVar}}
<!-- END bloc.subBloc -->
<!-- END bloc -->
";
        $blocsDefinitions = array(
            "simpleVar" => "hey",
            "bloc"  => array(
                "simpleVar" => "Ho",
                "subBloc"   => array(
                    "simpleVar" => "HAAAAAA!"
                )
            )
        );
        $resultShouldBe = "
hey
Ho
HAAAAAA!
";
       $tpl = new TplBlock();
        $result = $tpl -> addSubBlocsDefinitions($blocsDefinitions)->applyTplStr($model);
        $this->assertEquals($result, $resultShouldBe);


    }
    public function testaddSubBlocsDefinitions2(){
        $model = "
{{title}}
<!-- BEGIN fruits -->
{{fruits.name}}
{{fruits.price}}

<!-- END fruits -->";

        $blocsDefinitions = array(
            "title" => "Epicerie",
            "fruits"  => array(
                array(
                    "name" => "banana",
                    "price" => "2€"
                ),
                array(
                    "name" => "Orange",
                    "price" => "3€"
                )

            )
        );
        $resultShouldBe = "
Epicerie
banana
2€
Orange
3€
";

       $tpl = new TplBlock();
        $result = $tpl -> addSubBlocsDefinitions($blocsDefinitions)->applyTplStr($model);
        $this->assertEquals($resultShouldBe,$result);
    }

    public function testis_assoc(){
        $assocArray = array(
            "plip"  => "bar",
            "foo"   => "bar"

        );

        $nonAssocArray = array("apple", "juice","banana");

        $nawak = "kjhglkgkug";
        $nawak2 = 3;

        $this->assertTrue(TplBlock::is_assoc($assocArray));
        $this->assertFalse(TplBlock::is_assoc($nonAssocArray));
        $this->assertFalse(TplBlock::is_assoc($nawak));
        $this->assertFalse(TplBlock::is_assoc($nawak2));

    }
}
