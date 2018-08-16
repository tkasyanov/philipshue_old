<?php

require_once __DIR__ . '/lib/phue.php';

//$client = new \Client();
/**
* philipshue
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 22:08:36 [Aug 14, 2018])
*/
//
//
class philipshue extends module {




/**
* philipshue
*
* Module class constructor
*
* @access private
*/
function __construct() {
  $this->name="philipshue";
  $this->title="philipshue";
  $this->module_category="<#LANG_SECTION_DEVICES#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=1) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->data_source)) {
  $p["data_source"]=$this->data_source;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $data_source;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($data_source)) {
   $this->data_source=$data_source;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['DATA_SOURCE']=$this->data_source;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='huebridge' || $this->data_source=='') {
  if ($this->view_mode=='' || $this->view_mode=='search_huebridge') {
   $this->search_huebridge($out);
  }

     if ($this->view_mode == 'discover') {
         if ($this->config['API_LOG_DEBMES']) DebMes('Starting manual search for devices in the network', 'huebridge');
         $this->discover();
         if ($this->config['API_LOG_DEBMES']) DebMes('Manual search for devices in the network is finished', 'huebridge');
         $this->redirect('?');
     }

     if ($this->view_mode=='edit_huebridge') {
   $this->edit_huebridge($out, $this->id);
  }
  if ($this->view_mode=='delete_huebridge') {
   $this->delete_huebridge($this->id);
   $this->redirect("?data_source=huebridge");
  }
 }


    if ($this->view_mode == 'auth_huebridge') {
        DebMes('Starting manual update the properties of the device', 'auth_huebridge');
      //  $this->requestStatus($this->id);
        if ($this->config['API_LOG_DEBMES']) DebMes('Manual update the properties of the device is finished', 'auth_huebridge');
        $this->redirect('?');
    }



 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='huedevices') {
  if ($this->view_mode=='' || $this->view_mode=='search_huedevices') {
   $this->search_huedevices($out);
  }

     if ($this->view_mode == 'discover') {
         if ($this->config['API_LOG_DEBMES']) DebMes('Starting manual search for devices in the network', 'huedevices');
         $this->discover_devices($_GET["huebridge"]);
         if ($this->config['API_LOG_DEBMES']) DebMes('Manual search for devices in the network is finished', 'huedevices');
         $this->redirect('?');
     }
  if ($this->view_mode=='edit_huedevices') {
   $this->edit_huedevices($out, $this->id);
  }
 }
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
    if ($this->ajax) {
        global $op;
        if ($op == 'auth') {
            $dip = $_GET['dip'];
            $bid=$_GET['bid'];
            $maxTries = 30;
            $client = new Phue($dip);
            for ($i = 1; $i <= $maxTries; ++$i) {
                try {
                   $response= $client->newUser();
                    if ($response->error) {
                        if ($response->error->type!=101) {
                            echo "\n\n", "Failure to create user. Please try again! error: " . $response->error->type;
                            break;
                        }
                    }
                        else if ($response->success) {

                            //  print_r($client->loadInfo());
                            SQLExec("UPDATE huebridge SET USERNAME='".$response->success->username."' WHERE ID=".$bid);

                             echo "\n\n", "Successfully created new user: {$response->success->username}", "\n\n";

                            break;
                        }
                } catch ( Exception $e) {

                    echo "\n\n", "Failure to create user. Please try again!",
                    "\n", "Reason: {$e->getMessage()}", "\n\n";

                    break;
                }

                sleep(1);
            }
            echo "\n\n", "Failure to create user. Please try again! error: " . $response->error->type;


        }
    }



 $this->admin($out);
}

    function discover_devices($huebridge = '')

    {
        $dev = SQLSelectOne("SELECT * FROM huebridge WHERE  ID='".$huebridge."'");
        $bridge = new PHue($dev["IP"],$dev["USERNAME"]);

       $listLight=($bridge->loadLights());
       foreach ($listLight as $light){
           if (strlen($light['uniqueid'])<25) continue;
           $dev_rec = SQLSelectOne("SELECT * FROM huedevices WHERE  UUID='".$light['uniqueid']."' AND BRIDGEID='".$huebridge."'");
           if ($dev_rec['ID']) {
               $dev_rec['TITLE'] = $light['name'];
               $dev_rec['UPDATED'] = date('Y-m-d H:i:s');
               $dev_rec['JSON_STATE'] =json_encode ($light['state']);
               SQLUpdate('huedevices', $dev_rec);
           } else{
               $dev_rec = array();
               $dev_rec['BRIDGEID']=$huebridge;
               $dev_rec['MODELID']=$light['modelid'];
               $dev_rec['UUID'] = $light['uniqueid'];
               $dev_rec['TITLE'] = $light['name'];
               $dev_rec['UPDATED'] = date('Y-m-d H:i:s');
               $dev_rec['JSON_STATE'] =json_encode ($light['state']);
               $dev_rec['ID'] = SQLInsert('huedevices', $dev_rec);

           }
       }
        $listSensors=($bridge->loadSensors());
        foreach ($listSensors as $sensor){
            if (strlen($sensor['uniqueid'])<25) continue;
            $dev_rec = SQLSelectOne("SELECT * FROM huedevices WHERE  UUID='".$sensor['uniqueid']."' AND BRIDGEID='".$huebridge."'");
            if ($dev_rec['ID']) {
                $dev_rec['TITLE'] = $sensor['name'];
                $dev_rec['UPDATED'] = date('Y-m-d H:i:s');
                $dev_rec['JSON_STATE'] =json_encode ($sensor['state']);
                SQLUpdate('huedevices', $dev_rec);
            } else{
                $dev_rec = array();
                $dev_rec['BRIDGEID']=$huebridge;
                $dev_rec['MODELID']=$sensor['modelid'];
                $dev_rec['UUID'] = $sensor['uniqueid'];
                $dev_rec['TITLE'] = $sensor['name'];
                $dev_rec['UPDATED'] = date('Y-m-d H:i:s');
                $dev_rec['JSON_STATE'] =json_encode ($sensor['state']);
                $dev_rec['ID'] = SQLInsert('huedevices', $dev_rec);
            }
        }
     }

    /**
   Ищем бриджи
     */
    function discover($ip = '')
    {

        $this->getConfig();

        echo "Philips Hue Bridge Finder", "\n\n";
        echo "Checking meethue.com if the bridge has phoned home:", "\n";
        $response = @file_get_contents('http://www.meethue.com/api/nupnp');

        // Don't continue if bad response
        if ($response === false) {
            echo "\tRequest failed. Ensure that you have internet connection.";
            exit(1);
        }

        echo "\tRequest succeeded", "\n\n";

        // Parse the JSON response
        $bridges = json_decode($response);
        echo "Найдено ", count($bridges), "bridges \n";

        // Iterate through each bridge
        foreach ($bridges as $key => $bridge) {
            echo "\tBridge #", ++$key, "\n";
            echo "\t\tID: ", $bridge->id, "\n";
            echo "\t\tInternal IP Address: ", $bridge->internalipaddress, "\n";
            echo "\t\tMAC Address: ", $bridge->macaddress, "\n";
            echo "\n";

            $dev_rec = SQLSelectOne("SELECT * FROM huebridge WHERE  MAC='".$bridge->id."'");

            if ($dev_rec['ID']) {
                $dev_rec['IP'] = $bridge->internalipaddress;
                $dev_rec['UPDATED'] = date('Y-m-d H:i:s');
                SQLUpdate('huebridge', $dev_rec);
            } else{
                $dev_rec = array();
                $dev_rec['IP'] = $bridge->internalipaddress;
                $dev_rec['MAC'] = $bridge->id;
                $dev_rec['TITLE'] = 'Bridge ('.$bridge->id.')';
                $dev_rec['UPDATED'] = date('Y-m-d H:i:s');
                $dev_rec['ID'] = SQLInsert('huebridge', $dev_rec);

            }
        }
    }








        /**
* huebridge search
*
* @access public
*/
 function search_huebridge(&$out) {
  require(DIR_MODULES.$this->name.'/huebridge_search.inc.php');
 }
/**
* huebridge edit/add
*
* @access public
*/
 function edit_huebridge(&$out, $id) {
  require(DIR_MODULES.$this->name.'/huebridge_edit.inc.php');
 }
/**
* huebridge delete record
*
* @access public
*/
 function delete_huebridge($id) {
  $rec=SQLSelectOne("SELECT * FROM huebridge WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM huebridge WHERE ID='".$rec['ID']."'");
 }
/**
* huedevices search
*
* @access public
*/
 function search_huedevices(&$out) {
  require(DIR_MODULES.$this->name.'/huedevices_search.inc.php');
 }
/**
* huedevices edit/add
*
* @access public
*/
 function edit_huedevices(&$out, $id) {
  require(DIR_MODULES.$this->name.'/huedevices_edit.inc.php');
 }
 function propertySetHandle($object, $property, $value) {
   $table='huebridge';
   $properties=SQLSelect("SELECT ID FROM $table WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'");
   $total=count($properties);
   if ($total) {
    for($i=0;$i<$total;$i++) {
     //to-do
    }
   }
 }
 function processCycle() {
  //to-do
 }
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS huebridge');
  SQLExec('DROP TABLE IF EXISTS huedevices');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data) {
/*
huebridge - 
huedevices - 
*/
  $data = <<<EOD
 huebridge: ID int(10) unsigned NOT NULL auto_increment
 huebridge: TITLE varchar(100) NOT NULL DEFAULT ''
 huebridge: USERNAME varchar(255) NOT NULL DEFAULT ''
 huebridge: MAC varchar(25) NOT NULL DEFAULT ''
 huebridge: IP varchar(25) NOT NULL DEFAULT ''
 huebridge: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 huebridge: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
 huebridge: LINKED_METHOD varchar(100) NOT NULL DEFAULT ''
 huebridge: UPDATED datetime
 huedevices: ID int(10) unsigned NOT NULL auto_increment
 huedevices: TITLE varchar(100) NOT NULL DEFAULT ''
 huedevices: VALUE varchar(255) NOT NULL DEFAULT ''
 huedevices: BRIGHEID int(10) NOT NULL DEFAULT '0'
 huedevices: MODELID varchar(25) NOT NULL DEFAULT ''
 huedevices: JSON_STATE varchar(500) NOT NULL DEFAULT ''
 huedevices: UPDATED datetime
EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgQXVnIDE0LCAyMDE4IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
