<?php
/**********************************************************************************
*     NewsCenter
*     /Core/Class/Template.php
*     Version: $Id: Template.php,v 1.1 2004/10/09 02:00:59 jcrawford Exp $
*     Copyright (c) 2004, The NewsCenter Development Team

*     Permission is hereby granted, free of charge, to any person obtaining
*     a copy of this software and associated documentation files (the
*     "Software"), to deal in the Software without restriction, including
*     without limitation the rights to use, copy, modify, merge, publish,
*     distribute, sublicense, and/or sell copies of the Software, and to
*     permit persons to whom the Software is furnished to do so, subject to
*     the following conditions:

*     The above copyright notice and this permission notice shall be
*     included in all copies or substantial portions of the Software.

*     THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
*     EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
*     MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
*     NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
*     BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
*     ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
*     CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
*     SOFTWARE.
*********************************************************************************/
include_once('Smarty/Smarty.class.php');
final class Template extends Smarty {
	
	private static $instance;
	
	private function __construct() {
		$this->Smarty();
		
		$this->template_dir = BASE_PATH.'/smarty/templates/';
		$this->compile_dir = BASE_PATH.'/smarty/templates_c/';
		$this->config_dir = BASE_PATH.'/smarty/configs/';
		$this->cache_dir = BASE_PATH.'/smarty/cache/';
		
		$this->caching = false;
		
		$this->assign('appname', 'NewsCenter');
	}
	
	static function getInstance() {
		if(!Template::$instance) {
			Template::$instance = new Template();
		}
		return Template::$instance;	
	}
}
?>