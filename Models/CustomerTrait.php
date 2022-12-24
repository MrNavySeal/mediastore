<?php
    require_once("Libraries/Core/Mysql.php");
    trait CustomerTrait{
        private $con;
        private $strName;
        private $strPicture;
        private $strPassword;
        private $intRoleId;
        private $intIdUser;
        private $strIdTransaction;
        private $strCoupon;
        private $intIdOrder;
        private $strFirstName;
        private $strLastName;
        private $strEmail;
        private $strPhone;
        private $strCountry;
        private $strState;
        private $strCity;
        private $strAddress;
        private $strPostalCode;
        private $strSubject;
        private $strMessage;
        private $intIdProduct;
        

        public function setCustomerT($strName,$strPicture,$strEmail,$strPassword,$rolid){
            $this->con = new Mysql();
            $this->strNombre = $strName;
            $this->strPicture = $strPicture; 
            $this->strEmail =  $strEmail;
            $this->strPassword = $strPassword;
            $this->intRolId = $rolid;
            $return="";
            
            $sql = "SELECT * FROM person WHERE email = '$this->strEmail'";
            $request = $this->con->select_all($sql);
            if(empty($request)){
                $query = "INSERT INTO person(firstname,image,email,countryid,stateid,cityid,password,roleid) VALUE(?,?,?,?,?,?,?,?)";
                $arrData = array($this->strNombre,
                                $this->strPicture,
                                $this->strEmail,
                                99999,
                                99999,
                                99999,
                                $this->strPassword,
                                $this->intRolId
                                );
                $request_insert = $this->con->insert($query,$arrData);
                $return = $request_insert;
            }else{
                $return ="exist";
            }
            return $return;
        }
        public function selectCountries(){
            $this->con = new Mysql();
            $sql = "SELECT * FROM countries";
            $request = $this->con->select_all($sql);
            return $request;
        }
        public function selectStates($country){
            $this->con = new Mysql();
            $sql = "SELECT * FROM states WHERE country_id = $country";
            $request = $this->con->select_all($sql);
            return $request;
        }
        public function selectCities($state){
            $this->con = new Mysql();
            $sql = "SELECT * FROM cities WHERE state_id = $state";
            $request = $this->con->select_all($sql);
            return $request;
        }
        public function selectCouponCode($strCoupon){
            $this->con = new Mysql();
            $this->strCoupon = $strCoupon;
            $sql = "SELECT * FROM coupon WHERE code = '$this->strCoupon' AND status = 1";
            $request = $this->con->select($sql);
            return $request;
        }
        public function checkCoupon($idUser,$idCoupon){
            $this->con = new Mysql();
            $this->intIdUser = $idUser;
            $sql = "SELECT * FROM usedcoupon WHERE personid = $this->intIdUser AND couponid = $idCoupon";
            $request = $this->con->select($sql);
            if(!empty($request)){
                $request = false;
            }else{
                $request = true;
            }
            return $request;
        }
        public function setCoupon($idCoupon,$idUser,$code){
            $this->con = new Mysql();
            $this->intIdUser = $idUser;
            $sql = "INSERT INTO usedcoupon(couponid,personid,code) VALUE(?,?,?)";
            $arrData = array($idCoupon,$this->intIdUser,$code);
            $request = $this->con->insert($sql,$arrData);
            return;
        }
        public function insertOrder($idUser,$idTransaction,$dataPaypal,$amountData,$firstname,$lastname,$email,$phone,$country,$state,$city,$address,
        $postalCode,$note,$total,$status){

            $this->con = new Mysql();
            $this->strIdTransaction = $idTransaction;
            $this->intIdUser = $idUser;
            $this->strFirstName = $firstname;
            $this->strLastName = $lastname;
            $this->strEmail = $email;
            $this->strPhone = $phone;
            $this->strCountry = $country;
            $this->strState = $state;
            $this->strCity = $city;
            $this->strPostalCode = $postalCode;
            $this->strAddress=$address;

            $sql ="INSERT INTO orderdata(personid,idtransaction,paypaldata,amountdata,firstname,lastname,email,phone,address,country,state,city,postalcode,note,amount,type,status) VALUE(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $arrData = array(
                $this->intIdUser, 
                $this->strIdTransaction,
                $dataPaypal,
                $amountData,
                $this->strFirstName,
                $this->strLastName,
                $this->strEmail,
                $this->strPhone,
                $this->strAddress,
                $this->strCountry,
                $this->strState,
                $this->strCity,
                $this->strPostalCode,
                $note,
                $total,
                "paypal",
                $status);
            $request = $this->con->insert($sql,$arrData);
            return $request;
        }
        public function insertOrderDetail(array $arrOrder){
            $this->con = new Mysql();
            $this->intIdUser = $arrOrder['iduser'];
            $this->intIdOrder = $arrOrder['idorder'];
            $products = $arrOrder['products'];

            foreach ($products as $pro) {
                $price=0;
                $this->intIdProduct = openssl_decrypt($pro['idproduct'],METHOD,KEY);
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
                $request = $this->con->insert($query,$arrData);
            }
            return $request;
        }
        public function getOrder($idOrder){
            $this->con = new Mysql();
            $this->intIdOrder =$idOrder;
            $sql = "SELECT idorder,
                    idtransaction,
                    firstname,
                    lastname,
                    email,
                    phone,
                    address,
                    country,
                    state,
                    city,
                    postalcode,
                    note,
                    amount,
                    DATE_FORMAT(date, '%d/%m/%Y') as date,
                    status
                    FROM orderdata WHERE idorder = $this->intIdOrder";
            $order = $this->con->select($sql);
            if(!empty($order)){
                $sql = "SELECT * FROM orderdetail WHERE orderid = $this->intIdOrder";
                $detail = $this->con->select_all($sql);
                $arrData = array("order"=>$order,"detail"=>$detail);
            }   
            return $arrData;
        }
        public function setMessage($strName,$strEmail,$strSubject,$strMessage){
            $this->con = new Mysql();
            $this->strName = $strName;
            $this->strEmail = $strEmail;
            $this->strSubject = $strSubject;
            $this->strMessage = $strMessage;

            $sql = "INSERT INTO contact(name,email,subject,message,status) VALUES(?,?,?,?,?)";
            $arrData = array($this->strName,$this->strEmail,$this->strSubject,$strMessage,2);
            $request = $this->con->insert($sql,$arrData);
            return $request;
        }
        public function setSuscriberT($strEmail){
            $this->con = new Mysql();
            $this->strEmail = $strEmail;
            $return ="";
            $sql = "SELECT * FROM suscriber WHERE email = '$strEmail'";
            $request = $this->con->select($sql);
            if(empty($request)){
                $sql = "INSERT INTO suscriber(email) VALUES(?)";
                $arrData = array($strEmail);
                $request = $this->con->insert($sql,$arrData);
                $return = $request;
            }else{
                $return = "exists";
            }
            return $return;
        }
        public function statusCouponSuscriberT(){
            $this->con = new Mysql();
            $sql = "SELECT * FROM coupon WHERE id = 1 AND status = 1 AND discount > 0";
            $request = $this->con->select($sql);
            return $request;
        }
        public function selectShippingMode(){
            $this->con = new Mysql();
            $sql = "SELECT * FROM shipping WHERE status = 1";
            $request = $this->con->select($sql);
            if($request['id'] == 3){
                $sqlCities = "SELECT
                sh.id,
                c.name as country,
                s.name as state,
                cy.name as city,
                sh.value
                FROM shippingcity sh
                INNER JOIN countries c, states s, cities cy
                WHERE c.id = sh.country_id AND s.id = sh.state_id AND cy.id = sh.city_id
                ORDER BY cy.name ASC";
                $cities = $this->con->select_all($sqlCities);
                $request['cities'] = $cities;
            }
            return $request;
        }
        public function selectShippingCity($id){
            $this->con = new Mysql();
            $sql = "SELECT
            sh.id,
            c.name as country,
            s.name as state,
            cy.name as city,
            sh.value
            FROM shippingcity sh
            INNER JOIN countries c, states s, cities cy
            WHERE c.id = sh.country_id AND s.id = sh.state_id AND cy.id = sh.city_id AND sh.id = $id ORDER BY cy.name ASC";
            $request = $this->con->select($sql);
            return $request;
        }
        public function selectProduct($id){
            $this->con = new Mysql();
            $this->intIdProduct = $id;
            $sql = "SELECT * FROM product WHERE idproduct =$this->intIdProduct";
            $request = $this->con->select($sql);
            return $request;
        }
        public function updateStock($id,$stock){
            $this->con = new Mysql();
            $this->intIdProduct = $id;
            $sql = "UPDATE product SET stock=? WHERE idproduct = $this->intIdProduct";
            $arrData = array($stock);
            $request = $this->con->update($sql,$arrData);
            return $request;
        }
    }
    
?>