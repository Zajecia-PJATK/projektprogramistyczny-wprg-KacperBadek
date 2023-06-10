<?php

trait Amount{
    public function increaseQuantity()
    {
        $this->quantity += 1;
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

    public function __construct($id)
    {
        if (is_numeric($id)) {
            $this->id = $id;
            $this->quantity = 1;
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
