<?php
/*\
 | ------------------------------------------------------
 | @file : Di.php
 | @author : Fabien Beaujean
 | @description : Trait dependency Injection
 | @version : 3.0
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\General;

trait Di {
    /**
     * @var \Gcs\Framework\Core\Request\Request $request
     */
    private $request = null;

    /**
     * @var \Gcs\Framework\Core\Response\Response $response
     */
    private $response = null;

    /**
     * @var \Gcs\Framework\Core\Profiler\Profiler $profiler
     */
    private $profiler = null;

    /**
     * @var \Gcs\Framework\Core\Config\Config $config
     */
    private $config = null;
}