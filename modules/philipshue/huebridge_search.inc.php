<?php
/*
* @version 0.1 (wizard)
*/
 global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $qry="1";
  // search filters
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['huebridge_qry'];
  } else {
   $session->data['huebridge_qry']=$qry;
  }
  if (!$qry) $qry="1";
  $sortby_huebridge="ID DESC";
  $out['SORTBY']=$sortby_huebridge;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM huebridge WHERE $qry ORDER BY ".$sortby_huebridge);
  if ($res[0]['ID']) {
   //paging($res, 100, $out); // search result paging
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
    $tmp=explode(' ', $res[$i]['UPDATED']);
    $res[$i]['UPDATED']=fromDBDate($tmp[0])." ".$tmp[1];
   }
   $out['RESULT']=$res;
  }
