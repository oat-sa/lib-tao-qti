<?php

namespace qti\customOperators\text;

use qtism\common\datatypes\String;
use qtism\common\datatypes\Float;
use qtism\runtime\expressions\operators\CustomOperatorProcessor;

class StringToNumber extends CustomOperatorProcessor
{
    public function process() 
    {
        $returnValue = null;
        
        $operands = $this->getOperands();
        if (count($operands) > 0) {
            $operand = $operands[0];
            
            if ($operand !== null && $operand instanceof String) {
                $str = str_replace(array(',', '.'), '', $operand->getValue());
                $float = @floatval($str);
                
                $returnValue = new Float($float);
            }
        }
        
        return $returnValue;
    }
}
