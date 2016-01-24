<?php
require_once("mods/advanced_search/class.search.php");

event::register("search_assembling", "AdvSearch::replace");


$modInfo['advanced_search']['name'] = "Advanced Search";
$modInfo['advanced_search']['abstract'] = "Adds many different custom search criterea to a custom search dialogue, creates links for sharing search result";
$modInfo['advanced_search']['about'] = "Version ".ADV_SRCH_VERSION." by <b>Sonya Rayner</b>, fixed by <b>Redhouse</b>, enhanced for better compatibility by <b>Salvoxia</b>";