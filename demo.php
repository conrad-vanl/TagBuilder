<?php
/*
Copyright (c) 2010 Ethan Blackwelder, http://www.eblackwelder.com/

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */


require_once('Tag.php');

//Helper method to see tags in the web-browser:
function html_println() {
	echo "<pre>";
	foreach (func_get_args() as $arg) {
		echo htmlentities($arg), "\n";
	}
	echo "</pre>";
}

echo Tag::title('Tag Builder demo');
echo Tag::h2('Tag Builder demos');
echo Tag::p('Now for some examples...');

html_println(
	"1) Multiple ways to create simple tags:",
	
	//<h1>Hello, World!</h1>
	Tag::h1('Hello, World!'),       //Requires PHP 5.3.0
	new Tag('h1', 'Hello, World!'), //Fall-back way...
	"<h1>Hello, World!</h1>",       //good old fashioned way!
	
	//<br />
	Tag::br(),
	Tag::br('FOO!') // innerHtml ignored on self-closing tags
);



//part of example 2: you can't inline this example because it uses array-access (can't do method-chaining)
$input = Tag::input();
$input['type'] = $input['name'] = 'submit';
$input['value'] = 'Submit';
$input['disabled'] = NULL;

//remove an attribute:
$input['foo'] = 'oops';
unset($input['foo']);

//or like this:
$input->bar('whoops')->removeAttribute('bar');

html_println(
	"2) Several ways to assign attributes:",
	
	// <a href="http://www.google.com/">Click me!</a>
	Tag::a('Click me!')->href('http://www.google.com/'),                           //method-chaining
	Tag::a('Click me!', array('href' => 'http://www.google.com/')),                //(static) all-in-one
	Tag::a('Click me!')->setAttributes(array('href' => 'http://www.google.com/')), //post-constructor
	new Tag('a', 'Click me!', array('href' => 'http://www.google.com/')),          //constructor
	
	// <a href="http://en.wikipedia.org/" target="_blank">Click me!</a>
	Tag::a('Click me!')->href('http://en.wikipedia.org/')->target('_blank'),
	Tag::a('Click me!', array('href' => 'http://en.wikipedia.org/', 'target' => '_blank')),
	
	//Notice: "disabled" attribute has no value
	// <input type="submit" value="Submit" name="submit" disabled />
	Tag::input()->type('submit')->value('Submit')->name('submit')->disabled(),
	Tag::input()->type('submit')->value('Submit')->name('submit')->disabled(NULL),
	Tag::input(NULL, array('type' => 'submit', 'value' => 'Submit', 'name' => 'submit', 'disabled')),
	Tag::input(NULL, array('type' => 'submit', 'value' => 'Submit', 'name' => 'submit', 'disabled' => NULL)),
	Tag::input()->setAttributes(array('type' => 'submit', 'value' => 'Submit', 'name' => 'submit', 'disabled')),
	Tag::input()->setAttributes(array('type' => 'submit', 'value' => 'Submit', 'name' => 'submit', 'disabled' => NULL)),
	$input, // using "array-like" interface
	
	// <script src="path/to/src" type="text/javascript" />
	Tag::script()->src('path/to/src')->type('text/javascript')
);


html_println(
	"3) Use the \"Tag()\" function to concatenation multiple tags (without a root node): ",
	
	// <p>Plain</p>
	// <p><i>Italic</i></p>
	// <p><i><b>Bold Italic</b></i></p>
	Tag(
		Tag::p('Plain'),
		"\n",
		Tag::p( Tag::i( 'Italic' )),
		"\n",
		Tag::p( Tag::i(Tag::b( 'Bold Italic' ))),
		"\n"
	)
);

html_println(
	"4) Use the \"Tag()\" function to nest multiple tags or strings (as another tag's innerHtml).",
	
	Tag::div(
		Tag(
			"\n\t",
			Tag::h2('Oops...'),
			"\n\t",
			Tag::br(),
			"\n\t",
			Tag::p('A coolant leak occurred and the ' . Tag::b('reactor') . ' is gonna blow ' . Tag::i('any minute') . '!'),
			"\n"
		)
	)
);
	

html_println(
	// <good>bye!</good>
	Tag::good('bye!')
);

echo Tag::p( 'Visit the project website at ' . Tag::a('http://code.google.com/p/eblackwelder/')->href('http://code.google.com/p/eblackwelder/') . '.');

