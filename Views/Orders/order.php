<?php 
headerAdmin($data);
$order = $data['orderdata'];
$detail = $data['orderdetail'];

if($order['amountdata'] !=""){
    $amountData = json_decode($order['amountdata'],true);
    $totalInfo = $amountData['totalInfo'];
    $subtotal =$totalInfo['total']['subtotalCoupon'] >0 ? $totalInfo['total']['subtotalCoupon'] : $totalInfo['total']['subtotal'];
    $subtotalCoupon = $totalInfo['total']['subtotal'];
}

$total=0;
$company = $data['company'];
?>

<div class="body flex-grow-1 px-3" id="<?=$data['page_name']?>">
    <div id="modalItem"></div>
    <div class="container-lg">
        <div class="card">
            <div class="card-body">
                <div id="orderInfo">
                    <div class="d-flex justify-content-between flex-wrap mb-3">
                        <div class="mb-3 d-flex flex-wrap align-items-center">
                            <img src="<?=media()."/images/uploads/".$company['logo']?>" class="me-2" style="width=100px;height:100px;" alt="">
                            <div>
                                <p class="m-0 fw-bold"><?=$company['name']?></p>
                                <p class="m-0"><?=$company['addressfull']?></p>
                                <p class="m-0">+<?=$company['phonecode']." ".$company['phone']?></p>
                                <p class="m-0"><?=$company['email']?></p>
                                <p class="m-0"><?=BASE_URL?></p>
                            </div>
                        </div>
                        <div class="text-start">
                            <p class="m-0"><span class="fw-bold">Fecha: </span><?=$order['date']?></p>
                            <p class="m-0"><span class="fw-bold">Pedido: </span>#<?=$order['idorder']?></p>
                            <p class="m-0"><span class="fw-bold">Transaccion: </span><?=$order['idtransaction']?></p>
                            <p class="m-0"><span class="fw-bold">Estado: </span><?=$order['status']?></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12 mb-3">
                            <p class="m-0 mb-2 fw-bold">Cliente</p>
                            <p class="m-0">Nombre: <?=$order['firstname']." ".$order['lastname']?></p>
                            <p class="m-0">Teléfono: <?=$order['phone']?></p>
                            <p class="m-0">Email: <?=$order['email']?></p>
                            <p class="m-0">Dirección: <?=$order['address'].", ".$order['country'].", ".$order['state'].", ".$order['city']."  ".$order['postalcode']?></p>
                            <?php if($order['type'] == "paypal"){?>
                            <p class="m-0 fw-bold mt-3">Notas:</p>
                            <p class="m-0"><?=$order['note']?></p> 
                            <?php }?>
                        </div>
                    </div>
                    <table class="table items align-middle">
                        <thead class="text-center">
                            <tr>
                                <th>Descripcion</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach($detail as $product){
                                $total += $product['quantity']*$product['price'];
                            ?>
                                
                            <tr>
                                <td><?=$product['name']?></td>
                                <td><?=formatNum($product['price'])?></td>
                                <td><?=$product['quantity']?></td>
                                <td><?=formatNum($product['quantity']*$product['price'])?></td>
                            </tr>
                            <?php }?>
                        </tbody>
                        <?php if($order['type'] == "paypal"){?>
                        <tfoot >
                        <?php if($totalInfo['total']['subtotalCoupon'] >0) {?>
                            <tr>
                                <th colspan="3" class="text-end">Subtotal:</th>
                                <td class="text-right"><?= formatNum($subtotal)?></td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Cupón de descuento:</th>
                                <td class="text-right"><?=$amountData['couponInfo']['code']." - ".$amountData['couponInfo']['discount']?>%</td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Subtotal:</th>
                                <td class="text-right"><?= formatNum($subtotalCoupon)?></td>
                            </tr>
                            <?php }else{?>
                            <tr>
                                <th colspan="3" class="text-end">Subtotal:</th>
                                <td class="text-right"><?= formatNum($subtotal)?></td>
                            </tr>
                            <?php }?>
                            <tr>
                                <th colspan="3" class="text-end">Envio:</th>
                                <?php if($totalInfo['shipping']['id'] == 3){?>
                                <td class="text-right"><?=formatNum($totalInfo['shipping']['city']['value'])?></td>
                                <?php }else{ ?>
                                    <td class="text-right"><?= formatNum($totalInfo['shipping']['value'])?></td>
                                <?php }?>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <td class="text-right"><?= formatNum($order['amount'])?></td>
                            </tr>
                        </tfoot>
                        <?php }else{ ?>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <td class="text-right"><?= formatNum($order['amount'])?></td>
                                </tr>
                            </tfoot>
                        <?php }?>
                    </table>
                </div>
                <div class="row">
                    <div class="col-6 text-start">
                        <a href="<?=base_url()?>/Orders" class="btn btn-secondary text-white"><i class="fas fa-arrow-circle-left"></i> Regresar</a>
                    </div>
                    <div class="col-6 text-end">
                        <button type="button" id="btnPrint" class="btn btn-primary"><i class="fas fa-print"></i> Imprimir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php footerAdmin($data)?>        