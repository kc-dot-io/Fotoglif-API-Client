<?php
/*******************************************************************************
 * Fotoglif PHP API Client Library
 * Date: Monday Feb 16 2009 9:43am
 * Author: Kyle Campbell <mail@slajax.com>
 * 
 * Copyright (c) 2008 sLajax.com 
 *
 * Licensed under the MIT License:
 * 
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 * 
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 * 
 *******************************************************************************/

class fotoglif extends application
{
	function fotoglif($cli=false){ 
		define("_CLI",$cli);
		define("_APIURL","http://api.fotoglif.com/");
		define("_APIKEY","PUT YOURS HERE");
		define("_APISECRET","PUT YOURS HERE");
		define("_VERSION","sLajax.com - Fotoglif PHP API Client");
	}
	
	function getRecent($page="0"){
		return $this->sendRequest("image/recent","page=".$page);
	}

	function sendRequest($append="",$vars=false,$callback=false) {
		return $this->apiRequest('GET', $append, $vars, $callback);
	}

	function setApiCallback($callback,$static=false){
		$this->curlCallback = $callback;
		if($static) $this->curlStatic=true;
	}

	function makeURL($append="",$vars=""){
		$api_sig = sprintf('%s%s%s',_APISECRET,time(),_APIKEY);
		return _APIURL.$append.'?api_key='._APIKEY.'&api_sig='.md5($api_sig).'&now='.time().'&'.$vars;
	}

	function apiRequest($method, $append="", $vars="", $callback="postProcess") {
		$this->setApiCallback($callback);

#		echo  $this->makeURL($append,$vars);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->makeURL($append,$vars));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 4000);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT, _VERSION);

		if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			if($vars) curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
		}
		$data = curl_exec($ch);
		curl_close($ch);
		if ($data) {
			if ($this->curlCallback)
			{
				$callback = $this->curlCallback;
				if(!$this->curlStatic)$this->curlCallback = false;
				return $this->$callback($data);
			} else {
				return $data;
			}
		} else {
			return curl_error($ch);
		}
	}

	function postProcess($json){
		return $json;
	}
}

/* EXAMPLE:

   	$f=new fotoglif();
   	echo $f->getRecent();

 */
?> 
