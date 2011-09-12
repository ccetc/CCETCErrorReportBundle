<?php

namespace CCETC\ErrorReportBundle\Resources\config;

class Config {
  public $supportEmail;
  
  public function __construct($supportEmail) {
    $this->supportEmail = $supportEmail;    
  }
}