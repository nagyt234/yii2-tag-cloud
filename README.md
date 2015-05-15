yii2-tag-cloud
==============

[![Latest Stable Version](https://poser.pugx.org/alexander-suter/yii2-tag-cloud/v/stable)](https://packagist.org/packages/alexander-suter/yii2-tag-cloud)
[![Total Downloads](https://poser.pugx.org/alexander-suter/yii2-tag-cloud/downloads)](https://packagist.org/packages/alexander-suter/yii2-tag-cloud)

A simple tag cloud for Yii2.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

To install, either run

```
$ php composer.phar require alexander-suter/yii2-tag-cloud "*"
```

or add

```
"alexander-suter/yii2-tag-cloud": "*"
```

to the ```require``` section of your `composer.json` file.

## Usage

~~~
echo TagCloud::widget([
		'beginColor' => '00089A',
		'endColor' => 'A3AEFF',
		'minFontSize' => 8,
		'maxFontSize' => 15,
        'displayWeight' => false,
		'arrTags' => array (
                        "MVC"     => array('weight'=> 2),
                        "PHP"     => array('weight'=> 9, 'url' => 'http://php.net'),
                        "MySQL"   => array('weight'=> 8, 'url' => 'http://mysql.com'),
                        "jQuery"  => array('weight'=> 6, 'url' => 'http://jquery.com'),
                        "SQL"     => array('weight'=> 9),
                        "C#"    => array('weight'=> 2),
                ),
		'options' => ['style' => 'word-wrap: break-word;']
]);
~~~
