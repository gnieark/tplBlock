# tplBlock

A very simple PHP template class

[![Build Status](https://travis-ci.org/gnieark/tplBlock.svg?branch=master)](https://travis-ci.org/gnieark/tplBlock)

# Sample

This simple template file:

```html
<html>
<body>
    <h1>{{pageTilte}}</h1>
    <ul>
    <!-- BEGIN templatesystem -->
        <li><a href="{{templatesystem.url}}"> {{templatesystem.name}}</a>
         by {{templatesystem.author}} is {{templatesystem.quality}}</li> 
    <!-- END templatesystem -->
    </ul>

</body>
</html>
```

Parsed with this code

```php
<?php
require_once ("path/class.TplBlock.php");

//init object
$tpl = new TplBlock();
//add a var
$tpl->add_vars(array("pageTilte" => "Poke @zigazou ;)"));


$data = array(
    array(
        "url"       => "https://github.com/gnieark/tplBlock",
        "name"      => "tplBlock",
        "author"    => "Gnieark",
        "quality"   => "simple and perfect"
    ),
    array(
        "url"       => "https://github.com/Zigazou/TemplateEngine",
        "name"      =>  "TemplateEngine",
        "author"    => "Zigazou",
        "quality"   => "more complex than tplBlock"

    )
);

//add blocks
foreach ($data as $block){
    $tplTemplateSystem = new TplBlock("templatesystem");
    $tplTemplateSystem -> add_vars($block);
    $tpl->add_sub_block($tplTemplateSystem);
}

//parsing:
echo $tpl->apply_tpl_file("template.html");
```
will return:

```html
<html>
<body>
    <h1>Poke @zigazou ;)</h1>
    <ul>
            <li><a href="https://github.com/gnieark/tplBlock"> tplBlock</a>
         by Gnieark is simple and perfect</li> 
            <li><a href="https://github.com/Zigazou/TemplateEngine"> TemplateEngine</a>
         by Zigazou is more complex than tplBlock</li> 
    
    </ul>

</body>
</html>
```

# Conception choices

I wrote this class for use it on others personnals projects. It's really simple. I think logicals functions "OR" "IF", filtering, caching, are not the templating system matter.

If a block ( <--BEGIN .... )is in the template, but is not called, it will be deleted.

Methods apply_tpl_file and apply_tpl_str have for second (optional) parameters a bolean. (true if not given).
If true, the potentials carriage returns just after the BEGIN and just before the END are deleted.


For now, class is permissive. I'll improve it to manage templating errors.