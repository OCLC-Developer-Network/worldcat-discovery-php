Logging
============

The library allows logging to be added via Guzzle which supports any logging tool which implements a PSR-3 interface.
One can specify the log format using substitutions see [https://github.com/guzzle/guzzle/blob/master/src/MessageFormatter.php].

Log Message Format
==================
We use the follow message logging for our testing purposes

{host} {method} {target} {req_header_authorization} \n {code} {phrase} {res_header_x-OCLC-RequestId} {res_header_x-OCLC-SelfId} {error}

Example: 
==================================================

This example adds basic logging using Monolog Logging [https://github.com/Seldaek/monolog] and sending output to the buffer

.. code:: php

   require_once('vendor/autoload.php');

   use OCLC\Auth\WSKey;
   use OCLC\Auth\AccessToken;
   use WorldCat\Discovery\Bib;
   
   use Monolog\Logger;
   use Monolog\Handler\StreamHandler;
   
   $key = 'api-key';
   $secret = 'api-key-secret';
   $options = array('services' => array('WorldCatDiscoveryAPI', 'refresh_token'));
   $wskey = new WSKey($key, $secret, $options);
   $accessToken = $wskey->getAccessTokenWithClientCredentials('128807', '128807'));
   
    $logger = new Logger('discoveryAPILog');
    $handler = new StreamHandler(php://output, Logger::DEBUG);
    $logger->pushHandler($handler);
    $options = array(
            'logger' => $logger,
            'log_format' => 'Request - {host} {method} {target} {req_header_authorization} \n Response - {code} {phrase} {res_header_x-OCLC-RequestId} {res_header_x-OCLC-SelfId} {error}'
    );
   
   $bib = Bib::find(7977212, $accessToken, $options);
   
Example: 
==================================================

This example adds basic logging using the Monolog Logging [https://github.com/Seldaek/monolog] and sending output to the filesystem

.. code:: php

   require_once('vendor/autoload.php');

   use OCLC\Auth\WSKey;
   use OCLC\Auth\AccessToken;
   use WorldCat\Discovery\Bib;
   
   use Monolog\Logger;
   use Monolog\Handler\StreamHandler;
   
   $key = 'api-key';
   $secret = 'api-key-secret';
   $options = array('services' => array('WorldCatDiscoveryAPI', 'refresh_token'));
   $wskey = new WSKey($key, $secret, $options);
   $accessToken = $wskey->getAccessTokenWithClientCredentials('128807', '128807'));
   
   $logger = new Logger('discoveryAPILog');
   $handler = new StreamHandler(__DIR__.'/my_app.log', Logger::DEBUG);
   $logger->pushHandler($handler);
   $options = array(
           'logger' => $logger,
           'log_format' => Request - {host} {method} {target} {req_header_authorization} \n Response - {code} {phrase} {res_header_x-OCLC-RequestId} {res_header_x-OCLC-SelfId} {error}'
   );

   $bib = Bib::find(7977212, $accessToken, $options);   

Example: 
==================================================

This example adds basic logging using Zend Framework 2 Logging [http://framework.zend.com/manual/2.3/en/modules/zend.log.overview.html] and sending output to the buffer

.. code:: php

    use OCLC\Auth\WSKey;
    use OCLC\Auth\AccessToken;
    use WorldCat\Discovery\Bib;
    use Zend\Log\Logger;
    use Zend\Log\PsrLoggerAdapter;
    use Zend\Log\Writer\Stream;
    
    $key = 'api-key';
    $secret = 'api-key-secret';
    $options = array('services' => array('WorldCatDiscoveryAPI', 'refresh_token'));
    $wskey = new WSKey($key, $secret, $options);
    $accessToken = $wskey->getAccessTokenWithClientCredentials('128807', '128807'));
    
    $logMock = new Mock();
    $logger = new Logger();
    $logger->addWriter($logMock);
    
    $writer = new Stream('php://output');
    $logger = new Logger();
    $logger->addWriter($writer);
    
    $psrLogger = new PsrLoggerAdapter($logger); 

    $options = array(
            'logger' => $psrLogger,
            'log_format' => Request - {host} {method} {target} {req_header_authorization} \n Response - {code} {phrase} {res_header_x-OCLC-RequestId} {res_header_x-OCLC-SelfId} {error}'
    );
    
    $bib = Bib::find(7977212, $accessToken, $options); 

Example: 
==================================================

This example adds basic logging using Zend Framework 2 Logging [http://framework.zend.com/manual/2.3/en/modules/zend.log.overview.html] and sending output to the filesystem

.. code:: php

    use OCLC\Auth\WSKey;
    use OCLC\Auth\AccessToken;
    use WorldCat\Discovery\Bib;
    use Zend\Log\Logger;
    use Zend\Log\PsrLoggerAdapter;
    use Zend\Log\Writer\Stream;
    
    $key = 'api-key';
    $secret = 'api-key-secret';
    $options = array('services' => array('WorldCatDiscoveryAPI', 'refresh_token'));
    $wskey = new WSKey($key, $secret, $options);
    $accessToken = $wskey->getAccessTokenWithClientCredentials('128807', '128807'));
    
    $logMock = new Mock();
    $logger = new Logger();
    $logger->addWriter($logMock);
    
    $writer = new Stream(__DIR__.'/my_app.log');
    $logger = new Logger();
    $logger->addWriter($writer);
    
    $psrLogger = new PsrLoggerAdapter($logger); 

    $options = array(
            'logger' => $psrLogger,
            'log_format' => Request - {host} {method} {target} {req_header_authorization} \n Response - {code} {phrase} {res_header_x-OCLC-RequestId} {res_header_x-OCLC-SelfId} {error}'
    );
    
    $bib = Bib::find(7977212, $accessToken, $options);              
