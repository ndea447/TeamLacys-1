<?php
$emptyCart = "yes";
include "header.php";
include_once( "Checkout/CheckoutManager.php" );

$total = 0;
$CHECKOUT_MGR = CheckoutManager::getInstance();
$CART_MGR = CartManager::getInstance();
$ACCT_MGR = AccountManager::getInstance();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if($CART_MGR->isEmpty()){
		header("location: index.php");
	}else{
		$status = $CHECKOUT_MGR->checkout();
	}
    
    $grandTotal = $_POST['grandTotal'];
    $tax = $_POST['tax'];
    $shipping = $_POST['shipping'];
   
    
    if($status["success"] === 1){
		echo '<div class="row">' . "\n";

		echo '<div class="col-sm-4 col-sm-offset-4">' . "\n";
        
		echo '<h1>Checkout Completed!</h1>' . "\n";
        echo '<br/>' . "\n";
        echo '<h3>Item Details</h3>' . "\n";
        echo '<table class="table">' . "\n";
            
        echo '    <head>' . "\n";
        echo '        <th>Item</th>' . "\n";
        echo '        <th>Size</th>' . "\n";
        echo '        <th>Price</th>' . "\n";
        echo '        <th>Qty</th>' . "\n";
        echo '        <th>Color</th>' . "\n";
        echo '        <th>Amount</th>' . "\n";
        echo '    </head>' . "\n";
            
				$emailSummary = "";
                foreach( $CART_MGR->getItems() as $index => $item ){
                    $subTotal = 0;
                    //print_r( $item );
                    // print item to screen -nm
					if(isset($item['size'])){
						$size = $item['size'];
					}else{
						$size = "N/A";
					}
					$emailSummary .= $item['name'] . " " . $size . " $" . number_format($item['price'], 2) . " x" . $item['quantity'] . " \n";
                    echo '<tr>' . "\n";
                    echo '<td>' . $item['name'] . '</td>' . "\n";
					
					echo '<td>' . $size . '</td>' . "\n";
					
                    echo '<td>$' . number_format($item['price']) . '</td>' . "\n";
                    echo '<td> x' . $item['quantity'] . '</td>' . "\n";
                    if(isset($item['color'])){
					echo '<td>' . $item['color'] . '</td>' . "\n";
                    }else{
                    echo '<td>N/A</td>' . "\n";
                    }
                    $subTotal += ($item['price'] * $item['quantity']);
                    $total += ($item['price'] * $item['quantity']);
                    echo '<td>$' . number_format($subTotal, 2) . '</td>';
                    echo '</tr>' . "\n";
                    
                }
				
				$emailSummary .= "\nTotal: " . number_format($total, 2);
				$emailSummary .= "\nConfirmation# " . $status['confirmation_code'];
            
            echo '<tr>' . "\n";
			
			echo '<td></td>' . "\n";
            echo '<td></td>' . "\n";
			echo '<td></td>' . "\n";
            echo '<td></td>' . "\n";
			echo '<td><strong>Sub Total</strong></td>' . "\n";
			echo '<td>$' . number_format($total, 2) . '</td>' . "\n";
            echo '<tr>' . "\n";
			echo '<td></td>' . "\n";
            echo '<td></td>' . "\n";
			echo '<td></td>' . "\n";
            echo '<td></td>' . "\n";
			echo '<td><strong>Tax</strong></td>' . "\n";
			echo '<td>$' . number_format($tax, 2) . '</td>' . "\n";
            echo '<tr>' . "\n";
			echo '<td></td>' . "\n";
            echo '<td></td>' . "\n";
			echo '<td></td>' . "\n";
            echo '<td></td>' . "\n";
			echo '<td><strong>Shipping</strong></td>' . "\n";
			echo '<td>$' . number_format($shipping, 2) . '</td>' . "\n";
            echo '<tr>' . "\n";
			echo '<td></td>' . "\n";
            echo '<td></td>' . "\n";
			echo '<td></td>' . "\n";
            echo '<td></td>' . "\n";
			echo '<td><strong>Total</strong></td>' . "\n";
			echo '<td>$' . number_format($grandTotal, 2) . '</td>' . "\n";    
            echo '</tr>' . "\n";
			echo '<tr>' . "\n";
			echo '<td><strong>Confirmation Code:</strong></td>' . "\n";
            echo '<td>' . $status['confirmation_code'] . '</td>' . "\n";
			echo '<td></td>' . "\n";
			echo '<td></td>' . "\n";
			echo '<td></td>' . "\n";
            echo '<td></td>' . "\n";
			
            echo '</tr>' . "\n";
            echo '<tr>' . "\n";
            echo '<td></td>' . "\n";
            echo '<td></td>' . "\n";
            echo '<td></td>' . "\n";
            echo '<td></td>' . "\n";
            echo '<td></td>' . "\n";
            echo '<td></td>' . "\n";
                
            echo '</tr>' . "\n";
        echo '</table>' . "\n";
        $CART_MGR->emptyCart();
        $CHECKOUT_MGR->getNewSummary();
		
		$headers = 'From: sales@lacys.us';
		$to = $ACCT_MGR->getEmail();
		$subject = "Confirmation of Purchase";
		$body = "Thank you for your purchase " . $ACCT_MGR->getFirstName() . ",\n
				\n" . $emailSummary;
				
		
			
		//mail($to, $subject, $body, $headers);
		

		
        //header("location: index.php");
    }else{
        echo '<div class="row">';
            echo '<div class="col-sm-4 col-sm-offset-1">';
            echo '</div>';
            echo '<div class="col-sm-4 col-sm-offset-1">';
                foreach($status["errors"] as $key => $value) 
                    {
                        echo '<li>' . $value . '</li>'; 
                    }
                    echo '</ul>';
            echo '</div>';
        echo '</div>';
    }
}
?>

        
        
    </div>
    
</div>

<?php
include "footer.php";
?>