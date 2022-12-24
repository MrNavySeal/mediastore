<?php 
    class OrdersModel extends Mysql{
        private $intIdOrder;
        private $intIdUser;
        private $intIdTransaction;
        private $strFirstName;
        private $strLastName;
        private $strEmail;
        private $strPhone;
        private $strCountry;
        private $strState;
        private $strCity;
        private $strAddress;
        private $intTotal;
        private $intIdProduct;
        public function __construct(){
            parent::__construct();
        }
        /*************************Category methods*******************************/

        public function selectOrders(){
            $sql = "SELECT * ,DATE_FORMAT(date, '%d/%m/%Y') as date FROM orderdata ORDER BY idorder DESC";       
            $request = $this->select_all($sql);
            return $request;
        }
        public function selectOrder($id,$idPerson){
            $this->intIdOrder = $id;
            $this->intIdUser = $idPerson;
            $option="";
            if($idPerson !=""){
                $option =" AND personid = $this->intIdUser";
            }
            $sql = "SELECT * ,DATE_FORMAT(date, '%d/%m/%Y') as date FROM orderdata WHERE idorder = $this->intIdOrder $option";
            $request = $this->select($sql);
            return $request;
        }
        public function selectOrderDetail($id){
            $this->intIdOrder = $id;
            $sql = "SELECT * FROM orderdetail WHERE orderid = $this->intIdOrder";
            $request = $this->select_all($sql);
            return $request;
        }
        public function selectCoupon($id){
            $sql = "SELECT * FROM coupon WHERE id = $id";
            $request = $this->select($sql);
            return $request;
        }
        public function selectTransaction(string $intIdTransaction,$idPerson){
            $objTransaction = array();
            $this->intIdUser = $idPerson;
            $this->intIdTransaction = $intIdTransaction;

            $option="";
            if($idPerson !=""){
                $option =" AND personid = $this->intIdUser";
            }

            $sql = "SELECT * FROM orderdata WHERE idtransaction = '$this->intIdTransaction' $option";
            $request = $this->select($sql);
            if(!empty($request)){
                $objData = json_decode($request['paypaldata']);
                //dep($objData);exit;
                //$urlTransaction ="https://api.sandbox.paypal.com/v2/payments/captures/".$this->intIdTransaction;
                $urlOrder =$objData->links[0]->href;
                $objTransaction = curlConnectionGet($urlOrder,"application/json",getTokenPaypal());
                //dep($objTransaction);exit;
            }
            return $objTransaction;
        }
        public function insertRefund($idTransaction,$strDescription){
            $response = false;
            $this->intIdTransaction = $idTransaction;
            //href: "https://api.sandbox.paypal.com/v2/payments/captures/9PA67488FV4531342/refund"
            $sql = "SELECT idorder,paypaldata FROM orderdata WHERE idtransaction = '$this->intIdTransaction'";
            $request = $this->select($sql);
            if(!empty($request)){
                //$objData = $request['paypaldata'];
                $urlRefund = URLPAYPAL."/v2/payments/captures/".$this->intIdTransaction."/refund";
                $objData = curlConnectionPost($urlRefund,"application/json",getTokenPaypal());
                if(isset($objData->status) && $objData->status =="COMPLETED"){
                    $idorder = $request['idorder'];
                    $idTransaction = $objData->id;
                    $dataRefund = json_encode($objData);
                    $status = $objData->status;
                    $sqlRefund = "INSERT INTO refund(orderid,idtransaction,datarefund,description,status) VALUES(?,?,?,?,?)";
                    $arrData = array(
                        $idorder,
                        $idTransaction,
                        $dataRefund,
                        $strDescription,
                        $status
                    );
                    $requestRefund = $this->insert($sqlRefund,$arrData);
                    if($requestRefund >0){
                        $sql = "UPDATE orderdata SET status=? WHERE idorder = $idorder";
                        $arrData = array("REFUNDED");
                        $request = $this->update($sql,$arrData);
                        $response = true;
                    }
                }
            }
            return $response;

        }
        public function deleteOrder($id){
            $this->intIdOrder = $id;
            $sql = "DELETE FROM orderdata WHERE idorder = $this->intIdOrder";
            $request = $this->delete($sql);
            return $request;
        }
        public function search($search){
            $sql = "SELECT * ,DATE_FORMAT(date, '%d/%m/%Y') as date FROM orderdata 
                    WHERE idtransaction LIKE '%$search%' || idorder LIKE '%$search%'";
            $request = $this->select_all($sql);
            return $request;
        }
        public function sort($sort){
            $option="DESC";
            if($sort == 2){
                $option = " ASC"; 
            }
            $sql = "SELECT * ,DATE_FORMAT(date, '%d/%m/%Y') as date FROM orderdata ORDER BY idorder $option ";
            $request = $this->select_all($sql);
            return $request;
        }
        public function selectProducts(){
            $sql = "SELECT 
                p.idproduct,
                p.categoryid,
                p.subcategoryid,
                p.reference,
                p.name,
                p.description,
                p.price,
                p.discount,
                p.description,
                p.stock,
                p.status,
                p.route,
                c.idcategory,
                c.name as category,
                s.idsubcategory,
                s.categoryid,
                s.name as subcategory,
                DATE_FORMAT(p.date, '%d/%m/%Y') as date
            FROM product p
            INNER JOIN category c, subcategory s
            WHERE c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory AND p.status = 1 AND p.stock > 0
            ORDER BY p.idproduct DESC
            ";
            $request = $this->select_all($sql);
            if(count($request)> 0){
                for ($i=0; $i < count($request); $i++) { 
                    $idProduct = $request[$i]['idproduct'];
                    $sqlImg = "SELECT * FROM productimage WHERE productid = $idProduct";
                    $requestImg = $this->select_all($sqlImg);
                    if(count($requestImg)>0){
                        $request[$i]['image'] = media()."/images/uploads/".$requestImg[0]['name'];
                    }else{
                        $request[$i]['image'] = media()."/images/uploads/image.png";
                    }
                }
            }
            return $request;
        }
        public function selectProduct($id){
            $this->intIdProduct = $id;
            $sql = "SELECT 
                p.idproduct,
                p.categoryid,
                p.subcategoryid,
                p.reference,
                p.name,
                p.shortdescription,
                p.description,
                p.price,
                p.discount,
                p.stock,
                p.status,
                p.route,
                c.idcategory,
                c.name as category,
                s.idsubcategory,
                s.categoryid,
                s.name as subcategory,
                DATE_FORMAT(p.date, '%d/%m/%Y') as date
            FROM product p
            INNER JOIN category c, subcategory s
            WHERE c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory 
            AND p.idproduct = $this->intIdProduct";
            $request = $this->select($sql);
            $sqlImg = "SELECT * FROM productimage WHERE productid = $this->intIdProduct";
            $requestImg = $this->select_all($sqlImg);
            $request['image'] = media()."/images/uploads/".$requestImg[0]['name'];
            return $request;
        }
        public function searchProducts($search){
            $sql="SELECT 
                p.idproduct,
                p.categoryid,
                p.subcategoryid,
                p.reference,
                p.name,
                p.description,
                p.price,
                p.discount,
                p.description,
                p.stock,
                p.status,
                p.route,
                c.idcategory,
                c.name as category,
                c.route as routec,
                s.idsubcategory,
                s.categoryid,
                s.name as subcategory,
                DATE_FORMAT(p.date, '%d/%m/%Y') as date
            FROM product p
            INNER JOIN category c, subcategory s
            WHERE c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory AND
            p.name LIKE  '%$search%' AND p.status= 1|| c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory AND
            c.name LIKE  '%$search%' AND p.status= 1|| c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory AND
            s.name LIKE '%$search%' AND p.status= 1
            ";
            $request = $this->select_all($sql);
            if(count($request)> 0){
                for ($i=0; $i < count($request); $i++) { 
                    $idProduct = $request[$i]['idproduct'];
                    $sqlImg = "SELECT * FROM productimage WHERE productid = $idProduct";
                    $requestImg = $this->select_all($sqlImg);
                    if(count($requestImg)>0){
                        $request[$i]['image'] = media()."/images/uploads/".$requestImg[0]['name'];
                    }else{
                        $request[$i]['image'] = media()."/images/uploads/image.png";
                    }
                }
            }
            return $request;
        }
        public function searchCustomers($search){
            $sql = "SELECT *,DATE_FORMAT(date, '%d/%m/%Y') as date
            FROM person 
            WHERE firstname LIKE '%$search%' AND roleid=2
            ||  lastname LIKE '%$search%' AND roleid=2 ||  email LIKE '%$search%' AND roleid=2
            ||  phone LIKE '%$search%' AND roleid=2
            ORDER BY idperson DESC";

            $request = $this->select_all($sql);
            return $request;
        }
        public function selectCustomer($id){
            $this->intIdUser = $id;
            $sql = "SELECT 
                    p.idperson,
                    p.image,
                    p.firstname,
                    p.lastname,
                    p.email,
                    p.phone,
                    p.address,
                    p.roleid,
                    p.countryid,
                    p.stateid,
                    p.cityid,
                    p.typeid,
                    p.identification,
                    DATE_FORMAT(p.date, '%d/%m/%Y') as date,
                    p.status,
                    r.idrole,
                    r.name as role,
                    c.id,
                    s.id,
                    t.id,
                    c.name as country,
                    s.name as state,
                    t.name as city
                    FROM person p
                    INNER JOIN role r, countries c, states s,cities t 
                    WHERE c.id = p.countryid AND p.stateid = s.id AND t.id = p.cityid AND r.idrole = p.roleid AND p.idperson = $this->intIdUser";
            $request = $this->select($sql);
            return $request;
        }
        public function insertOrder(int $idUser,string $firstName,string $lastName,string $email, string $phone, string $country,
        string $state,string $city,string $address,int $total){

            $this->intIdUser = $idUser;
            $this->strFirstName = $firstName;
            $this->strLastName = $lastName;
            $this->strEmail = $email;
            $this->strPhone = $phone;
            $this->strCountry = $country;
            $this->strState = $state;
            $this->strCity = $city;
            $this->strAddress = $address;
            $this->intTotal = $total;

            $sql = "INSERT INTO orderdata(personid,firstname,lastname,email,phone,address,country,state,city,amount,type,status) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
            $arrData = array(
                $this->intIdUser,
                $this->strFirstName,
                $this->strLastName,
                $this->strEmail,
                $this->strPhone,
                $this->strCountry,
                $this->strState,
                $this->strCity,
                $this->strAddress,
                $this->intTotal,
                "POS",
                "COMPLETED"
            );
            $request = $this->insert($sql,$arrData);
            if($request > 0){
                $sqlUpdate = "UPDATE orderdata SET idtransaction=? WHERE idorder = $request";
                $arrUpdate = array("POS".$request);
                $requestUpdate = $this->update($sqlUpdate,$arrUpdate);
            }
            return $request;
        }
        public function insertOrderDetail(array $arrOrder){
            $this->intIdUser = $arrOrder['iduser'];
            $this->intIdOrder = $arrOrder['idorder'];
            $products = $arrOrder['products'];

            foreach ($products as $pro) {
                $price=0;
                $this->intIdProduct = $pro['idproduct'];
                if($pro['discount']>0){
                    $price = $pro['price']-($pro['price']*($pro['discount']*0.01));
                }else{
                    $price = $pro['price'];
                }
                $query = "INSERT INTO orderdetail(orderid,personid,productid,name,quantity,price)
                        VALUE(?,?,?,?,?,?)";
                $arrData=array($this->intIdOrder,
                                $this->intIdUser,
                                $this->intIdProduct,
                                $pro['name'],
                                $pro['qty'],
                                $price);
                $selectProduct = $this->selectProduct($this->intIdProduct);
                if($selectProduct['stock']>0){
                    $stock = $selectProduct['stock']-$pro['qty'];
                    $this->updateStock($this->intIdProduct,$stock);
                }
                $request = $this->insert($query,$arrData);
            }
            return $request;
        }
        public function updateStock($id,$stock){
            $this->intIdProduct = $id;
            $sql = "UPDATE product SET stock=? WHERE idproduct = $this->intIdProduct";
            $arrData = array($stock);
            $request = $this->update($sql,$arrData);
            return $request;
        }
    }
?>