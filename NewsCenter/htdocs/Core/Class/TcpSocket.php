<?php

/**********************************************************************************
*     NewsCenter
*     /Core/Class/TcpSocket.php
*     Version: $Id: TcpSocket.php,v 1.3 2004/10/09 14:06:52 exodus Exp $
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

include_once ("ITcpSocket.php");

define("DEFAULT_LOCAL_IP", "127.0.0.1");

class TcpSocket implements ITcpSocket
{
	
	//properties array
	private $props;
	private $m_socket; //socket handle
	
	function __construct()
	{
		//initialize our properties array
		$this->resetprops();			
		$this->props["LocalHost"] = $_SERVER['SERVER_NAME'];
		$this->props["LocalIP"] = gethostbyname($this->LocalHost);
	}
	
	function __destruct()
	{
		$this->close();	
	}
	
	public function connect($remoteHost, $remotePort)
	{
		//make sure we aren't already connected
		if($this->m_socket==NULL || !$this->IsConnected)
		{
			//here we will check if the remoteHost argument is local
			//if so there is no need to resolve the Host addy.
			//otherwise we will resolve & then connect.
			switch($remoteHost)
			{
				case "localhost": case DEFAULT_LOCAL_IP:
				case $this->LocalHost: case $this->LocalIP:
				
					$this->props["LocalPort"] = $remotePort;
					
					return $this->init_connect($this->LocalIP, $remotePort);
				break;
				
				default :
					//remote end point info
					$this->props["RemoteIP"] = gethostbyname($remoteHost);
					$this->props["RemoteHost"] = gethostbyaddr($this->RemoteIP);
					$this->props["RemotePort"] = $remotePort;
					
					return $this->init_connect($this->RemoteIP, $remotePort);	
				break;	
			}	
		}
		return FALSE; // already connected, return FALSE...	
	}
	
	private function init_connect($ip, $port)
	{
			//if our socket handle is NULL create a socket.
			if($this->m_socket==NULL)
				$this->m_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);	
	
			if(socket_connect($this->m_socket, $ip, $port))
			{
				$this->init_socket();
				$this->props["IsConnected"] = TRUE;
				return TRUE;
			}
			
			return FALSE;		
	}
	
	//listen for incomming connections
	public function listen($localPort, $pendingMax)
	{
		//make sure our socket is not already in use.
		if($this->m_socket==NULL & $this->IsConnected==FALSE)
		{
			$soc = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			
			if(!socket_bind($soc, $this->LocalIP, $localPort))
				return FALSE;
				
			if(!socket_listen($soc, $pendingMax))
				return FALSE;
				
			$this->props["LocalPort"] = $localPort;
			$this->props["IsListening"] = TRUE;
			
			$this->m_socket = socket_accept($soc);
			
			if($this->m_socket != FALSE)
			{
				$this->props["IsListening"] = FALSE;
				$this->props["IsConnected"] = TRUE;
				
				//remote end point information
				$this->props["RemotePort"] = $localPort;
				socket_getpeername($this->m_socket, $this->props["RemoteIP"]);
				$this->props["RemoteHost"] = gethostbyaddr($this->props["RemoteIP"]);
				
				$this->init_socket();
	
				return TRUE;
			}
			else
				return FALSE;	
		}
		
		return FALSE; //socket in use , return FALSE	
	}
	
	
	private function init_socket()
	{
		socket_set_block($this->m_socket); //sets the socket to blocking mode
		socket_set_option($this->m_socket, SOL_SOCKET, SO_RCVBUF, $this->ReceiveBuffer);
	}
	
	public function sendData($packet)
	{
		//make sure socket handle is initialized & connected
		if($this->m_socket != NULL && $this->IsConnected==TRUE)
		{
			//this will make sure the socket is writeable
			if(!(socket_select($r = NULL, $sockarr = array($this->m_socket), $e = NULL, $this->SelectInterval)) === FALSE)
			{
				$s = socket_send($this->m_socket, $packet, strlen($packet), 0);
				
				return $s; //return the amount of bytes sent
			}
		}
		
		return FALSE;
	}
	
	//receive any incomming data.
	public function getData()
	{
		//make sure socket isn't NULL & is connected
		if($this->m_socket != NULL && $this->IsConnected==TRUE)
		{
			$sockarr = array($this->m_socket);
			//make sure socket is readable
			if(!socket_select($sockarr, $w = NULL, $e = NULL, $this->SelectInterval) === FALSE)
			{
				do //loop until all available data is received into the buffer.
				{
					$in_data = NULL;
					socket_recv($this->m_socket, $in_data, 
									$this->ReceiveBuffer, 0);						
					$totaldata .= $in_data;
					
				} while(!socket_select($sockarr, $w = NULL, $e = NULL, 1) === FALSE);	
				
				return $totaldata;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	//shutdown all socket activity & free all resources
	public function close()
	{
		if($this->m_socket != NULL)
		{
			socket_shutdown($this->m_socket);
			$this->resetprops();
			socket_close($this->m_socket);
			$this->m_socket = NULL;
		}
	}
	
	private function resetprops()
	{
		$this->props = array("RemoteHost" => NULL, "RemoteIP" => NULL, 
			"RemotePort" => NULL, "LocalHost" => NULL,
			"LocalIP" => NULL, "LocalPort" => NULL,
			"IsConnected" => FALSE, "IsListening" => FALSE,
			"SelectInterval" => 500, "ReceiveBuffer" => 8192);
	}
	
	//properties functions
	public function __get($nm)
	{
		if(isset($this->props[$nm]))
		{
			return $this->props[$nm];	
		}
	}
	
	public function __set($nm, $val)
	{
		
		if(isset($this->props[$nm]))
		{
			switch ($nm)
			{
				case "ReceiveBuffer":
					if($this->m_socket != NULL)
						socket_set_option($this->m_socket, SOL_SOCKET, SO_RCVBUF, $val);
						
					if($val <= 1024000 & $val > 0)
						$this->props[$nm] = $val;
				break;
				
				case "SelectInterval":
					if($val <= 61440 & $val >= 0)
							$this->props[$nm] = $val;
				break;
				
				default:
				break;	
			}
		}
	}
		
} // end class TcpSocket

?>