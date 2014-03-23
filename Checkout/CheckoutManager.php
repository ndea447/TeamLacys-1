<?php

include __DIR__ . "/../db_connect.php"; 




class CheckoutManager{

    protected static $instance = null;

    protected function __construct()
    {
        
    }

    protected function __clone()
    {
        
    }

    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }
    
    
    public function validateCheckout($cardName,$cardNumber,$security,$expirationMonth, $expirationYear,$shipping,$street,$street2,$city,$state,$zip){
        
        $errors = array(); /* declare the array for later use */
        $success = 0;
        
        if(empty($cardName)){
            $errors[] = 'The card name cannot be blank.';
            
        }else{
            if(!ctype_alnum((str_replace(' ','',$cardName)))){
                $errors[] = 'The card name can only contain letters and digits.';
            }
        }
        
        if(!empty($cardNumber)){
            if(strlen($cardNumber) != 10){
				$errors[] = "The credit card number must be 10 digits.";
			}
        }else{
            $errors[] = 'The card number field cannot be blank.';
        }
        
        if(!empty($security)){
            if(!is_numeric($security)){
                $errors[]= 'The security code must be numeric only';
            }
            if(strlen($security) != 3){
                $errors[] = 'The security code must be 3 digits';
            }
        }else{
            $errors[] = 'The security code field cannot be blank';
        }
        
        
        if($shipping === "ship"){
            if(!empty($street)){
                
            }else{
                $errors[] = 'Street address cannot be blank';
            }
            
            if(!empty($city)){
                
            }else{
                $errors[] = 'City cannot be blank';
            }
            
            if(!empty($state)){
                
            }else{
                $errors[] = 'State cannot be blank';
            }
            
            if(!empty($zip)){
                
            }else{
                $errors[] = 'Zip code cannot be blank';
            }
        }
        
        if(!empty($errors)){
            return array("success" => $success,
                         "errors" => $errors);
        }else{
            $success = 1;
            return array("success" => $success,
                         "errors" => $errors);
        }
        
        
        
    }
    
    public function setCheckoutSummary($summary){
        $_SESSION['summary'] = $summary;
    }
    
    public function getNewSummary(){
        unset($_SESSION['summary']);
    }
    
    public function getTotal(){
        if(isset($_SESSION['summary']['total'])){
            return $_SESSION['summary']['total'];
        }
    }
    
    public function getCardType(){
        if(isset($_SESSION['summary']['cardType'])){
            return $_SESSION['summary']['cardType'];
        }
    }
    
    public function getCardName(){
        if(isset($_SESSION['summary']['name'])){
            return $_SESSION['summary']['name'];
        }
    }
    
    public function getCardNumber(){
        if(isset($_SESSION['summary']['number'])){
            return $_SESSION['summary']['number'];
        }
    }
    
    public function getCardSecurity(){
        if(isset($_SESSION['summary']['security'])){
            return $_SESSION['summary']['security'];
        }
    }
    
    public function getExpirationMonth(){
        if(isset($_SESSION['summary']['expirationMonth'])){
            return $_SESSION['summary']['expirationMonth'];
        }
    }
    
    public function getExpirationYear(){
        if(isset($_SESSION['summary']['expirationYear'])){
            return $_SESSION['summary']['expirationYear'];
        }
    }
	
	public function getExpirationDate(){
		return $this->getExpirationMonth() . "/" . $this->getExpirationYear();
	}
    
    public function getShippingOption(){
        if(isset($_SESSION['summary']['shipping'])){
            return $_SESSION['summary']['shipping'];
        }
    }
    
    public function getStreet1(){
        if(isset($_SESSION['summary']['street'])){
            return $_SESSION['summary']['street'];
        }
    }
    
    public function getStreet2(){
        if(isset($_SESSION['summary']['street2'])){
            return $_SESSION['summary']['street2'];
        }
    }
    
    public function getCity(){
        if(isset($_SESSION['summary']['city'])){
            return $_SESSION['summary']['city'];
        }
    }
    
    public function getState(){
        if(isset($_SESSION['summary']['state'])){
            return $_SESSION['summary']['state'];
        }
    }
    
    public function getZip(){
        if(isset($_SESSION['summary']['zip'])){
            return $_SESSION['summary']['zip'];
        }
    }
	
	public function getSaveCreditCard(){
		if(isset($_SESSION['summary']['saveCreditCard'])){
			return $_SESSION['summary']['saveCreditCard'];
		}
	}
    
    public function checkout(){
        global $DBH;
        $success = 0;
        $errors = array();
        $shippingId = null; //If the customer selected shipping this will get updated
        
        //shipping insert
        if($this->getShippingOption() === "ship"){
            $sql = "Insert into shipping(street_address1, street_address2, city, state,zip)
                    values (:street_address1, :street_address2,:city, :state, :zip)";
            $q = $DBH->prepare($sql);
            $q->execute(array(':street_address1'=>$this->getStreet1(),
                              ':street_address2'=>$this->getStreet2(),
                              ':city'=>$this->getCity(),
                              ':state'=>$this->getState(),
                              ':zip'=>$this->getZip()
                              ));
                              
            if(!$q)
            {
                //something went wrong, display the error
                $errors[] = "There was an issue with the database";
                return array("success" => $success,
                             "errors" => $errors);
                //echo mysql_error(); //debugging purposes, uncomment when needed
            }
            else
            {
                $shippingId = $DBH->lastInsertId();
                
            }
        }
        
        
        
        
        $raw_date = new DateTime();
        $date = $raw_date->format('Y-m-d');
        
        $confirmation_code = substr(md5(uniqid(rand(), true)), 16, 16);
        
        //purchase_summary insert
        $sql2 = "Insert into purchase_summary(amount_total, purchase_date, shipping_id, confirmation_code)
                    values (:amount_total, :purchase_date,:shipping_id, :confirmation_code)";
        $q2 = $DBH->prepare($sql2);
        $q2->execute(array(':amount_total'=>$this->getTotal(),
                          ':purchase_date'=>$date,
                          ':shipping_id'=>$shippingId,
						  ':confirmation_code'=>$confirmation_code
                          ));
    
        
        
        if(!$q2)
        {
            //something went wrong, display the error
            $errors[] = "There was an issue with the database";
            return array("success" => $success,
                         "errors" => $errors);
            //echo mysql_error(); //debugging purposes, uncomment when needed
        }
        else
        {
            $summaryId = $DBH->lastInsertId();
            
        }
        
        
        
        $CART_MGR = CartManager::getInstance();
		$ACCT_MGR = AccountManager::getInstance();	
        
		if($this->getSaveCreditCard() === "save"){
			$credit_sql = "Insert into credit_card_info(name_on_card, credit_card_number, security_code, expiration_date, card_type, expiration_month, expiration_year)
                        values (:name_on_card, :credit_card_number,:security_code, :expiration_date, :card_type, :expiration_month, :expiration_year)";
            $credit_query = $DBH->prepare($credit_sql);
            $credit_query->execute(array(':name_on_card'=>$this->getCardName(),
                              ':credit_card_number'=>$this->getCardNumber(),
                              ':security_code'=>$this->getCardSecurity(),
                              ':expiration_date'=>$this->getExpirationDate(),
                              ':card_type'=>$this->getCardType(),
							  ':expiration_month'=>$this->getExpirationMonth(),
							  ':expiration_year'=>$this->getExpirationYear()
                              ));
							  
			$creditId = $DBH->lastInsertId();
			
			$save_sql = "update customers set credit_card_id = ? where id = ?";
            $save_q = $DBH->prepare($save_sql);
            $save_q->execute(array($creditId, $ACCT_MGR->getId()));
		}
		
        // for each item in the cart -nm
        foreach( $CART_MGR->getItems() as $index => $item ){
            
            //purchase_details insert
            $sql3 = "Insert into purchase_details(customer_id, product_id, amount, quantity, size, purchase_summary_id)
                        values (:customer_id, :product_id,:amount, :quantity, :size, :purchase_summary_id)";
            $q3 = $DBH->prepare($sql3);
            $q3->execute(array(':customer_id'=>$ACCT_MGR->getId(),
                              ':product_id'=>$item['id'],
                              ':amount'=>$item['price'],
                              ':quantity'=>$item['quantity'],
                              ':size'=>$item['size'],
                              ':purchase_summary_id'=>$summaryId
                              ));
            
          
                        
            if(!$q3)
            {
                //something went wrong, display the error
                $errors[] = "There was an issue with the database";
                return array("success" => $success,
                             "errors" => $errors);
                //echo mysql_error(); //debugging purposes, uncomment when needed
            }
            
        }
        
        $success = 1;
        return array("success" => $success,
						"confirmation_code"=> $confirmation_code,
                         "errors" => $errors);
            
        
        
        
        
    }

}


?>
