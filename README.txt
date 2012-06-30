= TagBuilder: programmatically build well-formed HTML code.
= Version 1.0
= Hosted at http://code.google.com/p/eblackwelder/wiki/TagBuilderProject
= Copyright (c) 2010 Ethan Blackwelder, http://www.eblackwelder.com/



= Introduction =

[TagBuilderProject TagBuilder] is an HTML tag "builder": it can programmatically create well-formed HTML code. It is written in PHP and consists of one file (`Tag.php`) and the following API:
 * `class Tag` - all kinds of magic methods and other overloading trickery to easily build HTML tags.
 * `Tag()` - produces the string concatenation of multiple nodes (can be used as the innerHtml for another tag).

Read the manual about [http://www.php.net/manual/en/language.oop5.magic.php magic methods] and [http://www.php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods method overloading] or check out the examples below...


= Installation =
 # Download either the .ZIP or the .TAR.GZ archives from the Project's Downloads page.
 # Unzip/extract the files.
 # Place the `Tag.php` file in your PHP include_path.


= Examples =
You can download these examples from the [http://code.google.com/p/eblackwelder/source/browse/TagBuilder/trunk/ TagBuilder source folder].

== Example 1: Creating a Tag ==
Multiple ways to create simple tags:
{{{
//<h1>Hello, World!</h1>
echo "<h1>Hello, World!</h1>";       //good old fashioned way!
echo Tag::h1('Hello, World!');       //Requires PHP 5.3.0 (uses __callStatic)
echo new Tag('h1', 'Hello, World!'); //Works in PHP 5

//<br />
echo Tag::br();
echo Tag::br('FOO!'); // innerHtml ignored on self-closing tags
}}}

== Example 2: Adding Attributes ==
Multiple ways to assign attributes:
{{{
// <a href="http://www.google.com/">Click me!</a>
echo Tag::a('Click me!')->href('http://www.google.com/'); //method-chaining
echo Tag::a('Click me!', array('href' => 'http://www.google.com/')); //(static) all-in-one
echo Tag::a('Click me!')->setAttributes(array('href' => 'http://www.google.com/')); //post-constructor
echo new Tag('a', 'Click me!', array('href' => 'http://www.google.com/')); //constructor

// <a href="http://en.wikipedia.org/" target="_blank">Click me!</a>
echo Tag::a('Click me!')->href('http://en.wikipedia.org/')->target('_blank');
echo Tag::a('Click me!', array('href' => 'http://en.wikipedia.org/', 'target' => '_blank'));

//via ArrayAccess interface: (can't do method-chaining)
$a = new Tag('a', 'Click me!');
$a['href'] = 'http://en.wikipedia.org/';
$a['target'] = '_blank';
echo $a;

//Notice: "disabled" attribute has no value
// <input type="submit" value="Submit" name="submit" disabled />
echo Tag::input()->type('submit')->value('Submit')->name('submit')->disabled();
echo Tag::input()->type('submit')->value('Submit')->name('submit')->disabled(NULL);
echo Tag::input(NULL, array('type' => 'submit', 'value' => 'Submit', 'name' => 'submit', 'disabled'));
echo Tag::input(NULL, array('type' => 'submit', 'value' => 'Submit', 'name' => 'submit', 'disabled' => NULL));
echo Tag::input()->setAttributes(array('type' => 'submit', 'value' => 'Submit', 'name' => 'submit', 'disabled'));
echo Tag::input()->setAttributes(array('type' => 'submit', 'value' => 'Submit', 'name' => 'submit', 'disabled' => NULL));

//via ArrayAccess interface: (can't do method-chaining)
$input = new Tag('input');
$input['type'] = $input['name'] = 'submit';
$input['value'] = 'Submit';
$input['disabled'] = NULL;
echo $input;

// <script src="path/to/src" type="text/javascript" />
echo Tag::script()->src('path/to/src')->type('text/javascript');
}}}


== Example 3: Using Tag() to concatenate tags ==
{{{
// <p>Plain</p>
// <p><i>Italic</i></p>
// <p><i><b>Bold Italic</b></i></p>
echo Tag(
	Tag::p('Plain'),
	Tag::p( Tag::i( 'Italic' )),
	Tag::p( Tag::i(Tag::b( 'Bold Italic' )))
);
}}}

== Example 4: Nesting tags (via innerHtml of another tag) ==
Notice the use of `Tag()` to nest more than one 
{{{
echo Tag::div(
	Tag(
		Tag::h2('Oops...'),
		Tag::br(),
		Tag::p('A coolant leak occurred and the ' . Tag::b('reactor') . ' is gonna blow ' . Tag::i('any minute') . '!')
	)
);
}}}

= Removing Attributes =
{{{
$input = Tag::input();

// 1) ArrayAccess interface: (can't do method-chaining)
$input['foo'] = 'oops';
unset($input['foo']);

// 2) `Tag::removeAttribute()`
$input->bar('whoops')->removeAttribute('bar');
}}}

= Bugs =
 * No known bugs.
Note: Some (syntactic-sugar) features depends on PHP 5.3.0, but the API is flexible enough that there are alternative ways to accomplish the same thing. See Example 1.
Note: Method-chaining from static method calls is invalid syntax in PHP < 5.3.0 (so don't if you can't).

= Future Work =
Things that could be improved:
 * `Tag()` function - add line-breaks and/or tabs on demand (maybe a `Tagf(indentation, var-args)` method?)
 * Add more ways to add/append HTML to a `Tag` object (maybe just an `append(var-args)` method).
 * Implement a `Tag::toJSON()` method, just for fun.


= Related Projects =
I got the idea (and much of the semantics) for this from [http://genshi.edgewall.org/wiki/ApiDocs/genshi.builder Genshi's Builder API] (from the Genshi library by Edgewall.org).
