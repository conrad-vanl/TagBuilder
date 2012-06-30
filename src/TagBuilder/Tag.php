<?php

namespace Builder;

//Copyright (c) 2010 Ethan Blackwelder, http://www.eblackwelder.com/
//
//Permission is hereby granted, free of charge, to any person obtaining
//a copy of this software and associated documentation files (the
//"Software"), to deal in the Software without restriction, including
//without limitation the rights to use, copy, modify, merge, publish,
//distribute, sublicense, and/or sell copies of the Software, and to
//permit persons to whom the Software is furnished to do so, subject to
//the following conditions:
//
//The above copyright notice and this permission notice shall be
//included in all copies or substantial portions of the Software.
//
//THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
//EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
//MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
//NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
//LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
//OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
//WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

/*
 * This file contains the TagBuilder library (http://code.google.com/p/eblackwelder/wiki/TagBuilder).
 */

define('TAG_BUILDER_MIN_PHP_VERSION', '5.3.0');

if (version_compare(phpversion(), TAG_BUILDER_MIN_PHP_VERSION) < 0) {
	//See http://www.php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
	$warning = 'HTML Tag builder requires PHP 5.3.0+ for __callStatic() to work. You must use the Tag constructor manually.';
	trigger_error($warning, E_USER_NOTICE);
}

/**
 * @param var_args (mixed, optional, var-args) Anything...
 * @return string Simply oncatenates the arguments.
 * @todo : add line-breaks and/or tabs on demand (using a separate method?)
 */
function Tag($var_args = '') {
	return join('', func_get_args());
}

/**
 * Provides an HTML "tag" builder.
 * @see http://genshi.edgewall.org/wiki/ApiDocs/genshi.builder for related functionality (and inspiration)
 * @author Ethan
 * @todo : more ways to add/append HTML?
 * @todo : add a toJSON method (that can handle complex data for the innerHtml)
 */
class Tag implements ArrayAccess {
	private static $self_closing_tags = array('area', 'base', 'basefont', 'br', 'col', 'frame', 'hr', 'input', 'img', 'link', 'meta', 'param');
	private $name;
	private $innerHtml;
	private $attributes = array();

	public function __construct($tag_name, $innerHtml = '', $attributes = array()) {
		$this->name = $tag_name;
		$this->innerHtml = $innerHtml;
		$this->setAttributes((array) $attributes);
	}

	/**
	 * Magic method to intercept static method calls: usage is <code>Tag::$tag_name([ $innerHtml, [$attributes] ])</code> 
	 * @param $name (string) The name of the static method being called. Becomes the tag name.
	 * @param $arguments (array) The function arguments.
	 * @return (Tag) an instance of Tag.
	 * @throws Exception if $name is "falsy" or if called with more than 2 arguments.
	 */
	public static function __callStatic($name, $arguments) {
		$tag_name = $name;
		$innerHtml = '';
		$attributes = array();
		switch (count($arguments)) {
		case 0:
			break;
		case 1:
			$innerHtml  = array_shift($arguments);
			break;
		case 2:
			$innerHtml  = array_shift($arguments);
			$attributes = array_shift($arguments);
			break;
		default:
			throw new Exception("Usage: Tag::$name([\$innerHtml, [\$attributes]]) - returns a tag by the name of $name.");
			break;
		}
		if (!$tag_name) {
			throw new Exception('No tag name provided.');
		}
		return new Tag($tag_name, $innerHtml, $attributes);
	}
	
	/**
	 * Intercepts calls to unknown instance methods, used to add/overwrite attributes to $this.
	 * Usage: <code>$tag->$attr($value);</code>
	 * @param $name (string) The name of the method, acts as the name of the attribute.
	 * @param $arguments (array) Either none (for empty-attributes) or 1 (for the attribute value).
	 * @return Tag - for method-chaining
	 * @throws Exception if more than 1 argument is given.
	 */
	public function __call($name, $arguments) {
		switch (count($arguments)) {
		case 0:
			$this->attributes[$name] = NULL;
			break;
		case 1:
			$this->attributes[$name] = array_shift($arguments);
			break;
		default:
			throw new Exception("Usage: Tag.$name([\$value]) - Sets $name attribute. Returns \$this (for chaining).");
			break;
		}
		return $this; //for chaining
	}
	/**
	 * Adds all $attributes thus:
	 *   associative key => value entries become an attribute/value pair (key="value"),
	 *   but values with numeric indexes become empty attributes.
	 * @param $attributes (array) Attributes to add
	 * @return Tag - for method-chaining
	 */
	public function setAttributes(array $attributes) {
		foreach ((array) $attributes as $key => $value) {
			if (is_numeric($key)) {
				$this->attributes[$value] = NULL; //empty attribute
			} else {
				$this->attributes[$key] = $value;
			}
		}
		return $this; //for chaining
	}
	
	/**
	 * Unsets the $name attribute (if it exists).
	 * @param $name (string) an attribute name
	 * @return Tag - for method-chaining
	 */
	public function removeAttribute($name) {
		if (array_key_exists($name, $this->attributes)) {
			unset($this->attributes[$name]);
		}
		return $this; //for chaining
	}
	
	/**
	 * @return An HTML representation of $this, it's attributes (if any), and it's innerHtml (if any).
	 * Knows about HTML 4 self-closing tags.
	 */
	public function __toString() {
		$attribute_string = '';
		if (count($this->attributes) > 0) {
			$attribute_string = ' ' . $this->getAttributeString(); //leading space
		}
		$name = $this->name;
		if (in_array($name, Tag::$self_closing_tags)) {
			return "<$name$attribute_string />";
		} else {
			return "<$name$attribute_string>{$this->innerHtml}</$name>";
		}
	}
	
	private function getAttributeString() {
		$values = array();
		foreach ($this->attributes as $key => $value) {
			if ($value === NULL) { //empty attribute
				$tmp = $key;
			} else {
				$tmp = "$key=\"$value\"";
			}
			$values[] = $tmp;
		}
		return join(' ', $values);
	}
	
	
	/* Magic attribute getting/setting */
	
	public function __get($name) {
		$value = NULL;
		if (array_key_exists($name, $this->attributes)) {
			$value = $this->attributes[$name];
		}
		return $value;
	}
	
	public function __set($name, $value) {
		$this->attributes[$name] = $value;
	}
	
	/* ArrayAccess methods */
	
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->attributes);
	}
	
	public function offsetGet($offset, $default = NULL) {
		$value = $default;
		if (array_key_exists($offset, $this->attributes)) {
			$value = $this->attributes[$offset];
		}
		return $value;
	}
	
	public function offsetSet($offset, $value) {
		$this->attributes[$offset] = $value;
	}
	
	public function offsetUnset($offset) {
		unset($this->attributes[$offset]);
	}
}