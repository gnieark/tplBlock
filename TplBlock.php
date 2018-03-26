<?php
/**
 * Gnieark’s TplBlock.
 *
 * PHP version 5
 *
 * @category Template
 * @package  TplBlock
 * @author   gnieark <gnieark@tinad.fr>
 * @link     https://blog-du-grouik.tinad.fr
 * @license  GNU General Public License V3
 * @link     https://github.com/gnieark/tplBlock/
 */
namespace TplBlock;

/**
 * The TplBlock class.
 *
 * @category Template
 * @package  TplBlock
 * @author   gnieark <gnieark@tinad.fr>
 * @license  GNU General Public License V3
 * @link     https://github.com/gnieark/tplBlock/
 */
class TplBlock
{
    /**
     * The string starting a block start.
     *
     * @var string
     */
    const BLOCKSTARTSTART = '<!--\s+BEGIN\s+';

    /**
     * The string ending a block start.
     *
     * @var string
     */
    const BLOCKSTARTEND = '\s+-->';

    /**
     * The string starting a block end.
     *
     * @var string
     */
    const BLOCKENDSTART = '<!--\s+END\s+';

    /**
     * The string ending a block end.
     *
     * @var string
     */
    const BLOCKENDEND = '\s+-->';

    /**
     * The string starting an enclosure.
     *
     * @var string
     */
    const STARTENCLOSURE = '{{';

    /**
     * The string ending an enclosure.
     *
     * @var string
     */
    const ENDENCLOSURE = '}}';

    /**
     * The name of the block.
     *
     * @var string
     */
    public $name = '';

    /**
     * The array containing the variables used by TplBlock.
     *
     * @var array
     */
    private $vars = [];

    /**
     * The array containing the sub blocks.
     *
     * @var array
     */
    private $subBlocs = [];

    /**
     * The regex recognizing that a block is unused.
     *
     * @var string
     */
    private $unusedRegex = "";

    /**
     * Should we trim?
     *
     * @var boolean
     */
    private $trim = true;

   /**
     * Should we replace non set template vars by an empty string?
     *
     * @var boolean
     */
    private $replaceNonGivenVars = true;

    /**
     * Use strict mode?
     * 
     * @var boolean
     */
    private $strictMode = true;
    /**
     * Initialize TplBlock
     *
     * The name can be empty only for the top one block.
     *
     * @param string $name The template name
     */
     
    public function __construct($name = "")
    {
        // Checks that name is valid.
        if ($name !== "" and ! ctype_alnum($name)) {
            throw new \UnexpectedValueException(
                "Only alpha-numerics chars are allowed on the block name"
            );
        }

        $this->name = $name;

        // Build the unused regex.
        $this->unusedRegex = '/'
                           . self::BLOCKSTARTSTART
                           . ' *([a-z][a-z0-9.]*) *'
                           . self::BLOCKSTARTEND
                           . '(.*?)'
                           . self::BLOCKENDSTART
                           . ' *\1 *'
                           . self::BLOCKENDEND
                           . '/is'
                           ;
    }

    /**
     * Add simple variables
     *
     * The array must be structured like this:
     *
     *     [ "key" => "value", "key2" => "value2" ]
     *
     * @param array $vars Variables to add.
     *
     * @return TplBlock For chaining.
     */
    public function addVars(array $vars)
    {
        $this->vars = array_merge($this->vars, $vars);
        return $this;
    }

    /**
     * Add a sub block.
     *
     * @param TplBlock $bloc The block to add as a sub block.
     *
     * @return TplBlock For chaining.
     */
    public function addSubBlock(TplBlock $bloc)
    {
        // An unnamed block cannot be a sub block.
        if ($bloc->name === "") {
            throw new \UnexpectedValueException(
                "A sub tpl block can't have an empty name"
            );
        }

        $this->subBlocs[$bloc->name][] = $bloc;

        return $this;
    }

    /**
     * Generate the sub block regex.
     *
     * @param string $prefix   The prefix to add to the block name.
     * @param string $blocName The block name.
     *
     * @return string The regex.
     */
    private function subBlockRegex($prefix, $blocName)
    {
        return '/'
             . self::BLOCKSTARTSTART
             . preg_quote($prefix . $blocName)
             . self::BLOCKSTARTEND
             . ($this->trim === false ? '' : '(?:\R|)?' )
             . '(.*?)'
             . ($this->trim === false ?  '' : '(?:\R|)?' )
             . self::BLOCKENDSTART
             . preg_quote($prefix . $blocName)
             . self::BLOCKENDEND
             . '/is';
    }

    /**
     * Make some tests to check template consistency.
     * Return error message if needed, return 1 otherwise.
     * 
     * @param string $str the string to check
     * @return string message
     */
    public function checkConsistency($str){
        

        return 1;
    }

    /**
     * Shake the template string and input vars then returns the parsed text.
     *
     * @param string $str          containing the template to parse
     * @param string $subBlocsPath optional, for this class internal use.
     *                             The path should look like "bloc.subbloc".
     *
     * @return string The processed output.
     */
    public function applyTplStr($str, $subBlocsPath = "")
    {
        if($this->strictMode){
            $consistency = self::checkConsistency($str);
            if($consistency <> 1 ){
                throw new \UnexpectedValueException($consistency);
            }

        }

        // Replace all simple vars.
        $prefix = $subBlocsPath === "" ? "" : $subBlocsPath . ".";

        foreach ($this->vars as $key => $value) {
            $str = str_replace(
                self::STARTENCLOSURE . $prefix . $key . self::ENDENCLOSURE,
                $value,
                $str
            );
        }
    
        // Parse blocs.
        foreach ($this->subBlocs as $blocName => $blocsArr) {
            $str = preg_replace_callback(
                $this->subBlockRegex($prefix, $blocName),
                function ($m) use ($blocName, $blocsArr, $prefix) {
                    $out = "";
                    foreach ($blocsArr as $bloc) {
                        // Recursion.
                        $out .= $bloc->applyTplStr(
                            $m[1],
                            $prefix . $blocName
                        );
                    }

                    return $out;
                },
                $str
            );
        }

        // Delete unused blocs.
        $str = preg_replace($this->unusedRegex, "", $str);
        
        //Replace non setted vars by empty string
        if($this->replaceNonGivenVars) {
          $str = preg_replace( "/" .self::STARTENCLOSURE .'([a-z][a-z0-9.]*)' .self::ENDENCLOSURE ."/", '', $str );
        }

        return $str;
    }

    /**
     * Load a file, and pass his content to applyTplStr function.
     *
     * @param string $file The file path of the template to load
     *
     * @return string The processed output.
     */
    public function applyTplFile($file)
    {
        if (! $tplStr = file_get_contents($file)) {
            throw new \UnexpectedValueException("Cannot read given file $file");
        }

        return $this->applyTplStr($tplStr, "");
    }

    /**
     * Enables trimming.
     *
     * @return TplBlock For chaining.
     */
    public function doTrim()
    {
        $this->trim = true;
        return $this;
    }

    /**
     * Disables trimming.
     *
     * @return TplBlock For chaining.
     */
    public function dontTrim()
    {
        $this->trim = false;
        return $this;
    }
    
    /**
     * Enable the behaviour: Non given vars will be replaced by an empty string.
     *
     * @return TplBlock For chaining.
     */
    public function doReplaceNonGivenVars()
    {
      $this->replaceNonGivenVars = true;
      return $this;
    
    }
    
    /**
     * Enable the behaviour: Non given vars will be replaced by an empty string.
     *
     * @return TplBlock For chaining.
     */
    public function dontReplaceNonGivenVars()
    {
      $this->replaceNonGivenVars = false;
      return $this;
    
    }

    /**
     * Enable mode strict. If template is inconsistent, will throw an exception 
     * and return nothing
     * 
     * @return TplBlock For chaining.
     */
    public function doStrictMode()
    {
        $this->strictMode = true;
        return $this;
    }

    /**
     * Disable mode strict. If template is inconsistent, will be parsed anyway.  
     * and no errors will be returned.
     * 
     * @return TplBlock For chaining.
     */
    public function dontStrictMode(){
        $this->strictMode = false;
        return $this;

    }
}
