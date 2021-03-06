<?php
/**********************************INCLUDE*********************************** *
* **************************************************************************** */

/**
 * Logs an account out. 
 *
 * @author(s) nicholas malacarne <nicholas.malacarne@gmail.com>
 */
session_start(); 
ob_start();
 
try {
	
	unset($_SESSION);
	session_destroy();
    
}catch( Exception $e ){
    
    $report = "";
    
    $report += $e->getFile();
    $report += $e->getLine();
    $report += $e->getMessage();
    
    echo $report;
}

exit();