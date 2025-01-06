# tplBlock

A very simple PHP template class


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

Parsed with this code:

```php
<?php
require_once ("path/TplBlock.php");

//init object
$tpl = new TplBlock();
//add a var
$tpl->addVars(array("pageTilte" => "Poke @zigazou ;)"));


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
    $tplTemplateSystem -> addVars($block);
    $tpl->>addSubBlock($tplTemplateSystem);
}

//parsing:
echo $tpl->applyTplFile("template.html");
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


# Install

Simply put the file TplBlock.php on your project https://raw.githubusercontent.com/gnieark/tplBlock/master/TplBlock.php and include it

# Documentation

See the comments on the TplBlock.php file; the sample and the unit tests.
