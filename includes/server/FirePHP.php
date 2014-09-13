<?
/*<!--firePHP-->*/
//http://www.firephp.org/HQ/Use.htm
require_once('./includes/server/vendor/FirePHPCore-0.3.2/lib/FirePHPCore/FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
//$firephp-> *

//require_once('./includes/server/FirePHPCore-0.3.2/lib/FirePHPCore/fb.php');
//FB:: *
if (!in_array($_SERVER['REMOTE_ADDR'],unserialize(IPS_DEV))) {
	$firephp->setEnabled(false);  // or FB::setEnabled(false);
}
//FB::send(/* See fb() */);

/* EXCEPCIONES */
//Error, Exception & Assertion Handling
//Convert E_WARNING, E_NOTICE, E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE and E_RECOVERABLE_ERROR errors to ErrorExceptions and send all Exceptions to Firebug automatically if desired.
//Assertion errors can be converted to exceptions and thrown if desired. You can also manually send caught exceptions to Firebug.
$firephp->registerErrorHandler(
            $throwErrorExceptions=true);
$firephp->registerExceptionHandler();
$firephp->registerAssertionHandler(
            $convertAssertionErrorsToExceptions=true,
            $throwAssertionExceptions=false);
/*
try {
  throw new Exception('Test Exception');
} catch(Exception $e) {
  $firephp->error($e);  // or FB::
}
*/
/**/

/* GRUPOS Y TIPOS DE MENSAJES*/
/*
$firephp->group('Collapsed and Colored Group',
                array('Collapsed' => true,
                      'Color' => '#FF00FF'));
$firephp->log('Plain Message');     // or FB::
$firephp->info('Info Message');     // or FB::
$firephp->warn('Warn Message');     // or FB::
$firephp->error('Error Message');   // or FB::
$firephp->log('Message con optional label','Optional Label');
//Tb se puede usar:
//$firephp->fb('Message', FirePHP::LOG | FirePHP::INFO | FirePHP::WARN | FirePHP::ERROR);
$firephp->groupEnd();
*/
/**/
//$firephp->trace('Trace Label');
?>