<?PHP
/**********************************************************************************
*     NewsCenter
*     /Core/Class/NNTP_Parser.php
*     Version: $Id: NNTP_Parser.php,v 1.3 2004/10/13 00:49:01 exodus Exp $
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
		$this->m_nntpSocket = new TcpSocket();
	}
	
	function __destruct()
	{
		$this->m_nntpSocket=NULL;
	}
	
	public function connect($serverName)
	{
		if($this->m_nntpSocket->connect($serverName, 119))
		{
			$connectResponse = $this->m_nntpSocket->getData();
			
			if(!$connectResponse==FALSE)
			{
				$connectarr = array("ResponseCode" => substr($connectResponse, 0, 3),
									"ResponseMsg" => substr($connectResponse, 4));
				return $connectarr;	
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function listGroups()
	{
		if($this->m_nntpSocket != NULL && $this->m_nntpSocket->IsConnected)
		{
			if(!$this->m_nntpSocket->sendData("LIST\n")==FALSE)
			{
				$listResponse = $this->m_nntpSocket->getData();
				
				if(!$listResponse==FALSE)
				{
					$listarr = split("\n", $listResponse);
					$index=FALSE;
					$linearr = NULL;
					
					foreach($listarr as $line)
					{
						if($index==FALSE)
						{
							$index=TRUE;
							$linearr[] = array("ResponseCode" => substr($line, 0, 3),
											"ResponseMsg" => substr($line, 4));
						}
						else
							$linearr[] = split(" ", $line);
					}
					return $linearr;
				}
				return FALSE;	
			}
			return FALSE;
		}
		return FALSE;
	}
}

?>