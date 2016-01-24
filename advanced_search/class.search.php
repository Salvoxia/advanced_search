<?php

/* includes */
require_once("version.php");
require_once("class.advkilllist.php");
require_once("common/includes/class.pagesplitter.php");

class AdvSearch
{
	static function replace(&$object)
	{
		require_once("common/includes/class.killlisttable.php");
		$replace_search = config::get('adv_search_replace_search') == "true";
		
		if ($replace_search or $_REQUEST['p'] == "adv_search")
		{
			$object->replace("newSearch", "AdvSearch::start");
			$object->replace("checkSearch", "AdvSearch::advSrch");
			$object->delete("display");
		}
		else
		{
			$object->addBehind("checkSearch", "AdvSearch::srchHdr");
		}
	}
	
	function srchHdr()
	{
		return "<a href=\"?a=search&p=adv_search\"><div class=block-header>▼ Advanced Options</div></a>\n";
	}
	
	static function start($object)
	{
		/* advanced search */
		$object->page->setTitle('Advanced Search');
		
		/* start session */
		session_start();
	}
	
	static function advSrch($object)
	{
		/* init variables */
		global $smarty;
		$html = "";
		
		// process $_REQUEST data, escape string, decode html entities
		$req = array();
		foreach ($_REQUEST as $name => $val)
		{
			$req[$name] = DBBaseQuery::escape(htmlspecialchars_decode(trim($val)));
		}
		
		if ($req['act'] == "go" and (isset($req['killperpage']) or isset($_SESSION['killperpage'])))
		{	/* do the search */
			// create advanced kill list with filter options
			$list = new AdvKillList();
			
			// check if being paged
			if (isset($req['killperpage']))
			{
				// populate filter options
				$killperpage = $req['killperpage'];
				
				$list->setDestrShpClass($req['destroyedshipclass']);
				$list->setInvShpClass($req['invshipclass']);
				$list->setSysRange($req['sysrange']);
				
				if (strlen($req['victimname']) > 0)
					$list->setVictimName($req['victimname']);
				
				if (strlen($req['victimcorp']) > 0)
					$list->setVictimCorp($req['victimcorp']);
				
				if (strlen($req['victimally']) > 0)
					$list->setVictimAlly($req['victimally']);
				
				if (strlen($req['destroyedship']) > 0)
					$list->setDestroyedShip($req['destroyedship']);
				
				if (strlen($req['system']) > 0)
					$list->setSystem($req['system']);
				
				if (strlen($req['const']) > 0)
					$list->setConstellation($req['const']);
				
				if (strlen($req['region']) > 0)
					$list->setRegion($req['region']);
				
				if (strlen($req['invship']) > 0)
					$list->setInvShip($req['invship']);
				
				if (strlen($req['invpilot']) > 0)
					$list->setInvPilot($req['invpilot']);
				
				if (strlen($req['invcorp']) > 0)
					$list->setInvCorp($req['invcorp']);
				
				if (strlen($req['invally']) > 0)
					$list->setInvAlly($req['invally']);
				
				if (strlen($req['invweapon']) > 0)
					$list->setInvWeapon($req['invweapon']);
				
				if (strlen($req['invcount']) > 0)
					$list->setInvCount($req['invcount']);
				
				if (strlen($req['itemdropped']) > 0)
					$list->setItemDropped($req['itemdropped']);
				
				if (strlen($req['itemdestroyed']) > 0)
					$list->setItemDestroyed($req['itemdestroyed']);
				
				if (strlen($req['commcnt']) > 0)
					$list->setCommentCount($req['commcnt']);
				
				if (strlen($req['daterange']) > 0)
					$list->setDateRange($req['daterange']);
					
				// save session data
				$shareUrl = "?a=search&p=adv_search&act=go";
				$shareUrlPieces = array();
				foreach ($req as $name => $val)
				{
					switch (strtolower($name))
					{
						case "a": break;
						case "p": break;
						case "act": break;
						case "submit": break;
						case "phpsessid": break;
						case "page": break;
						default: 
							if (preg_match("/^edk/i", $name) == 0)
							{
								$_SESSION[$name] = $val;
								
								if (strlen($val) > 0)
									$shareUrlPieces[] = strtolower($name)."=".urlencode($val);
							}
					}			
				}
				
				if (count($shareUrlPieces) > 0)
					$shareUrl .= "&".implode("&", $shareUrlPieces);
			}
			else
			{
				// populate filter options from saved session
				$killperpage = $_SESSION['killperpage'];
				
				$list->setDestrShpClass($_SESSION['destroyedshipclass']);
				$list->setInvShpClass($_SESSION['invshipclass']);
				$list->setSysRange($_SESSION['sysrange']);
				
				if (strlen($_SESSION['victimname']) > 0)
					$list->setVictimName($_SESSION['victimname']);
				
				if (strlen($_SESSION['victimcorp']) > 0)
					$list->setVictimCorp($_SESSION['victimcorp']);
				
				if (strlen($_SESSION['victimally']) > 0)
					$list->setVictimAlly($_SESSION['victimally']);
				
				if (strlen($_SESSION['destroyedship']) > 0)
					$list->setDestroyedShip($_SESSION['destroyedship']);
				
				if (strlen($_SESSION['system']) > 0)
					$list->setSystem($_SESSION['system']);
				
				if (strlen($_SESSION['const']) > 0)
					$list->setConstellation($_SESSION['const']);
				
				if (strlen($_SESSION['region']) > 0)
					$list->setRegion($_SESSION['region']);
				
				if (strlen($_SESSION['invship']) > 0)
					$list->setInvShip($_SESSION['invship']);
				
				if (strlen($_SESSION['invpilot']) > 0)
					$list->setInvPilot($_SESSION['invpilot']);
				
				if (strlen($_SESSION['invcorp']) > 0)
					$list->setInvCorp($_SESSION['invcorp']);
				
				if (strlen($_SESSION['invally']) > 0)
					$list->setInvAlly($_SESSION['invally']);
				
				if (strlen($_SESSION['invweapon']) > 0)
					$list->setInvWeapon($_SESSION['invweapon']);
				
				if (strlen($_SESSION['invcount']) > 0)
					$list->setInvCount($_SESSION['invcount']);
				
				if (strlen($_SESSION['itemdropped']) > 0)
					$list->setItemDropped($_SESSION['itemdropped']);
				
				if (strlen($_SESSION['itemdestroyed']) > 0)
					$list->setItemDestroyed($_SESSION['itemdestroyed']);
				
				if (strlen($_SESSION['commcnt']) > 0)
					$list->setCommentCount($_SESSION['commcnt']);
				
				if (strlen($_SESSION['daterange']) > 0)
					$list->setDateRange($_SESSION['daterange']);
			}
			
			$combined = config::get('adv_search_show_combined') or "default";
			$error_handling = config::get('adv_search_error_handling') or "continue";
			
			if($combined == "always" or (config::get('show_comb_home') and $combined == "default"))
			{
				if(ALLIANCE_ID > 0) $list->addCombinedAlliance(ALLIANCE_ID);
				if(CORP_ID > 0) $list->addCombinedCorp(CORP_ID);
				if(PILOT_ID > 0) $list->addCombinedPilot(PILOT_ID);
			}
			
			// add page splitter
			$pagesplitter = new PageSplitter($list->getCount(), $killperpage);
			$list->setPageSplitter($pagesplitter);
			
			// prepare list table
			$table = new KillListTable($list);
			$table->setDayBreak(false);
			if (method_exists($table, "setCombined") and ($combined == "always" or (config::get('show_comb_home') and $combined == "default")))
				$table->setCombined(true);
				
			$errors = $list->getErrors();
			
			/* error handling */
			if (count($errors) and $error_handling == "halt")
			{
				// header
				$html .= "<div class=\"kb-date-header\">There was one or more errors</div>\n";
				
				// list errors
				$html .= "<ul>\n";
				foreach($errors as $error)
				{
					$html .= "<li>".$error.";</li>\n";
				}
				$html .= "</ul>\n";
			}
			else
			{
				if (count($errors) and $error_handling == "continue")
				{
					// header
					$html .= "<div class=\"kb-date-header\">There was one or more errors</div>\n";
					
					// list errors
					$html .= "<ul>\n";
					foreach($errors as $error)
					{
						$html .= "<li>".$error.";</li>\n";
					}
					$html .= "</ul>\n";
				}
				
				// header
				$html .= "<div class=kb-kills-header>Search results";
				
				// share url
				if ($shareUrl)
					$html .= " (<a href=\"".$shareUrl."\">share</a>)";
				
				$html .= "</div>\n";
		
				// generate html
				$html .= $table->generate();
				$html .= $pagesplitter->generate();
			}
		}
		else /* generate search options */
		{
			/* get ship classes */
			$kbShipClasses = array();
			
			$qry = new DBQuery();
			$qry->execute("SELECT * FROM `kb3_ship_classes` WHERE `scl_class` NOT LIKE 'POS%' AND `scl_class` NOT LIKE 'Drone' ORDER BY `scl_class`");
			while ($sql_row = $qry->getRow())
				$kbShipClasses[$sql_row['scl_id']] = $sql_row['scl_class'];
				
			$smarty->assignByRef('kbShipClasses', $kbShipClasses);
		
			// generate from tpl
			$html.=$smarty->fetch(getcwd().'/mods/advanced_search/adv_search.tpl');
		}
		
		// generate page                     
		$html .= "<hr><p class=\"kb-subtable\" align=\"right\"><i>Advanced Search by Sonya Rayner<br>".ADV_SRCH_VERSION."</i></p>";
		
		/* return the generated content */
		return $html;
	}
}
?>