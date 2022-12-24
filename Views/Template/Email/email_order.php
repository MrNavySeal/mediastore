<?php 

$order = $data['order']['order'];
$detail = $data['order']['detail'];
$amountData= $data['order']['amountData'];
$totalInfo = $amountData['totalInfo'];
$subtotal =$totalInfo['total']['subtotalCoupon'] >0 ? $totalInfo['total']['subtotalCoupon'] : $totalInfo['total']['subtotal'];
$subtotalCoupon = $totalInfo['total']['subtotal'];
 ?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Pedido</title>
	<style type="text/css">
		p{
			font-family: arial;letter-spacing: 1px;color: #7f7f7f;font-size: 12px;
		}
		hr{border:0; border-top: 1px solid #CCC;}
		h4{font-family: arial; margin: 0;}
		table{width: 100%; max-width: 600px; margin: 10px auto; border: 1px solid #CCC; border-spacing: 0;}
		table tr td, table tr th{padding: 5px 10px;font-family: arial; font-size: 12px;}
		#detalleOrden tr td{border: 1px solid #CCC;}
		.table-active{background-color: #CCC;}
		.text-center{text-align: center;}
		.text-right{text-align: right;}

		@media screen and (max-width: 470px) {
			.logo{width: 90px;}
			p, table tr td, table tr th{font-size: 9px;}
		}
	</style>
</head>
<body>
	<div>
		<br>
		<p class="text-center">Se ha generado un pedido, a continuación encontrará los datos.</p>
		<br>
		<hr>
		<br>
		<table>
			<tr>
				<td width="33.33%">
					<img src="<?= media();?>/images/uploads/icon.gif" alt="Logo" width="100px" height="100px">
				</td>
				<td width="33.33%">
					<div class="text-center">
						<h4><strong><?= $data['company']['name'] ?></strong></h4>
						<p>
							<?= $data['company']['addressfull']?> <br>
							Teléfono: <?= "+".$data['company']['phonecode']." ".$data['company']['phone'] ?> <br>
							Email: <?= $data['company']['email'] ?>
						</p>
					</div>
				</td>
				<td width="33.33%">
					<div class="text-right">
						<p>
							Pedido: <strong><?= $order['idorder'] ?></strong><br>
                            Fecha: <?= $order['date'] ?><br>
						</p>
					</div>
				</td>				
			</tr>
		</table>
		<table>
			<tr>
		    	<td width="140">Nombre:</td>
		    	<td><?= $order['firstname'].' '.$order['lastname'] ?></td>
		    </tr>
			<tr>
		    	<td>Email</td>
		    	<td><?= $order['email'] ?></td>
		    </tr>
		    <tr>
		    	<td>Teléfono</td>
		    	<td><?= $order['phone'] ?></td>
		    </tr>
		    <tr>
		    	<td>Dirección de envio:</td>
		    	<td><?= $order['country']?>/<?= $order['state']?>/<?= $order['city']?><br><?=$order['address']?><br><?=$order['postalcode']!="" ? '<br>Postal code - '.$order['postalcode'] : ""?></td>
		    </tr>
			<tr>
		    	<td>Notas:</td>
		    	<td><?= $order['note']?></td>
		    </tr>
		</table>
		<table>
		  <thead class="table-active">
		    <tr>
		      <th>Descripción</th>
		      <th class="text-right">Precio</th>
		      <th class="text-center">Cantidad</th>
		      <th class="text-right">Total</th>
		    </tr>
		  </thead>
		  <tbody id="detalleOrden">
		  	<?php 
		  		if(count($detail) > 0){
		  			$subtotal = 0;
		  			foreach ($detail as $product) {
						$subtotal+=$product['quantity']*$product['price'];
		  	 ?>
		    <tr>
		      <td><?=$product['name']?><br></td>
		      <td class="text-right"><?=formatNum($product['price'])?></td>
		      <td class="text-center"><?= $product['quantity'] ?></td>
		      <td class="text-right"><?= formatNum($product['price']*$product['quantity'])?></td>
		    </tr>
			<?php 		
				}
			} 
			?>
		  </tbody>
		  <tfoot>
				<?php if($totalInfo['total']['subtotalCoupon'] >0) {?>
		  		<tr>
		  			<th colspan="3" class="text-right">Subtotal:</th>
		  			<td class="text-right"><?= formatNum($subtotal)?></td>
		  		</tr>
				<tr>
		  			<th colspan="3" class="text-right">Cupón de descuento:</th>
		  			<td class="text-right"><?=$amountData['couponInfo']['code']." - ".$amountData['couponInfo']['discount']?>%</td>
		  		</tr>
				<tr>
		  			<th colspan="3" class="text-right">Subtotal:</th>
		  			<td class="text-right"><?= formatNum($subtotalCoupon)?></td>
		  		</tr>
				<?php }else{?>
				<tr>
		  			<th colspan="3" class="text-right">Subtotal:</th>
		  			<td class="text-right"><?= formatNum($subtotal)?></td>
		  		</tr>
				<?php }?>
				<tr>
		  			<th colspan="3" class="text-right">Envio</th>
					<?php if($totalInfo['shipping']['id'] == 3){?>
		  			<td class="text-right"><?=formatNum($totalInfo['shipping']['city']['value'])?></td>
					<?php }else{ ?>
						<td class="text-right"><?= formatNum($totalInfo['shipping']['value'])?></td>
					<?php }?>
		  		</tr>
		  		<tr>
		  			<th colspan="3" class="text-right">Total:</th>
		  			<td class="text-right"><?= formatNum($order['amount'])?></td>
		  		</tr>
		  </tfoot>
		</table>
		<div class="text-center">
			<h4>Gracias por tu compra!</h4>			
		</div>
	</div>									
</body>
</html>