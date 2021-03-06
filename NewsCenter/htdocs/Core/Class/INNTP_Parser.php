<?PHP
/**********************************************************************************
*     NewsCenter
*     /Core/Class/INNTP_Parser.php
*     Version: $Id: INNTP_Parser.php,v 1.1 2004/10/09 14:06:52 exodus Exp $
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

interface INNTP_Parser
{
	public function Connect($serverName);
	public function SelectArticle($msgID, $IsMsgID);
	public function SelectBody($msgID, $IsMsgID);
	public function SelectHead($msgID, $IsMsgID);
	public function SelectStat($msgID, $IsMsgID);
	public function SelectGroup($groupName);
	public function Help();
	public function IHave($msgID);
	public function Last();
	public function ListGroups();
	public function ListNewGroups($date, $time, $distro);
	public function GetNewNews($newsgroup, $date, $time, $distro);
	public function Next();
	public function Post();
	public function Slave();
	public function Quit();
	public function GetLastInternalError();
}

?>