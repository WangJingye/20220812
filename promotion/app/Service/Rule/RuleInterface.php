<?php
namespace App\Service\Rule;

Interface RuleInterface
{
    
    function getCartItems();
    
    function setCartItems($cartItems);
    
    function collect();
}



