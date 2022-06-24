<?php

class API_COINOS
{

  const END_POINT="https://coinos.io/api/";


  const OBTENER_BALANCE = "me";
  const CREAR_INVOICE = "lightning/invoice";
  const ASOCIAR_INVOICE= "invoice";

  const LISTAR_PAGOS= 'payments';


  private $debug;
  private $error_text;
  private $error_text_log;

  public function __construct() 
  {
        
        $this->debug=false;
      
  }
  

  public function getErrorText()
  {
    return $this->error_text;
  }

  public function getErrorTextLog()
  {
    return $this->error_text_log;
  }

 
  public function setDebug($debug)
  {
     $this->debug=$debug;
  }


  public function obtenerBalance()
  {  
     $url=self::END_POINT.self::OBTENER_BALANCE;    


       
     $respuesta=$this->enviarDatos($url);
     
     if ($respuesta['resultado']==0)
        {
          $this->error_text_log=json_encode($respuesta['datos']);
          
          return 0;
          
        }

       $trama=json_decode($respuesta['datos']);
       

       $balance=$trama->account->balance;

      return $balance;
  }


   public function listPayments()
   {
      $url=self::END_POINT.self::LISTAR_PAGOS;    
      $rs=$this->enviarDatos($url);

      
      if ($rs['resultado']==0)
      {
          $this->error_text_log=json_encode($rs['datos']);
          $respuesta=array("resultado"=>0,"datos"=>"");
      }
      else
      {
        
          $respuesta=array("resultado"=>1,"datos"=>$rs['datos']);
      }

      return $respuesta;      

   }

   public function buscarInvoice($invoice_id)
   {
     $rs=$this->listPayments();
     

     if($rs['resultado']!=0)
     {
       $pagos=json_decode($rs['datos'],true);       

      foreach ($pagos as $item) 
      {
        if($item['invoice_id']==$invoice_id)
         return true;
      }
      
     }
      

     return false;

   }


public function crearInvoice($monto)
{

      $respuesta=array("resultado"=>0,"id"=>"","invoice"=>"");
      $invoice=$this->generarInvoice($monto);

      if($invoice)
      {
        $resultado=$this->asociarInvoiceUsuario($invoice);


        if($resultado!=0)
          $respuesta=array("resultado"=>1,"id"=>$resultado,"invoice"=>$invoice);

      }

   return $respuesta;

}

  private function generarInvoice($monto)
  {

      $url=self::END_POINT.self::CREAR_INVOICE;      

      $datos=array("amount"=>$monto);

      $respuesta=$this->enviarDatos($url,$metodo="POST",json_encode($datos));

      if ($respuesta['resultado']==0)
      {
          $this->error_text_log=json_encode($respuesta['datos']);

      }

        $invoice_result="";

        $invoice=$respuesta['datos']; //Verificar que sea string
        if (substr($invoice, 0, 4)=="lnbc")
           $invoice_result=$invoice;
        
       return $invoice_result;
    
  }


  private function asociarInvoiceUsuario($invoice)
  {
     
    

     $url=self::END_POINT.self::ASOCIAR_INVOICE;      
     $datos=array("invoice"=>array("text"=>$invoice,"network"=>"bitcoin"),"user"=>array("username"=>USUARIO));


     $respuesta=$this->enviarDatos($url,$metodo="POST",json_encode($datos));

      if ($respuesta['resultado']==0)
      {
          $this->error_text_log=json_encode($respuesta['datos']);
          return 0;
      }

        $trama=json_decode($respuesta['datos']);

      return $trama->id;

  }


  private function enviarDatos($url,$metodo="GET",$parametros_json=null)
  {

    $curl = curl_init();

    if($metodo=="GET")
     {
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
        "authorization: Bearer ".TOKEN,
        "content-type: application/json")
      ));
    }
    else
    {

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $parametros_json,
        CURLOPT_HTTPHEADER => array(
    "authorization: Bearer ".TOKEN,
    "content-type: application/json"),
    ));


    }

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err)            
      return array("resultado"=>0,"datos"=>"cURL Error #:" . $err);
    else     
      return array("resultado"=>1,"datos"=>$response);
    
  }    

}

?>
