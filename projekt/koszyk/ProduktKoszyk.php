<?php

trait Amount{
    public function increaseQuantity($quantity)
    {
        $this->quantity += $quantity;
    }

    public function decreaseQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity -= 1;
        }
    }
}

class ProduktKoszyk
{
    private $id;
    private $quantity;

    public function __construct($id, $quantity)
    {
        if (is_numeric($id) && is_numeric($quantity) && $id > 0 && $quantity > 0) {
            $this->id = $id;
            $this->quantity = $quantity;
        }
    }

    public function __get($property)
    {
        if ($property === 'id') {
            return $this->id;
        }
        if ($property === 'quantity') {
            return $this->quantity;
        }
    }

    public function __set($property, $value)
    {
        if ($property === 'id') {
            if (is_numeric($value)) {
                $this->id = $value;
            }
        }

        if ($property === 'quantity') {
            if (is_numeric($value)) {
                $this->quantity = $value;
            }
        }
    }

use Amount;
}
