<?php

/**********************************************************************************
*     NewsCenter
*     /Core/Class/TcpSocket.php
*     Version: $Id: TcpSocket.php,v 1.1 2004/10/08 21:25:29 jcrawford Exp $
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

/***********************************************************
*TODOs & IDEAS for v1.1 beta
*1. implement better exception handling
*2. make my own TcpSocketEx (exception) class
*3. update receive method to receive to buffer until no data is
*	readily available.
	
*************************************************************/

include ("ITcpSocket.php");

define("DEFAULT_LOCAL_IP", "127.0.0.1");

class TcpSocket implements ITcpSocket
{

	//properties array
	private $props;
	
	private $m_selectInterval;
	
	//remote end point information
	private $m_RemoteHost;
	private $m_RemoteIP;
	private $m_RemotePort;
	
	//local info
	private $m_localHost;
	private $m_localIP;
	private $m_localPort;
	
	private $m_socket; //socket handle
	
	//socket state booleans
	private $connected = FALSE;
	private $listening = FALSE;
	

	function __construct()
	{
		$this->m_localHost = $_SERVER['SERVER_NAME']; //get local host name
		$this->m_localIP = gethostbyname($this->m_localHost); //get local IP
		$this->m_receiveBuffer = 8192; //8k
		$this->m_selectInterval = 500; // half a second; 500 milliseconds
		
		//initialize our properties array
		$this->props = array("RemoteHost" => $this->m_RemoteHost, "RemoteIP" => $this->m_RemoteIP, 
					"RemotePort" => $this->m_RemotePort, "LocalHost" => $this->m_localHost,
					"LocalIP" => $this->m_localIP, "LocalPort" => $this->m_localPort,
					"IsConnected" => $this->connected, "IsListening" => $this->listening,
					"SelectInterval" => $this->m_selectInterval, "SocketHandle" => $this->m_socket);
	}
	
	function __destruct()
	{
		$this->close();	
	}
	
	public function connect($remoteHost, $remotePort)
	{
		//make sure we aren't already connected
		if($this->m_socket==NULL || !$this->connected)
		{
			//here we will check if the remoteHost argument is local
			//if so there is no need to resolve the Host addy.
			//otherwise we will resolve & then connect.
			switch($remoteHost)
			{
				case "localhost": case DEFAULT_LOCAL_IP:
				case $this->m_localHost: case $this->m_localIP:
				
					$this->m_localPort = $remotePort;
					
					return $this->init_connect($this->m_localIP, $remotePort);
					
				break;
				
				default :
				
					$this->m_RemoteIP = gethostbyname($remoteHost);
					$this->m_RemoteHost = gethostbyaddr($this->m_RemoteIP);
					$this->m_RemotePort = $remotePort;
					
					return $this->init_connect($this->m_RemoteIP, $remotePort);
					
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
				
				$this->connected = TRUE;
				return TRUE;
			}
			
			return FALSE;		
	}
	
	//listen for incomming connections
	public function listen($localPort, $pendingMax)
	{
		//make sure our socket is not already in use.
		if($this->m_socket==NULL)
		{
			$soc = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			
			socket_bind($soc, $this->m_localIP, $localPort);
			
			socket_listen($soc, $pendingMax);
			$this->m_localPort = $m_localPort;
			$this->listening = TRUE;
			
			$this->m_socket = socket_accept($soc);
			
			if($this->m_socket != FALSE)
			{
				$this->listening = FALSE;
				$this->connected = TRUE;
				
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
		set_socket_blocking($this->m_socket); //sets the socket to blocking mode
		socket_set_option($this->m_socket, SOL_SOCKET, SO_RCVBUF, 8192);
	}
	
	public function sendData($packet)
	{
		//make sure socket handle is initialized & connected
		if($this->m_socket != NULL && $this->connected==TRUE)
		{
			//this will make sure the socket is writeable
			if(!(socket_select($r = NULL, $sockarr = array($this->m_socket), $e = NULL, $this->m_selectInterval)) === FALSE)
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
		if($this->m_socket != NULL && $this->connected==TRUE)
		{
			if((!socket_select($sockarr = array($this->m_socket), $w = NULL, $e = NULL, $this->m_selectInterval)) === FALSE)
			{
				$rcvbuff = socket_get_option($this->m_socket, SOL_SOCKET, SO_RCVBUF);
				
				socket_recv($this->m_socket, $in_data, 
								$rcvbuff, 0);				
				return $in_data;
			}	
		}
		return FALSE;
	}
	
	//shutdown all socket activity & free all resources
	public function close()
	{
		if($this->m_socket != NULL)
		{
			socket_shutdown($this->m_socket);
			$this->connected = FALSE;
			$this->listening = FALSE;
			
			socket_close($this->m_socket);
			$this->m_socket = NULL;
		}
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
		switch ($nm)
		{
			case "ReceiveBuffer":
				if($this->m_socket != NULL)
					if($val <= 1024000 & $val > 0)
						socket_set_option($this->m_socket, SOL_SOCKET, SO_RCVBUF, $val)
					;
			break;
			
			case "SelectInterval":
				if($val <= 61440 & $val >= 0)
					$this->props[$nm] = $val;
			break;
			
			default:
			break;	
		}
	}
	
} // end class TcpSocket

?>