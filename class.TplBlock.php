<?php
class InvalidTemplateException extends UnexpectedValueException{

}
class TplBlock {
/*
* Class TplBlog
* By gnieark https://blog-du-grouik.tinad.fr 2018
* Licenced under the GNU General Public License V3
* https://www.gnu.org/licenses/gpl-3.0.fr.html
*/
  const blockStartStart = '<!-- BEGIN ';
  const blockStartEnd = ' -->';
  const blockEndStart = '<!-- END ';
  const blockEndEnd = ' -->';

  const startEnclosure = '{{';
  const endEnclosure = '}}';

  public $name = '';
  private $vars = array();
  private $subBlocs = array();
  private $unusedRegex = "";

  /*
  * Initialise TplBlock
  * Input object name
  * Can be empty only for the top one block
  */

  public function __construct($name = NULL){
    $this->name = $name;

    $this->unusedRegex = '/'
                       . self::blockStartStart
                       . ' *([a-z][a-z.]*) *'
                       . self::blockStartEnd
                       . '(.*?)'
                       . self::blockEndStart
                       . ' *\1 *'
                       . self::blockEndEnd
                       . '/is'
                       ;

  }

  /*
  * Add simple vars
  * Input array structured like:
  * {"key":"value","key2":"value2"}
  */
  public function add_vars(ARRAY $vars){
    $this->vars = array_merge($this->vars,$vars);
  }

  /*
  * add_sub_block
  * Input: a TplBlock object.
  */
  public function add_sub_block(TplBlock $bloc){
    if(is_null($bloc->name) || empty($bloc->name)){
      throw new InvalidTemplateException("A sub tpl bloc can't have an empty name");
      return false;
    }
    $this->subBlocs[$bloc->name][] = $bloc;
  }

  private function subBlockRegex($prefix, $blocName,$trim = true) {
      echo "t".$trim;
    return '/'
         . self::blockStartStart
         . preg_quote($prefix . $blocName)
         . self::blockStartEnd
         . (($trim === false)? '' : '(?:\R|)?' )
         . '(.*?)'
         . (($trim === false)?  '' : '(?:\R|)?' )
         . self::blockEndStart
         . preg_quote($prefix . $blocName)
         . self::blockEndEnd
         . '/is';
  }

  /*
  * Shake the template string and input vars 
  * Then returns the parsed text
  * Input: 
  *   $str String containing the template to parse
  *   $subBlocsPath String optional, for this class internal use. The path like "bloc.subbloc"
  *   $trim Boolean
  *       if true(default), the potentials Carriages returns beginning 
  *       and ending the bloc are deleted
  */
  public function apply_tpl_str($str,$subBlocsPath = "", $trim = true){

    //replace all simple vars
    $prefix = (empty($subBlocsPath)? "" : $subBlocsPath.".");
    foreach($this->vars as $key=>$value){

      $str = str_replace(self::startEnclosure . $prefix . $key . self::endEnclosure,
                          $value,
                           $str);
    }
    
    //parse blocs
    foreach($this->subBlocs as $blocName => $blocsArr){
      $str = preg_replace_callback(
        $this->subBlockRegex($prefix, $blocName, $trim),
        function($m) use($blocName,$blocsArr,$prefix, $trim) {
          $out = "";
          foreach($blocsArr as $bloc){
            //recursion
            $out.=$bloc->apply_tpl_str( $m[1] , $prefix . $blocName , $trim );
          }
          return $out;
        }
        ,$str
      );
    }

    // Delete unused blocs
    $str = preg_replace($this->unusedRegex, "", $str);
    return $str;
    
  }

  /*
  * load a file, and pass his content to apply_tpl_str function.
  */
  public function apply_tpl_file($file, $trim = true){
    if(!$tplStr = file_get_contents($file)){
      throw new InvalidTemplateException("Cannot read given file ".$file);
      return false;
    }
    return $this->apply_tpl_str($tplStr, null, $trim);
  }
}
