# Coinos php lib
PHP Wrapper for coinos.io wallet

Coinos.io is a web custody service that allows you to receive and send Bitcoin payments on Onchain, Liquid and Lightning.
This wrapper  was developed to facilitate lightning network  integration with php sites.

## How use

 1. In config.php file you must set the TOKEN  and USUARIO from coinos.io
 2. Create a file  php  that include  file config.php and the  wrapper api_coinos.php
 3. Instantiate the wrapper and use the method that you need , next an example to  create a invoice.

~~~
include('config.php');
include('api_coinos.php');
$api=new API_COINOS();
//monto in sats
$monto=1000; 
$respuesta=$api->crearInvoice($monto);
if($respuesta['resultado']==1)
{		
	echo "The id=".$respuesta['id'];				
    echo "<br/>Invoice=".$respuesta['invoice'];
}
else
{
	echo "There was an error";
}
~~~