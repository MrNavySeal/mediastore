<?php
    require_once("Models/CustomerTrait.php");
    class Contact extends Controllers{
        use CustomerTrait;
        public function __construct(){
            parent::__construct();
            session_start();
        }

        public function contact(){
            $company=getCompanyInfo();
            $data['page_tag'] = "Contact | ".$company['name'];
			$data['page_title'] = "Contacto | ".$company['name'];
			$data['page_name'] = "contact";
            $data['app'] = "contact.js";
            $this->views->getView($this,"contact",$data);
        }
        public function setContact(){
            if($_POST){
                if(empty($_POST['txtContactName']) || empty($_POST['txtContactEmail']) || empty($_POST['txtContactMessage'])){
                    $arrResponse = array("status"=>true,"msg"=>"Data error");
                }else{
                    $strName = ucwords(strClean($_POST['txtContactName']));
                    $strEmail = strtolower(strClean($_POST['txtContactEmail']));
                    $strMessage = strClean($_POST['txtContactMessage']);
                    $strSubject = $_POST['txtSubject'] !="" ? strClean(($_POST['txtSubject'])) : "Se ha enviado un nuevo mensaje";
                    $company = getCompanyInfo();
                    $request = $this->setMessage($strName,$strEmail,$strSubject,$strMessage);
                    if($request > 0){
                        $dataEmail = array('email_remitente' => $company['email'], 
                                                'email_usuario'=>$strEmail, 
                                                'email_copia'=>$company['secondary_email'],
                                                'asunto' =>$strSubject,
                                                "message"=>$strMessage,
                                                "company"=>$company,
                                                'name'=>$strName);
                        sendEmail($dataEmail,'email_contact');
                        $arrResponse = array("status"=>true,"msg"=>"Recibimos tu mensaje, pronto nos comunicaremos contigo.");
                    }else{
                        $arrResponse = array("status"=>false,"msg"=>"Error, intenta de nuevo");
                    }
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
    }
?>