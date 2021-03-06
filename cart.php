<?php
/**********************************INCLUDE*********************************** *
* **************************************************************************** */
include "header.php";
?>

<?php 
include_once( "Checkout/CheckoutManager.php" );

$orderTotal	= CheckoutManager::getOrderTotal();
?>

<div class="row">
	
    <div class="col-sm-10 col-sm-offset-1">
		<h1>Cart</h1>
	</div>
	<div class="col-sm-2 col-sm-offset-9">
		<form action="./index.php">
			<button class="btn btn-default btn-sm pull-right">Continue Shopping</button>
		</form>
		<br/>
		<br/>
	</div>
</div>
<div class="row">
<div class="col-sm-10 col-sm-offset-1">
	<table class="table table-condensed table-responsive">
		<th>Item</th>
		<th>Name</th>
		
		<th>Price</th>
		<th>Qty</th>
		<th>Size</th>
        <th>Color</th>
		<th>Total</th>
		<th></th>
		
		<?php
			if (CartManager::getInstance()->isEmpty()){
			
				echo "<tr><td>There are no items in your cart!</td></tr>";
				echo "</br></br></br></br></br></br></br>\n";
			
			}else{ // list all items in cart -nm
			
				foreach (CartManager::getInstance()->getItems() as $index => $item){
			
					echo '<tr>';
					echo '	<td>';
					echo '		<img src="' . $item['image_location'] . ' " width=96 height=144 />';
					echo '	</td>';
					
					echo '	<td>';
					echo '		<font>' . $item['name'] . '</font>';
					echo '	</td>';
					
					
					echo '	<td>';
					echo '		<font>$' . number_format($item['price'], 2) . '</font>';
					echo '	</td>';
					
					echo '	<td>';
					echo '		<font>' . $item['quantity'] . '</font>';
					echo '	</td>';
					
					echo '	<td>';
					if(isset($item['size'])){
						echo '		<font>' . $item['size'] . '</font>';
					}else{
						echo '		<font>N/A</font>';
					}
					echo '	</td>';
                    
                    echo '	<td>';
                    if(isset($item['color'])){
					echo '		<font>' . $item['color'] . '</font>';
                    }else{
                    echo '		<font>N/A</font>';
                    }
					echo '	</td>';
					
					echo '	<td>';
					echo '		<font>$' . number_format($item["price"] * $item["quantity"], 2) . '</font>';
					echo '	</td>';
					
					echo '	<td>';
					echo '	<form id="removeCartForm" method="POST" action="./php/cart/ajax/cart_remove_item.php">';
					echo '		<input type="hidden" name="id" 				value="' . $item['id'] 				. '"/>';
					echo '		<input type="hidden" name="name" 			value="' . $item['name'] 			. '"/>';
				
					echo '		<input type="hidden" name="image_location" 	value="' . $item['image_location'] 	. '"/>';
					echo '		<input type="hidden" name="price" 			value="' . $item['price'] 			. '"/>';
					echo '		<input type="hidden" name="index" 			value="' . $index 					. '"/>';
					echo '		<button type="submit" class="btn btn-danger btn-xs pull-right">Remove</button>';
					echo '	</form>';
					echo '	</td>';
					echo '</tr>';
				}
			}
		?>
	</table>
</div>
</div>
</div>
</div>

<div class="row">
	<div class="col-sm-3 col-sm-offset-8">
	<table class="table">
		
		<tr>
			<td><b>Subtotal:</b></td>
			<td><?php echo "$" . number_format( $orderTotal['sub'], 2 ); ?></td>
		</tr>
		
		<tr>
			<td><b>Sales Tax:</b></td>
			<td><?php echo "$" . number_format( $orderTotal['tax'], 2 ); ?></td>
		</tr>
		
		<tr>
			<td><b>Estimated Shipping:</b></td>
			<td><?php echo "$" . number_format( $orderTotal['ship'], 2 ); ?></td>
		</tr>
		
		<tr>
			<td><b>Total:</b></td>
			<td><?php echo "$" . number_format( $orderTotal['grand'], 2 ); 	?></td>
			<td>
				<form method="POST" action="<?php
					
					if (AccountManager::getInstance()->isLoggedIn()){
					
						echo './checkout.php';
					}else{
					
						echo './guestcheckout.php';
					}
				?>" >
					
					<?php 
						if (!CartManager::getInstance()->isEmpty()){
						
							echo '<button class="btn btn-success btn-sm" type="submit" name="beginCheckout" value="Checkout" id="cartCheckout">Check Out</button>';
						}
					?>
					
					<input type="hidden" name="total" value="<?php echo $orderTotal['grand']; ?>"/>
				</form>
			</td>
		</tr>
		
	</table>
	</br>
</div>
</div>


<?php
include "footer.php";
?>