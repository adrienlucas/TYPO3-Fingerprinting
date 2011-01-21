<?php

function optionIsSet($opt){
	static $options = '';
	if(empty($options))
		$options = ' '.implode(' ', array_slice($GLOBALS['argv'], 1)).' ';
	return strpos($options, ' -'.$opt.' ') !== FALSE;
}
