<?php

include('config.php');
include('api_coinos.php');


$api=new API_COINOS();


$accion=$_GET['accion'];


switch ($accion) 
{

	case 'buscarInvoice':

		//$invoice_id=286026;
		$invoice_id=$_GET['invoice_id'];

		if($invoice_id)
		{
			echo "Invoice_id Buscado ".$invoice_id;

			$res=$api->buscarInvoice($invoice_id);

			if($res)
 				echo "<br/>Invoice_id  found";
			else
 				echo "<br/>Invoice_id not found";	
		}	
		else
		{
			echo "Escriba en la url el invoice_id";
		}	
		
	
		break;	


	case 'crearInvoice':

		$monto=$_GET['monto'];
		if($monto)
		{

			$respuesta=$api->crearInvoice($monto);


			if($respuesta['resultado']==1)
			{		
  				echo "El id=".$respuesta['id'];  				
  				echo "<br/>Invoice=".$respuesta['invoice'];
			}
			else
			{
 				echo "There was an error";
			}

		}
		else
		{
			echo "Escriba en la url el valor del campo monto";
		}	
		
	
		# code...
		break;		

	case 'listarPagos':
		$res=$api->listPayments();

		echo "<pre>";		
		print_r($res);
		echo "</pre>";
	
		break;			
	
	default:
		$balance=$api->obtenerBalance();
		echo "Balance=".$balance." sats";

		break;
}
echo "<hr/>";
$url='<a href="?accion=listarPagos">List Payments</a>';
echo $url."<br/>";
$url='<a href="?accion=buscarInvoice&invoice_id=idaqui">Look Invoice</a>';
echo $url."<br/>";
$url='<a href="?accion=crearInvoice&monto=1000">Create Invoice for 1000 sats</a>';
echo $url;
echo "<hr/>";

?>

