<?php
    require_once("Controllers/Product.php");
    class Orders extends Controllers{
        private $objProduct;
        public function __construct(){
            
            session_start();
            if(empty($_SESSION['login'])){
                header("location: ".base_url());
                die();
            }
            parent::__construct();
            getPermits(6);
        }

        public function orders(){
            if($_SESSION['permitsModule']['r']){
                $data['page_tag'] = "Pedidos";
                $data['page_title'] = "Pedidos";
                $data['page_name'] = "orders";
                $data['orders'] = $this->getOrders();
                $data['products'] = $this->getProducts();
                
                $data['app'] = "orders.js";
                $this->views->getView($this,"orders",$data);
            }else{
                header("location: ".base_url());
                die();
            }
        }
        public function order($idOrder){
            if($_SESSION['permitsModule']['r']){
                if(is_numeric($idOrder)){
                    $idPerson ="";
                    if($_SESSION['userData']['roleid'] == 2 ){
                        $idPerson= $_SESSION['idUser'];
                    }
                    $data['orderdata'] = $this->model->selectOrder($idOrder,$idPerson);
                    $data['orderdetail'] = $this->model->selectOrderDetail($idOrder);
                    $data['page_tag'] = "Order";
                    $data['page_title'] = "Order";
                    $data['page_name'] = "order";
                    $data['company'] = getCompanyInfo();
                    $this->views->getView($this,"order",$data);
                }else{
                    header("location: ".base_url()."/Orders");
                }
                
            }else{
                header("location: ".base_url());
                die();
            }
        }
        public function transaction($idTransaction){
            if($_SESSION['permitsModule']['r']){
                $idPerson ="";
                if($_SESSION['userData']['roleid'] == 2 ){
                    $idPerson= $_SESSION['idUser'];
                }
                $data['transaction'] = $this->model->selectTransaction($idTransaction,$idPerson);
                $data['page_tag'] = "Transaction";
                $data['page_title'] = "Transaction";
                $data['page_name'] = "transaction";
                $data['app'] = "orders.js";
                $this->views->getView($this,"transaction",$data);
                
            }else{
                header("location: ".base_url());
                die();
            }
        }
        public function getOrders($option=null,$params=null){
            if($_SESSION['permitsModule']['r']){
                $html="";
                $request="";
                if($option == 1){
                    $request = $this->model->search($params);
                }else if($option == 2){
                    $request = $this->model->sort($params);
                }else{
                    $request = $this->model->selectOrders();
                }
                if(count($request)>0){
                    for ($i=0; $i < count($request); $i++) { 

                        $btnView='<a href="'.base_url().'/orders/order/'.$request[$i]['idorder'].'" class="btn btn-info text-white m-1" type="button" title="View order" name="btnView"><i class="fas fa-eye"></i></a>';
                        $btnPaypal='';
                        $btnDelete ="";

                        if($request[$i]['type'] == "paypal"){
                            $btnPaypal = '<a href="'.base_url().'/orders/transaction/'.$request[$i]['idtransaction'].'" class="btn btn-info m-1 text-white " type="button" title="View Transaction" name="btnPaypal"><i class="fab fa-paypal"></i></a>';
                        }

                        if($_SESSION['permitsModule']['d'] && $_SESSION['userData']['roleid'] == 1){
                            $btnDelete = '<button class="btn btn-danger text-white m-1" type="button" title="Delete" data-id="'.$request[$i]['idorder'].'" name="btnDelete"><i class="fas fa-trash-alt"></i></button>';
                        }
                        if($_SESSION['userData']['roleid'] == 1){

                            $html.='
                                <tr class="item">
                                    <td>'.$request[$i]['idorder'].'</td>
                                    <td>'.$request[$i]['idtransaction'].'</td>
                                    <td>'.$request[$i]['date'].'</td>
                                    <td>'.formatNum($request[$i]['amount']).'</td>
                                    <td>'.$request[$i]['type'].'</td>
                                    <td>'.$request[$i]['status'].'</td>
                                    <td class="item-btn">'.$btnView.$btnPaypal.$btnDelete.'</td>
                                </tr>
                            ';
                        }elseif($_SESSION['idUser'] == $request[$i]['personid']){
                            $html.='
                            <tr class="item">
                                <td>'.$request[$i]['idorder'].'</td>
                                <td>'.$request[$i]['idtransaction'].'</td>
                                <td>'.$request[$i]['date'].'</td>
                                <td>'.formatNum($request[$i]['amount']).'</td>
                                <td>'.$request[$i]['type'].'</td>
                                <td>'.$request[$i]['status'].'</td>
                                <td class="item-btn">'.$btnView.$btnPaypal.$btnDelete.'</td>
                            </tr>
                        ';
                        }
                    }
                    $arrResponse = array("status"=>true,"data"=>$html);
                }else{
                    $html = '<tr><td colspan="7">No hay datos</td></tr>';
                    $arrResponse = array("status"=>false,"data"=>$html);
                }
            }else{
                header("location: ".base_url());
                die();
            }
            return $arrResponse;
        }
        public function getTransaction(string $idTransaction){
            if($_SESSION['permitsModule']['r'] && $_SESSION['userData']['roleid'] !=2){
                $idTransaction = strClean($idTransaction);
                $request = $this->model->selectTransaction($idTransaction,"");
                if(!empty($request)){
                    $arrResponse = array("status"=>true,"data"=>$request);
                }else{
                    $arrResponse = array("status"=>false,"msg"=>"Datos no encontrados.");
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function setRefund(){
            if($_POST){
                if(empty($_POST['idTransaction']) || empty($_POST['txtDescription'])){
                    $arrResponse = array("status"=>false,"msg"=>"Error de datos.");
                }else{
                    if($_SESSION['permitsModule']['u'] && $_SESSION['userData']['roleid'] !=2){
                        $idTransaction = strClean($_POST['idTransaction']);
                        $strDescription = strClean($_POST['txtDescription']);
                        $request = $this->model->insertRefund($idTransaction,$strDescription);
                        if($request){
                            $arrResponse = array("status"=>true,"msg"=>"El pedido ha sido reembolsado.");
                        }else{
                            $arrResponse = array("status"=>false,"msg"=>"El pedido no puede ser reembolsado.");
                        }
                    }else{
                        $arrResponse = array("status"=>false,"msg"=>"No se puede reembolsar, por favor, póngase en contacto con su administrador.");
                    }
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function delOrder(){
            if($_SESSION['permitsModule']['d']){

                if($_POST){
                    if(empty($_POST['idOrder'])){
                        $arrResponse=array("status"=>false,"msg"=>"Error de datos");
                    }else{
                        $id = intval($_POST['idOrder']);
                        $request = $this->model->deleteOrder($id);

                        if($request=="ok"){
                            $arrResponse = $this->getOrders();
                        }else{
                            $arrResponse = array("status"=>false,"msg"=>"No se ha podido eliminar, intenta de nuevo.");
                        }
                    }
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                }
            }else{
                header("location: ".base_url());
                die();
            }
            die();
        }
        public function search($params){
            if($_SESSION['permitsModule']['r']){
                $search = strClean($params);
                $arrResponse = $this->getOrders(1,$params);
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function sort($params){
            if($_SESSION['permitsModule']['r']){
                $params = intval($params);
                $arrResponse = $this->getOrders(2,$params);
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function getProduct(){
            if($_SESSION['permitsModule']['r']){
                if($_POST){
                    if(empty($_POST)){
                        $arrResponse = array("status"=>false,"msg"=>"Data error");
                    }else{
                        $id = intval($_POST['idProduct']);
                        $request = $this->model->selectProduct($id);
                        if($request['discount']>0){
                            $request['price'] = $request['price'] - ($request['price'] * ($request['discount']/100));
                        }
                        $request['priceFormat'] = formatNum($request['price']);
                        $arrResponse = array("status"=>true,"data"=>$request);
                    }
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                }
            }else{
                header("location: ".base_url());
                die();
            }
            die();
        }
        public function getProducts($option=null,$params=null){
            if($_SESSION['permitsModule']['r']){
                $html="";
                $request="";
                if($option == 1){
                    $request = $this->model->searchProducts($params);
                }else{
                    $request = $this->model->selectProducts();
                }
                if(count($request)>0){
                    for ($i=0; $i < count($request); $i++) { 
                        $price = formatNum($request[$i]['price']);
                        if($request[$i]['discount']>0){
                            $discount = '<span class="text-success">'.$request[$i]['discount'].'% OFF</span>';
                        }else{
                            $discount = '<span class="text-danger">No discount</span>';
                        }
                        $html.='
                            <tr class="item">
                                <td>
                                    <img src="'.$request[$i]['image'].'" class="rounded">
                                </td>
                                <td>'.$request[$i]['name'].'</td>
                                <td>'.$price.'</td>
                                <td>'.$discount.'</td>
                                <td><button type="button" class="btn btn-primary" id="btn'.$request[$i]['idproduct'].'" onclick="addProduct('.$request[$i]['idproduct'].',this)">Add</button></td>
                            </tr>
                        ';
                    }
                    $arrResponse = array("status"=>true,"data"=>$html);
                }else{
                    $html = '<tr><td colspan="5">No hay datos</td></tr>';
                    $arrResponse = array("status"=>false,"data"=>$html);
                }
            }else{
                header("location: ".base_url());
                die();
            }
            
            return $arrResponse;
        }
        public function searchProducts($params){
            if($_SESSION['permitsModule']['r']){
                $search = strClean($params);
                $arrResponse = $this->getProducts(1,$params);
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function searchCustomers($params){
            if($_SESSION['permitsModule']['r']){
                $search = strClean($params);
                $request = $this->model->searchCustomers($search);
                $html ="";
                if(count($request)>0){
                    for ($i=0; $i < count($request); $i++) { 
                        $html .='
                        <button class="p-2 btn w-100 text-start" data-id="'.$request[$i]['idperson'].'" onclick="addCustom(this)">
                            <p class="m-0 fw-bold">'.$request[$i]['firstname'].' '.$request[$i]['lastname'].'</p>
                            <p class="m-0">Email: <span>'.$request[$i]['email'].'</span></p>
                            <p class="m-0">Phone: <span>'.$request[$i]['phone'].'</span></p>
                        </button>
                        ';
                    }
                    $arrResponse = array("status"=>true,"data"=>$html);
                }else{
                    $arrResponse = array("status"=>false,"data"=>$html);
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function setOrder(){
            //dep($_POST);exit;
            if($_SESSION['permitsModule']['w']){
                if($_POST){
                    if(empty($_POST['id']) || empty($_POST['products'])){
                        $arrResponse = array("status"=>false,"msg"=>"Error de datos");
                    }else{
                        $arrProducts = json_decode($_POST['products'],true);
                        $orderDetail = [];
                        $total = 0;
                        for ($i=0; $i < count($arrProducts) ; $i++) { 
                            $request = $this->model->selectProduct($arrProducts[$i]['id']);
                            $request['qty'] = $arrProducts[$i]['qty'];
                            array_push($orderDetail,$request);
                        }

                        foreach ($orderDetail as $product) {
                            if($product['discount']>0){
                                $total += $product['qty'] * ($product['price'] - ($product['price']*($product['discount']/100)));
                            }else{
                                $total += $product['qty'] * $product['price'];
                            }
                        }
                        
                        $idCustomer = intval($_POST['id']);
                        $customInfo = $this->model->selectCustomer($idCustomer);

                        $requestOrder = $this->model->insertOrder($idCustomer,$customInfo['firstname'],$customInfo['lastname'],$customInfo['email'],
                        $customInfo['phone'],$customInfo['country'],$customInfo['state'],$customInfo['city'],$customInfo['address'],$total);

                        if($requestOrder > 0){
                            $arrData = array("iduser"=>$idCustomer,"idorder"=>$requestOrder,"products"=>$orderDetail);
                            $requestOrderDetail = $this->model->insertOrderDetail($arrData);
                            $arrResponse = array("status"=>true,"msg"=>"Datos guardados");
                        }else{
                            $arrResponse = array("status"=>false,"msg"=>"Error, intenta de nuevo");
                        }
                    }
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                }
            }
            die();
        }
        
    }
?>