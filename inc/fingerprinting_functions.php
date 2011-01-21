<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Adrien LUCAS <contact@adrienlucas.net>
*  All rights reserved
*
*  This script is part of the TYPO3 Fingerprinting project. The 
*  TYPO3 Fingerprinting project is free software but is not part of
*  the TYPO3 project; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

$targetSums = array();

function usage(){
	global $argv;
	exit("Usage : $ {$argv[0]} [-debug] http://example.org/\n\n");
}

function matchBranchInMetatag(){
	global $_target, $_ctx;

	$branchMatches = array();
	
	preg_match('#content="TYPO3 ([0-9.-]+)#', file_get_contents($_target, FALSE, $_ctx), $branchMatches);
	
	return isset($branchMatches[1]) ? str_replace('-', '.', $branchMatches[1]) : FALSE;
}

function matchVersionInChangelog($files){
	global $_target, $_ctx;
	
	$cl = '';
	$versionMatches = array();
	
	while(!preg_match('#Release of TYPO3 ([0-9.-]+)#', $cl, $versionMatches) && !empty($files))
		$cl = file_get_contents($_target . array_shift($files), FALSE, $_ctx);
	
	return isset($versionMatches[1]) ? $versionMatches[1] : FALSE;
}

function matchVersionsByFilesums($sums){
	global $_target, $_ctx, $targetSums;
	
	$versions = array();
	
	foreach($sums as $file => $fingerprints){
		if(!isset($targetSums[$file])){
			$content = file_get_contents($_target . $file, FALSE, $_ctx);
		
			if($content !== FALSE) $content = md5($content);
		
			$targetSums[$file] = $content;
		}
		
		if(isset($fingerprints[$targetSums[$file]])) foreach($fingerprints[$targetSums[$file]] as $ver){
			if(!isset($versions[$ver]))
				$versions[$ver] = 0;
			$versions[$ver]++;
		}
		
	}
	
	arsort($versions, SORT_NUMERIC);
	return $versions;
}
