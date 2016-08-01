<?php

namespace qti\customOperators;

use qtism\common\enums\BaseType;
use qtism\common\datatypes\String;
use qtism\runtime\expressions\operators\CustomOperatorProcessor;

abstract class CsvToContainer extends CustomOperatorProcessor
{
    public function process() 
    {
        $returnValue = null;
        
        $operands = $this->getOperands();
        if (count($operands) > 0) {
            $operand = $operands[0];
            
            if ($operand !== null && $operand instanceof String) {
                $returnValue = $this->createContainer();
                $values = explode(',', $operand->getValue());
                
                foreach ($values as $value) {
                    $returnValue[] = new String($value);
                }
            }
        }
        
        return $returnValue;
    }
    
    abstract protected function createContainer();
}
