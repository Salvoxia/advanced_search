<?php

/* includes */
require_once('common/includes/class.kill.php');
require_once('common/includes/class.pagesplitter.php');
require_once('class.searchquery.php');

/* advanced kill list - mimics the KillList class so it could work with KillListTable and PageSplitter */
class AdvKillList
{
    function AdvKillList()
    {
        $this->qry_ = new SearchQuery();
        
		$this->destrclass_ = "all";
		$this->invclass_ = "all";
		$this->sysrange_ = 0;
		$this->startindex_ = 0;
		$this->killperpage_ = 25;
		
		// error list
		$this->errors = array();
        
        // old values, remove the unnecessary ones
        $this->killpointer_ = 0;
        $this->offset_ = 0;
        $this->killcounter_ = 0;
        $this->realkillcounter_ = 0;
        $this->ordered_ = true;
        $this->walked = false;
        $this->executed = false;
    }
    
	/* for debug purposes * /
	public static function showDebugBT()
	{
		$bt = debug_backtrace();
		echo "<ul>";
		
		foreach ($bt as $trace)
		{
			$file = $trace['file'];
			$file = substr($file, strrpos($file, "\\") - strlen($file) + 1);
			
			$line = $trace['line'];
	        $function = $trace['function'];
	        $class = $trace['class'];
	        $type = $trace['type'];
			
			echo "<li>".$class.($type == "::" ? "::" : (strlen($class) > 0 ? "->" : "")).$function."() in $file @ $line</li>";
		}
		
		echo "</ul>";
	}//*/
    
    function findAssociatedSystems($sname, $jrange)
	{	
		$ssql = "SELECT sys_id, sys_name FROM kb3_systems WHERE ";
		
		$ssys = explode(",", $sname);
		$where = array();
		foreach ($ssys as $sys)
			$where[] = "lower( sys_name ) LIKE lower( '%".trim($sys)."%' )";
			
		$ssql .= implode(" OR ", $where);
		
		$qry = new DBQuery();
		$qry->execute($ssql);
		
		$systems = array();
		while ($srow = $qry->getRow())
			$systems[$srow['sys_name']] = $srow['sys_id'];
			
		unset($qry);
		
		for ($i = 0; $i < $jrange; $i++)
		{
			$ssql = "SELECT sys_id, sys_name 
						FROM `kb3_system_jumps` 
							INNER JOIN kb3_systems ON sjp_to = sys_id 
						WHERE sjp_from IN (".implode(", ", $systems).")";
						
			$qry = new DBQuery();
			$qry->execute($ssql);
			while ($srow = $qry->getRow())
				$systems[$srow['sys_name']] = $srow['sys_id'];
				
			unset($qry);
		}
		
		return $systems;
	}
	
	function breakUpAndGetIDs($field, $dbfieldid, $dbfieldname, $dbtable)
	{
		$sval = explode(",", $field);
		
		$where = array();
		
		foreach($sval as $val)
			$where[] = "lower( ".$dbfieldname." ) LIKE lower( '".trim($val)."' )";
		
		$ssql = "SELECT ".$dbfieldid." FROM ".$dbtable." WHERE ".implode(" OR ", $where);
		$qry = new DBQuery();
		$qry->execute($ssql);
		
		$sid = array();
		while ($srow = $qry->getRow())
			$sid[] = $srow[$dbfieldid];
		
		return $sid;
	}
    
	function setDestrShpClass($shpclass)
	{
		$this->destrclass_ = $shpclass;
	}
	
	function setInvShpClass($shpclass)
	{
		$this->invclass_ = $shpclass;
	}
		
	function setSysRange($sysrange)
	{
		$this->sysrange_ = $sysrange;
	}
	
	function setSystem($system)
	{
		$arr = $this->findAssociatedSystems(trim(str_replace(array('*', '&'), array('%', ','), $system)), $this->sysrange_);
		if (count($arr) > 0)
	 		$this->system_ = $arr;
		else
			$this->errors[] = "No systems matching \"".$system."\" found";
	}
													
	function setVictimCorp($victimcorp)
	{
		$arr = $this->breakUpAndGetIDs(trim(str_replace(array('*', '&'), array('%', ','), $victimcorp)), "crp_id", "crp_name", "kb3_corps");
		if (count($arr) > 0)
			$this->victimcorp_ = $arr;
		else
			$this->errors[] = "No corps matching \"".$victimcorp."\" found";
	}
		
	function setVictimAlly($victimally)
	{
		$arr = $this->breakUpAndGetIDs(trim(str_replace(array('*', '&'), array('%', ','), $victimally)), "all_id", "all_name", "kb3_alliances");
		if (count($arr) > 0)
			$this->victimally_ = $arr;
		else
			$this->errors[] = "No alliances matching \"".$victimally."\" found";
	}
		
	function setVictimName($victimname)
	{
		$arr = $this->breakUpAndGetIDs(trim(str_replace(array('*', '&'), array('%', ','), $victimname)), "plt_id", "plt_name", "kb3_pilots");
		if (count($arr) > 0)
			$this->victimname_ = $arr;
		else
			$this->errors[] = "No players matching \"".$victimname."\" found";
	}
		
	function setDestroyedShip($destroyedship)
	{
		$arr = $this->breakUpAndGetIDs(trim(str_replace(array('*', '&'), array('%', ','), $destroyedship)), "shp_id", "itemShips.typeName", "kb3_ships INNER JOIN kb3_invtypes AS itemShips ON shp_id = itemShips.typeID");
		if (count($arr) > 0)
			$this->destroyedship_ = $arr;
		else
			$this->errors[] = "No ships matching \"".$destroyedship."\" found";
	}
		
	function setRegion($region)
	{	
		$arr = $this->breakUpAndGetIDs(trim(str_replace(array('*', '&'), array('%', ','), $region)), "reg_id", "reg_name", "kb3_regions");
		if (count($arr) > 0)
			$this->region_ = $arr;
		else
			$this->errors[] = "No region names matching \"".$region."\" found";
	}
		
	function setConstellation($const)
	{
		$arr = $this->breakUpAndGetIDs(trim(str_replace(array('*', '&'), array('%', ','), $const)), "con_id", "con_name", "kb3_constellations");
		if (count($arr) > 0)
			$this->const_ = $arr;
		else
			$this->errors[] = "No constellation names matching \"".$const."\" found";
	}
		
	function setInvShip($invship)
	{
		$vals = explode("&", $invship);
		$this->invship_ = array();
		
		foreach($vals as $val)
		{
			$arr = $this->breakUpAndGetIDs(trim(str_replace('*', '%', $val)), "shp_id", "itemShips.typeName", "kb3_ships INNER JOIN kb3_invtypes AS itemShips ON shp_id = itemShips.typeID");
			if (count($arr) > 0)
				$this->invship_[] = $arr;
			else
				$this->errors[] = "No ships matching \"".$val."\" found";
		}
	}
		
	function setInvWeapon($invweapon)
	{
		$vals = explode("&", $invweapon);
		$this->invweapon_ = array();
		
		foreach($vals as $val)
		{
			$arr = $this->breakUpAndGetIDs(trim(str_replace('*', '%', $val)), "typeID", "typeName", "kb3_invtypes");
			if (count($arr) > 0)
				$this->invweapon_[] = $arr;
			else
				$this->errors[] = "No items matching \"".$val."\" found";
		}
	}
								
	function setInvCorp($invcorp)
	{
		$vals = explode("&", $invcorp);
		$this->invcorp_ = array();
		
		foreach($vals as $val)
		{
			$arr = $this->breakUpAndGetIDs(trim(str_replace('*', '%', $val)), "crp_id", "crp_name", "kb3_corps");
			if (count($arr) > 0)
				$this->invcorp_[] = $arr;
			else
				$this->errors[] = "No corps matching \"".$val."\" found";
		}
	}
		
	function setInvAlly($invally)
	{
		$vals = explode("&", $invally);
		$this->invally_ = array();
		
		foreach($vals as $val)
		{
			$arr = $this->breakUpAndGetIDs(trim(str_replace('*', '%', $val)), "all_id", "all_name", "kb3_alliances");
			if (count($arr) > 0)
				$this->invally_[] = $arr;
			else
				$this->errors[] = "No alliances matching \"".$val."\" found";
		}
	}
		
	function setInvPilot($invpilot)
	{
		$vals = explode("&", $invpilot);
		$this->invpilot_ = array();
		
		foreach($vals as $val)
		{
			$arr = $this->breakUpAndGetIDs(trim(str_replace('*', '%', $val)), "plt_id", "plt_name", "kb3_pilots");
			if (count($arr) > 0)
				$this->invpilot_[] = $arr;
			else
				$this->errors[] = "No pilots matching \"".$invpilot."\"";
		}
	}
							
	function setItemDestroyed($itemdestroyed)
	{
		$vals = explode("&", $itemdestroyed);
		$this->itemdestroyed_ = array();
		
		foreach($vals as $val)
		{
			$arr = $this->breakUpAndGetIDs(trim(str_replace('*', '%', $val)), "typeID", "typeName", "kb3_invtypes");
			if (count($arr) > 0)
				$this->itemdestroyed_[] = $arr;
			else
				$this->errors[] = "No items matching \"".$itemdestroyed."\" found";
		}
	}
							
	function setItemDropped($itemdropped)
	{
		$vals = explode("&", $itemdropped);
		$this->itemdropped_ = array();
		
		foreach($vals as $val)
		{
			$arr = $this->breakUpAndGetIDs(trim(str_replace('*', '%', $val)), "typeID", "typeName", "kb3_invtypes");
			if (count($arr) > 0)
				$this->itemdropped_[] = $arr;
			else
				$this->errors[] = "No items matching \"".$itemdropped."\" found";
		}
	}
	
	function setInvCount($invcount)
	{
		$vals = explode(",", $invcount);
		$involved = array();
		$where = array();
		foreach($vals as $val)
		{
			if(strpos($val, '-'))
			{
				$varr = explode("-", $val);
				
				// add value range to partial filters
				if (count($varr) > 1 and is_numeric($varr[0]) and is_numeric($varr[1]))
					$where[] = "(ind_cnt >= ".$varr[0]." AND ind_cnt <= ".$varr[1].")";
			}
			else
			{
				if (is_numeric($val))
					$involved[] = $val;
				else
					$this->errors[] = "Non-numeric symbol found in field \"Involved Count\" in \"".$val."\"";
			}
		}
		
		// construct partial filter from value list
		if (count($involved) > 0)
			$where[] = "ind_cnt IN (".implode(", ", $involved).")";
		
		if (count($where) > 0)
			$this->invcount_ = "(".implode(" OR ", $where).")";
		else
			$this->errors[] = "No numeric entries found in field \"Involved Count\"";
	}
	
	function setCommentCount($commcnt)
	{
		$vals = explode(",", $commcnt);
		$comments = array();
		$where = array();
		foreach($vals as $val)
		{
			if(strpos($val, '-'))
			{
				$varr = explode("-", $val);
				
				// add value range to partial filters
				if (count($varr) > 1 and is_numeric($varr[0]) and is_numeric($varr[1]))
					$where[] = "(com_count >= ".$varr[0]." AND com_count <= ".$varr[1].")";
			}
			else
			{
				if (is_numeric($val))
					$comments[] = $val;
				else
					$this->errors[] = "Non-numeric symbol found in field \"Involved Count\", in \"".$val."\"";$this->errors[] = "Non-numeric symbol found in field \"Comment Count\", in \"".$val."\""; //rm
			}
		}
		
		// construct partial filter from value list
		if (count($comments) > 0)
			$where[] = "com_count IN (".implode(", ", $comments).")";   //rm
		
		if (count($where) > 0)
			$this->commcnt_ = "(".implode(" OR ", $where).")";
		else
			$this->errors[] = "No numeric entries found in field \"Comment Count\"";  //rm
	}
	
	function setDateRange($daterange)
	{
		$vals = explode(",", $daterange);
		$dates = array();
		$where = array();
		foreach($vals as $val)
		{
			if(strpos($val, '-'))
			{
				$varr = explode("-", $val);
				
				$ts1 = strtotime(str_replace('.', '-', $varr[0]));
				$ts2 = strtotime(str_replace('.', '-', $varr[1]));
				
				// add value range to partial filters
				if (count($varr) > 1 and $ts1 !== false and $ts2 !== false)
					$where[] = "(kll_timestamp >= ".date('\'Y-m-d\'', $ts1)." AND kll_timestamp < ".date('\'Y-m-d\'', $ts2+3600*24).")";
			}
			else
			{
				$ts = strtotime(str_replace('.', '-', $val));
				
				if ($ts !== false)
					$dates[] = date('\'Y-m-d%\'', $ts);
				else
					$this->errors[] = "Non-date symbol found in field \"Date Range\", in \"".$val."\"";
			}
		}
		
		// construct partial filter from value list
		foreach($dates as $val)
			$where[] = "kll_timestamp LIKE ".$val;
		if (count($where) > 0)
			$this->daterange_ = "(".implode(" OR ", $where).")";
		else
			$this->errors[] = "No numeric entries found in field \"Date Range\"";
	}

    function execQuery()
    {
        if (!$this->executed)
        {
			$this->sql_ = "	SELECT SQL_CALC_FOUND_ROWS kills.kll_id as 'ID',
								kll_timestamp as 'Time',
								pilots.plt_id as 'Pilot_ID',
								pilots.plt_name as 'Name',
								pilots.plt_externalid as 'Pilot_Ext_ID',
								corps.crp_name as 'Corp',
								corps.crp_id as 'Corp_ID',
								alliances.all_name as 'Ally',
								alliances.all_id as 'Ally_ID',
								fbpilots.plt_id as 'FB_Pilot_ID',
								fbpilots.plt_name as 'FB_Name',
								fbpilots.plt_externalid as 'FB_Pilot_Ext_ID',
								fbcorps.crp_id as 'FB_Corp_ID',
								fbcorps.crp_name as 'FB_Corp',
								fballys.all_id as 'FB_Ally_ID',
								fballys.all_name as 'FB_Ally',
								shp_id as 'Ship_ID',
								itemShips.typeName as 'Ship',
								itemShips.typeID as 'Ship_ID',
								scl_class as 'Class',
								scl_id as 'ClassID',
								scl_value as 'ShipValue',
								sys_id as 'System_ID',
								sys_name as 'System',
								sys_sec as 'SysSec',
								kll_dmgtaken as 'Damage',
								ind_cnt as 'Involved',
								kll_points as 'KillPoints',
								kll_external_id as 'ExtID',
								kll_isk_loss as 'ISK_Loss',
								com_count as 'Comments'
							FROM kb3_kills as kills
								INNER JOIN kb3_pilots AS pilots ON kll_victim_id = pilots.plt_id
								INNER JOIN kb3_corps AS corps ON kll_crp_id = corps.crp_id
								INNER JOIN kb3_alliances AS alliances ON kll_all_id = alliances.all_id
								INNER JOIN kb3_pilots AS fbpilots ON kll_fb_plt_id = fbpilots.plt_id
								INNER JOIN kb3_inv_detail AS fb ON fb.ind_kll_id = kills.kll_id AND fb.ind_plt_id = kll_fb_plt_id
								INNER JOIN kb3_corps AS fbcorps ON fbcorps.crp_id = fb.ind_crp_id
								INNER JOIN kb3_alliances AS fballys ON fballys.all_id = fb.ind_all_id
								INNER JOIN kb3_ships AS ships ON kll_ship_id = shp_id
								INNER JOIN kb3_ship_classes AS ship_classes ON shp_class = scl_id
								INNER JOIN kb3_invtypes AS itemShips ON shp_id = itemShips.typeID
								INNER JOIN kb3_systems AS systems ON kll_system_id = sys_id
								INNER JOIN kb3_constellations AS constellations ON sys_con_id = con_id
								INNER JOIN kb3_regions AS regions ON con_reg_id = reg_id
								INNER JOIN (SELECT ind_kll_id AS cnt_kll_id, count(*) AS ind_cnt FROM kb3_inv_detail GROUP BY ind_kll_id) AS cnt_kill ON cnt_kll_id = kll_id
								LEFT JOIN (SELECT kll_id AS com_kll_id, count( * ) AS com_count FROM kb3_comments GROUP BY com_kll_id ) AS comtemp ON com_kll_id = kll_id";
									
			// main query WHERE clause
			$WHERE = array();
		
			// add selectable filters
			if ($this->destrclass_ != "all")
				$WHERE[] = "scl_id = ".$this->destrclass_;
			
			if ($this->invclass_ != "all")
				$WHERE[] = "kills.kll_id IN (SELECT ind_kll_id FROM kb3_inv_detail 
											INNER JOIN kb3_ships ON ind_shp_id = shp_id
										WHERE shp_class = ".$this->invclass_.")";
			
			// add systems within range
			if (isset($this->system_))
				$WHERE[] = "sys_id IN (".implode(", ", $this->system_).")";
			
			// process search fields											
			if (isset($this->victimcorp_))
				$WHERE[] = "corps.crp_id IN (".implode(", ", $this->victimcorp_).")";
				
			if (isset($this->victimally_))
				$WHERE[] = "alliances.all_id IN (".implode(", ", $this->victimally_).")";
				
			if (isset($this->victimname_))
				$WHERE[] = "pilots.plt_id IN (".implode(", ", $this->victimname_).")";
				
			if (isset($this->destroyedship_))
				$WHERE[] = "shp_id IN (".implode(", ", $this->destroyedship_).")";
				
			if (isset($this->region_))
				$WHERE[] = "reg_id IN (".implode(", ", $this->region_).")";
				
			if (isset($this->const_))
				$WHERE[] = "con_id IN (".implode(", ", $this->const_).")";
				
			if (isset($this->invship_))
				foreach ($this->invship_ as $val)
					$WHERE[] = "kills.kll_id IN (SELECT ind_kll_id FROM kb3_inv_detail WHERE ind_shp_id IN (".implode(", ", $val)."))";
			
			if (isset($this->invweapon_))
				foreach($this->invweapon_ as $val)
					$WHERE[] = "kills.kll_id IN (SELECT ind_kll_id FROM kb3_inv_detail WHERE ind_wep_id IN (".implode(", ", $val)."))";
										
			if (isset($this->invcorp_))
				foreach($this->invcorp_ as $val)
					$WHERE[] = "kills.kll_id IN (SELECT ind_kll_id FROM kb3_inv_detail WHERE ind_crp_id IN (".implode(", ", $val)."))";
				
			if (isset($this->invally_))
				foreach($this->invally_ as $val)
					$WHERE[] = "kills.kll_id IN (SELECT ind_kll_id FROM kb3_inv_detail WHERE ind_all_id IN (".implode(", ", $val)."))";
				
			if (isset($this->invpilot_))
				foreach($this->invpilot_ as $val)
					$WHERE[] = "kills.kll_id IN (SELECT ind_kll_id FROM kb3_inv_detail WHERE ind_plt_id IN (".implode(", ", $val)."))";
									
			if (isset($this->itemdestroyed_))
				foreach($this->itemdestroyed_ as $val)
					$WHERE[] = "kills.kll_id IN (SELECT itd_kll_id FROM kb3_items_destroyed WHERE itd_itm_id IN (".implode(", ", $val)."))";
									
			if (isset($this->itemdropped_))
				foreach($this->itemdropped_ as $val)
					$WHERE[] = "kills.kll_id IN (SELECT itd_kll_id FROM kb3_items_dropped WHERE itd_itm_id  IN (".implode(", ", $val)."))";
			
			// make filters for involved count,  comment count and date range fields
			if (isset($this->invcount_))
				$WHERE[] = $this->invcount_;
				
			if (isset($this->commcnt_))
				$WHERE[] = $this->commcnt_;
				
			if (isset($this->daterange_))
				$WHERE[] = $this->daterange_;
				
			if (count($WHERE) > 0)
				$this->sql_ .= "\nWHERE ".implode("\nAND ", $WHERE);
			
			$this->sql_ .= "\nGROUP BY kills.kll_id\nORDER BY kll_timestamp DESC";
			
			$this->sql_ = str_replace("\t", "", $this->sql_); //die ("<pre>".$this->sql_."</pre>");
            $this->qry_->execute($this->sql_);
            
            if(!$this->plimit_ || $this->limit_)
				$this->count_ = $this->qry_->totalRecordCount();
			
			$this->executed = true;
        }
    }

    function getRow()
    {
        $this->execQuery();
        if ($this->plimit_ && $this->killcounter_ >= $this->plimit_)
        {
            // echo $this->plimit_." ".$this->killcounter_;
            return null;
        }

        $skip = $this->poffset_ - $this->killpointer_;
        if ($skip > 0)
        {
            for ($i = 0; $i < $skip; $i++)
            {
                $this->killpointer_++;
                $row = $this->qry_->getRow();
            }
        }

        $row = $this->qry_->getRow();

        return $row;
    }

    function getKill()
    {
        $this->execQuery();
		if ($this->plimit_ && $this->killcounter_ >= $this->plimit_)
		{
		// echo $this->plimit_." ".$this->killcounter_;
			return null;
		}

		if ($this->count_ == $this->qry_->recordCount())
			$skip = $this->poffset_ - $this->killpointer_;
		else
			$skip = 0;
		
		if ($skip > 0)
		{
			for ($i = 0; $i < $skip; $i++)
			{
				$this->killpointer_++;
				$row = $this->qry_->getRow();
			}
		}

		$row = $this->qry_->getRow();
		if ($row)
		{
			$this->killcounter_++;
			
			if ($row['ClassID'] != 2 && $row['ClassID'] != 3 && $row['ClassID'] != 11)
				$this->realkillcounter_++;
			
			if ($this->walked == false)
			{
				$this->killisk_ += $row['ISK_Loss'];
				$this->killpoints_ += $row['KillPoints'];
			}

			$kill = new KillWrapper($row['ID']);
            
			$arr = array('victimexternalid' => (int)$row['Pilot_Ext_ID'],
				'victimname' => $row['Name'],
				'victimid' => (int)$row['Pilot_ID'],
				'victimcorpid' => (int)$row['Corp_ID'],
				'victimcorpname' => $row['Corp'],
				'victimallianceid' => (int)$row['Ally_ID'],
				'victimalliancename' => $row['Ally'],
				'victimshipvalue' => $row['ShipValue'],
				'fbpilotid' => (int)$row['FB_Pilot_ID'],
				'fbpilotexternalid' => (int)$row['FB_Pilot_Ext_ID'],
				'fbcorpid' => (int)$row['FB_Corp_ID'],
				'fballianceid' => (int)$row['FB_Ally_ID'],
				'fbpilotname' => $row['FB_Name'],
				'fbcorpname' => $row['FB_Corp'],
				'fballiancename' => $row['FB_Ally'],
				'victimshipid' => (int)$row['Ship_ID'],
				'dmgtaken' => $row['Damage'],
				'timestamp' => $row['Time'],
				'solarsystemid' => (int)$row['System_ID'],
				'solarsystemname' => $row['System'],
				'solarsystemsecurity' => $row['SysSec'],
				'externalid' => (int)$row['ExtID'],
				'killpoints' => (int)$row['KillPoints'],
				'iskloss' => (float)$row['ISK_Loss']
				);
			
			$kill->setArray($arr);
			
			// Set the involved party count if it is known
			//if ($this->involved_)
				$kill->setInvolvedPartyCount((int)$row['Involved']);
				
			// Set the comment count if it is known
			//if ($this->comments_)
				$kill->setCommentCount((int)$row['Comments']);
				
			if (isset($this->_tag))
			{
				$kill->_tag = $this->_tag;
			}
			
			return $kill;
		}
		else
		{
			$this->walked = true;
			return null;
		}
    }

    function getAllKills()
    {
        while ($this->getKill())
        {
        }
        $this->rewind();
    }

    function setPageSplitter($pagesplitter)
    {
        if (isset($_GET['page'])) $page = $_GET['page'];
        else $page = 1;
        $this->plimit_ = $pagesplitter->getSplit();
        $this->poffset_ = ($page * $this->plimit_) - $this->plimit_;
    }

    function getCount()
    {
        $this->execQuery();
        return $this->qry_->recordCount();
    }

    function getRealCount()
    {
        $this->execQuery();
        return $this->qry_->recordCount();
    }

    function rewind()
    {
        $this->qry_->rewind();
        $this->killcounter_ = 0;
    }

    function addCombinedPilot($pilot)
    {
            if(is_numeric($pilot)) $this->comb_plt_[] = $pilot;
            else $this->comb_plt_[] = $pilot->getID();
    }

    function addCombinedCorp($corp)
    {
            if(is_numeric($corp)) $this->comb_crp_[] = $corp;
            else $this->comb_crp_[] = $corp->getID();
    }

    function addCombinedAlliance($alliance)
    {
            if(is_numeric($alliance)) $this->comb_all_[] = $alliance;
            else $this->comb_all_[] = $alliance->getID();
    }
    
    function getErrors()
    {
    	return $this->errors;
   	}
}

?>