<?php
/*******************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Redirect;

use Bitrix\Main\Localization\Loc;

const ID = 'rodzeta.redirect';
const APP = __DIR__.'/';
const LIB = APP.'lib/';

define(__NAMESPACE__."\CONFIG",
	$_SERVER['DOCUMENT_ROOT'].'/local/config/.'.ID.'/');

const FILE_OPTIONS = CONFIG.'options.php';
const FILE_REDIRECTS = CONFIG.'urls.csv';
const FILE_REDIRECTS_CACHE = CONFIG.'urls.php';
const FILE_REDIRECTS_DOMAINS = CONFIG.'domains.php';

require LIB.'encoding/include.php';

function AppendValues($data, $n, $v)
{
	yield from $data;
	for ($i = 0; $i < $n; ++$i) {
		yield  $v;
	}
}

function Options()
{
	return is_readable(FILE_OPTIONS) ?
		include FILE_OPTIONS : [
			'redirect_www' => 'Y',
			'redirect_https' => '',
			'redirect_slash' => 'Y',
			'redirect_index' => 'Y',
			'use_redirect_urls' => 'N',
			'ignore_query' => 'Y',
			'redirect_from_404' => 'N',
		];
}

function OptionsUpdate($data)
{
	if (!is_dir(CONFIG)) {
		@mkdir($_SERVER['DOCUMENT_ROOT'].'/local/', BX_DIR_PERMISSIONS);
		@mkdir($_SERVER['DOCUMENT_ROOT'].'/local/config/', BX_DIR_PERMISSIONS);
		@mkdir($_SERVER['DOCUMENT_ROOT'].'/local/config/.'.ID, BX_DIR_PERMISSIONS);
	}
	\Encoding\PhpArray\Write(FILE_OPTIONS, [
		'redirect_www' => $data['redirect_www'],
		'redirect_https' => $data['redirect_https'],
		'redirect_slash' => $data['redirect_slash'],
		'redirect_index' => $data['redirect_index'],
		'redirect_multislash' => $data['redirect_multislash'],
		'use_redirect_urls' => $data['use_redirect_urls'],
		'ignore_query' => $data['ignore_query'],
		'redirect_from_404' => $data['redirect_from_404'],
	]);
}

function Select($fromCsv = false)
{
	if ($fromCsv) {
		return \Encoding\Csv\Read(FILE_REDIRECTS);
	}

	return is_readable(FILE_REDIRECTS_CACHE) ?
		include FILE_REDIRECTS_CACHE : [];
}

function Update($data)
{
	$urls = [];
	$urlsMap = [];
	foreach ($data['redirect_urls'] as $url) {
		$from = trim($url[0]);
		$to = trim($url[1]);
		if ('' != $from && '' != $to) {
			$urls[] = $url;
			$urlsMap[$from] = [$to, trim($url[2]), trim($url[3])];
		}
	}
	if (!is_dir(CONFIG)) {
		@mkdir(CONFIG, 0777, true);
	}
	\Encoding\Csv\Write(FILE_REDIRECTS, $urls);
	\Encoding\PhpArray\Write(FILE_REDIRECTS_CACHE, $urlsMap);
}

function Domains()
{
	return is_readable(FILE_REDIRECTS_DOMAINS) ?
		include FILE_REDIRECTS_DOMAINS : [];
}

function HandlerRedirectUrl()
{
	if ('GET' != $_SERVER['REQUEST_METHOD'] && 'HEAD' != $_SERVER['REQUEST_METHOD']) {
		return;
	}
	// ignore scripts from /bitrix/, cli scripts and cron scripts
	if ('cli' == php_sapi_name()
			|| defined('BX_CRONTAB')
			|| \CSite::InDir('/bitrix/')) {
		return;
	}
	$currentOptions = Options();
	$host = $_SERVER['SERVER_NAME'];
	$protocol = !empty($_SERVER['HTTPS'])
		&& 'off' != $_SERVER['HTTPS'] ? 'https' : 'http';
	$port = (!empty($_SERVER['SERVER_PORT'])
				&& '80' != $_SERVER['SERVER_PORT']
				&& '443' != $_SERVER['SERVER_PORT']) ?
			(':'.$_SERVER['SERVER_PORT']) : '';

	$currentUri = $_SERVER['REQUEST_URI'];
	$url = null;
	$isAbsoluteUrl = false;

	if ('Y' == $currentOptions['redirect_www']
			&& 'www.' == substr($_SERVER['SERVER_NAME'], 0, 4)) {
		$host = substr($_SERVER['SERVER_NAME'], 4);
		$url = $currentUri;
	}

	$toProtocol = $currentOptions['redirect_https'];
	if ('to_https' == $toProtocol && 'http' == $protocol) {
		$protocol = 'https';
		$url = $currentUri;
	} elseif ('to_http' == $toProtocol && 'https' == $protocol) {
		$protocol = 'http';
		$url = $currentUri;
	}

	if ('Y' == $currentOptions['redirect_index']
			|| 'Y' == $currentOptions['redirect_slash']
			|| 'Y' == $currentOptions['redirect_multislash']) {
		$changed = false;
		$u = parse_url($currentUri);
		if ('Y' == $currentOptions['redirect_index']) {
			$tmp = rtrim($u['path'], '/');
			if ('index.php' == basename($tmp)) {
				$dname = dirname($tmp);
				$u['path'] = (DIRECTORY_SEPARATOR != $dname ? $dname : '').'/';
				$changed = true;
			}
		}
		if ('Y' == $currentOptions['redirect_slash']) {
			$tmp = basename(rtrim($u['path'], '/'));
			// add slash to url
			if ('/' != substr($u['path'], -1, 1)
					&& '.php' != substr($tmp, -4)
					&& '.htm' != substr($tmp, -4)
					&& '.html' != substr($tmp, -5)) {
				$u['path'] .= '/';
				$changed = true;
			}
		}
		if ('Y' == $currentOptions['redirect_multislash']) {
			if (false !== strpos($u['path'], '//')) {
				$u['path'] = preg_replace('{/+}s', '/', $u['path']);
				$changed = true;
			}
		}
		if ($changed) {
			$url = $u['path'];
			if (!empty($u['query'])) {
				$url .= '?'.$u['query'];
			}
		}
	}

	$status = '';
	if ('Y' == $currentOptions['use_redirect_urls']) {
		if ('Y' == $currentOptions['ignore_query']) {
			$currentUri = parse_url($currentUri, PHP_URL_PATH);
		}
		$redirects = Select();
		if (isset($redirects[$currentUri])) {
			list($url, $status) = $redirects[$currentUri];
			if ('http' == substr($url, 0, 4)) {
				$isAbsoluteUrl = true;
			}
		} else {
			// find part url
			foreach ($redirects as $fromUri => $v) {
				list($toUri, $status, $partUrl) = $v;
				if ('Y' != $partUrl) {
					continue;
				}
				$reFromUri = '{'.str_replace("\*\*\*", '(.+?)', preg_quote($fromUri)).'}s';
				if (preg_match($reFromUri, $currentUri, $m)) {
					$tmp = [];
					foreach ($m as $matchIdx => $matchValue) {
						if ($matchIdx > 0) {
							$tmp['{'.$matchIdx.'}'] = $matchValue;
						}
					}
					$url = str_replace(array_keys($tmp), array_values($tmp), $toUri);
					break;
				}
			}
		}
	}
	$status = '302' == $status ?
		'302 Found' : '301 Moved Permanently';

	// host redirects
	$domainRedirects = Domains();
	if (!empty($domainRedirects[$host])) {
		$host = $domainRedirects[$host];
		if (empty($url)) {
			$url = $currentUri;
		}
	}

	if (!empty($url)) {
		if ($isAbsoluteUrl) {
			LocalRedirect($url, true, $status);
		} else {
			LocalRedirect($protocol.'://'.$host.$port.$url, true, $status);
		}
		exit;
	}
}

function OnEpilog()
{
	if (!defined('ERROR_404') || ERROR_404 != 'Y') {
		return;
	}
	global $APPLICATION;
	// get parent level url
	$uri = parse_url($APPLICATION->GetCurPage(false), PHP_URL_PATH);
	$segments = explode('/', trim($uri, '/'));
	array_pop($segments);
	if (count($segments) > 0) {
		$uri = '/'.implode('/', $segments).'/';
	} else {
		$uri = '/';
	}
	// redirect
	LocalRedirect($uri, false, '301 Moved Permanently');
	exit;
}

function init()
{
	Loc::loadMessages(__FILE__);

	AddEventHandler(
		'main',
		'OnBeforeProlog',
		__NAMESPACE__.'\\HandlerRedirectUrl'
	);

	$options = Options();
	if ('Y' == $options['redirect_from_404']) {
		AddEventHandler(
			'main',
			'OnEpilog',
			__NAMESPACE__.'\\OnEpilog'
		);
	}
}

init();
