<?php
/**********************************INCLUDE*********************************** *
* **************************************************************************** */


/**
 * This class manages a shopping cart.
 *
 * @author(s) nicholas malacarne <nicholas.malacarne@gmail.com>
 */
class CartManager {
    
    // session ID -nm
    const SESSION_NAME = "CART_MGR";
	
	// list of items currently in the cart -nm
	private $items_l = array();
    
    // constructor -nm
    private function __construct(){
        
		// do nothing -nm
		
    } // end constructor -nm
    
    // destructor -nm
    public function __destruct() {
        
        // serialize object to session -nm
        $_SESSION[ self::SESSION_NAME ] = serialize( $this );
        
        // close user session -nm
        session_write_close();
        
    } // end destructor -nm
	
	// add item to the cart -nm
	public function addItem( $aItem ){
		
		array_push( $this->items_l, $aItem );
		
	} // end method -nm
	
	public function getItemCount(){
	
		return count( $this->items_l );
	
	} // end method -nm
	
	public function __toString(){
	
		return "Cart(" . $this->getItemCount() . ")"; 
		
	} // end method -nm
    
    // init method -nm
    public static function init(){
        
		// Report simple running errors
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
		
		// start user session -nm
		session_start();

		 
		if ( isset( $_SESSION[ self::SESSION_NAME ] ) === TRUE ){
			 
			// unserialize and return the existing object -nm
			return unserialize( $_SESSION[ self::SESSION_NAME ] );
			 
		}else{
			 
			return new self();
		}
    } // end method -nm
    
} // end class -nm
