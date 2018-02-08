<?php
# Coded by Joel A. Ossi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET["q"]) && $_GET["q"] != "") {
    $domain = $_GET["q"];
    $domain= get_domain($domain);
   
    if(!$domain){
        echo "Invalid domain name";
        exit();
    }
    
    $records = false;
    try {
       $records = dns_get_record($domain, DNS_ANY);
    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
        
    }

    if ($records) {
        $shell_res_arr = array();
        $shell_final_arr=array();
        foreach ($records as $dr) {
            if ($dr["type"] == "NS") {

                $cmd = "dig axfr @" . $dr["target"] . " " . $domain;
                //$cmd=  "dig axfr " .$domain. " @".$dr["target"]  ;
                
                //echo $cmd;
               // exit();
                
                $shell_res = shell_exec($cmd);  
                //$shell_final_arr[]= "<<>> DiG " . php_uname('s') . " " . php_uname('v') . " <<>> " . $cmd . " </br>" . $shell_res . " </br></br>";
                $shell_final_arr[]=  $shell_res ;
            }
        }
        
        foreach($shell_final_arr as $fr){
            echo "<pre>".strip_tags($fr)."</pre></br>";
        }

    } else {
        echo "Error occured while getting  DNS records";
        exit();
    }
} else {
    echo "Required parameters missing ";
    exit();
}

//Get domain name from URL
function get_domain($url)
{
  $pieces = parse_url($url);
  $domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];
  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
    return $regs['domain'];
  }
  return false;
}
