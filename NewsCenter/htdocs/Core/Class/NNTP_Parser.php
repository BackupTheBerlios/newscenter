<?PHP
/**********************************************************************************
*     NewsCenter
*     /Core/Class/NNTP_Parser.php
*     Version: $Id: NNTP_Parser.php,v 1.4 2004/10/19 04:19:18 exodus Exp $
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

define("CONNSTAT_PARAM_NUM", 2);
define("LISTSTAT_PARAM_NUM", 2);
define("LISTTXT_PARAM_NUM", 4);

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
				return array_combine(array("ResponseCode", "ResponseMsg"),
						$this->splitData($connectResponse, CONNSTAT_PARAM_NUM));
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
					$listarr = split("\n", $listResponse); //split lines of data into an array
					array_pop($listarr); // get rid of the ending bs
					array_pop($listarr);
					
					$firstIndex=FALSE;
					$linearr = NULL;
					
					foreach($listarr as $line)
					{
							if($firstIndex==FALSE) //first index holds the status response
							{
								$firstIndex=TRUE;
								$linearr[] = array_combine(array("ResponseCode", "ResponseMsg"),
												$this->splitData($line, LISTSTAT_PARAM_NUM));
							}
							else //combine text response parameters into array.
								$linearr[] = array_combine(array("NewsGroup", "LastMsgID", "FirstMsgID", "CanPost"),
												$this->splitData($line, LISTTXT_PARAM_NUM));
					}
					return $linearr;
				}
				return FALSE;	
			}
			return FALSE;
		}
		return FALSE;
	}
	
	//GROUP has 2 different status response possibilities
	//that vary in parameter numbers so we have to check
	//the status code before we can determine how many parameters
	//to split & to determine the proper array setup that the 
	//parameters will be combined into.
	public function selectGroup($groupName)
	{
		if($this->m_nntpSocket != NULL && $this->m_nntpSocket->IsConnected)
		{
			if(!$this->m_nntpSocket->sendData("GROUP ".$groupName."\n")==FALSE)
			{
				$groupResponse = $this->m_nntpSocket->getData();
				
				if(!$groupResponse==FALSE)
				{
					return $this->splitData($groupResponse, 2);
				}
				return FALSE;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	private function splitData($nntpData, $numElements)
	{
		return split(" ", $nntpData, $numElements);
	}
	
	
	private function checkStatus($statusCode)
	{
		switch($statusCode)
		{
			
		}
	}
	private function checkStatusCode($progressCode)
	{
		switch($progressCode)
		{
			case '1': //Informative Message
			break;
			
			case '2': //Command OK
			break;
			
			case '3': //Command OK so far, send the rest
			break;
			
			case '4': //Command was correct, but could not be performed
			break;
			
			case '5': //Incorrect command, or serious program error
			break;
			
			default: // ?? invalid status code
			break;
		}
	}
	
	private function checkCategory($categoryCode)
	{
		switch($categorycode)
		{
			case '0': //Connection, setup, and miscellaneous messages
			break;
			
			case '1': //Newsgroup selection
			break;
			
			case '2': //Article selection
			break;
			
			case '3': //Distribution functions
			break;
			
			case '4': //Posting
			break;
			
			case '8': //Nonstandard (private implementation) extensions
			break;
			
			case '9': //Debugging output
			break;
			
			default: // invalid category code ?
			break;	
		}
	}
}

?>