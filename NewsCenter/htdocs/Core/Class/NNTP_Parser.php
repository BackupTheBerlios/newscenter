<?PHP
/**********************************************************************************
*     NewsCenter
*     /Core/Class/NNTP_Parser.php
*     Version: $Id: NNTP_Parser.php,v 1.2 2004/10/09 16:37:23 jcrawford Exp $
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

class NNTP_Parser //implements INNTP_Parser
{
	private $m_nntpSocket;
	
	function __construct()
	{
		
	}
	
	function __destruct()
	{
		$this->m_nntpSocket=NULL;
	}
	
	public function Connect($serverName)
	{
		$m_nntpSocket = new TcpSocket();
		if($m_nntpSocket->connect($serverName, 119))
		{
			$connectResponse = $m_nntpSocket->getData();
			return $connectResponse;	
		}
	}
	
	public function ListGroups()
	{
		if($m_nntpSocket != NULL && $m_nntpSocket->IsConnected)
		{
			if(!$m_nntpSocket->sendData("LIST\n")==FALSE)
			{
				$listResponse = $m_nntpSocket->getData();
				return $listResponse;
			}
		}
	}
}

?>