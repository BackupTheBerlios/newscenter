<?php
/**********************************************************************************
*     NewsCenter
*     /index.php
*     Version: $Id: index.php,v 1.4 2004/10/09 16:37:23 jcrawford Exp $
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

include('global.php');



try {
	$tpl = Template::getInstance();
	$sock = new NNTP_Parser();
	$sock->connect('news.php.net');
	echo 'response: '.$sock->ListGroups();

} catch(TcpSocketEx $e) {
	echo 'TCP SOCKET EXCEPTION<BR>';
	echo 'Message: '.$e->getMessage().'<br>';
	echo 'File: '.$e->getFile().'<br>';
	echo 'Line: '.$e->getLine().'<br>';
	echo '<br>';
	echo 'Trace: <pre>';
	print_r($e->getTrace());
	echo '</pre>';
} catch (TemplateEx $e) {
	echo 'TEMPLATE EXCEPTION<BR>';
	echo 'Message: '.$e->getMessage().'<br>';
	echo 'File: '.$e->getFile().'<br>';
	echo 'Line: '.$e->getLine().'<br>';
	echo '<br>';
	echo 'Trace: <pre>';
	print_r($e->getTrace());
	echo '</pre>';
} catch (Exception $e) {
	unset($db);
	echo '<BR>!!! ERROR !!!<br><br>';
	echo 'Message: '.$e->getMessage().'<br>';
	echo 'File: '.$e->getFile().'<br>';
	echo 'Line: '.$e->getLine().'<br>';
	echo '<br>';
	echo 'Trace: <pre>';
	print_r($e->getTrace());
	echo '</pre>';
}
?>