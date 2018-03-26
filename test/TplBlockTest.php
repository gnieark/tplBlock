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
namespace TplBlockTest;

use PHPUnit\Framework\TestCase;
use TplBlock\TplBlock;

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
     * @expectedException UnexpectedValueException
     */
    public function testSendEmptyNameOnSubFunction()
    {
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

        $this->assertContains("have", $actual);
        $this->assertFalse(strpos("WONT", $actual));
    }

    /**
     * Test if error on blocks names WTF.
     *
     * @return void
     *
     * @expectedException UnexpectedValueException
     */
    public function testIfErrorOnForbiddenName()
    {
        new TplBlock("kjsd54 65");
    }

    /**
     * Test if error on blocks names WTF.
     *
     * @return void
     *
     * @expectedException UnexpectedValueException
     */
    public function testIfErrorOnForbiddenNameAgain()
    {
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


        $this->assertContains("name",$resultWithoutReplace);
        $this->assertFalse(strpos("name", $resultWithReplace));
    
        
    }
}
