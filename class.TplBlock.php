<?php
class InvalidTemplateException extends UnexpectedValueException{

}
class TplBlock {
  const blockStartStart = '<!-- BEGIN ';
  const blockStartEnd = ' -->';
  const blockEndStart = '<!-- END ';
  const blockEndEnd = ' -->';

  const startEnclosure = '{{';
  const endEnclosure = '}}';

  public $name = '';
  private $vars = array();
  private $subBlocs = array();

  /*
  * Initialise TplBlock
  * Input object name
  */

  public function __construct($name = NULL){
    $this->name = $name;
  }

  /*
  * Add simple vars
  * Input array structured like:
  * {"key":"value","key2":"value2"}
  */
  public function add_vars(ARRAY $vars){
    $this->vars = array_merge($this->vars,$vars);
  }

  public function add_sub_block(TplBlock $bloc){
    if(is_null($bloc->name) || empty($bloc->name)){
      throw new InvalidTemplateException("A sub tpl bloc can't have an empty name");
      return false;
    }
    $this->subBlocs[$bloc->name][] = $bloc;
  }

  /*
  * Shake template and input vars and returns the text
  */
  public function apply_tpl_str($str,$subBlocsPath = ""){

    //replace all simple vars
    $prefix = (empty($subBlocsPath)? "" : $subBlocsPath.".");
    foreach($this->vars as $key=>$value){

      $str = str_replace(self::startEnclosure . $prefix . $key . self::endEnclosure,
                          $value,
                           $str);
    }
    
    //Isolate blocs

    foreach($this->subBlocs as $blocName => $blocsArr){
      $str = preg_replace_callback(
        '/' . self::blockStartStart . preg_quote($prefix . $blocName) . self::blockStartEnd .
          '(.*?)'.
          self::blockEndStart . preg_quote($prefix . $blocName). self::blockEndEnd. 
        '/is',
        function($m) use($blocName,$blocsArr,$prefix) {
          $out = "";
          foreach($blocsArr as $bloc){
            $out.=$bloc->apply_tpl_str( $m[1] , $prefix . $blocName );
          }
          return $out;
        }
        ,$str
      );
    }
    return $str;
  }

  public function apply_tpl_file($file){
    if(!$tplStr = file_get_contents($file)){
      throw new InvalidTemplateException("Cannot read given file ".$file);
      return false;
    }
    return $this->apply_tpl_str($tplStr);
  }
}
