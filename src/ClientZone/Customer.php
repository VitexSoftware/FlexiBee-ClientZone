<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ClientZone;

/**
 * Description of FlexiBeeUser
 *
 * @author vitex
 */
class Customer extends \FlexiPeeHP\Bricks\Customer
{
    /**
     * Where to look for username
     * @var string 
     */
    public $loginColumn = 'email';
}
