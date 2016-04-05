<?php

namespace qti\customOperators\math\graph;

use qtism\common\enums\BaseType;
use qtism\common\datatypes\Integer as QtismInteger;
use qtism\common\datatypes\String as QtismString;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\expressions\operators\CustomOperatorProcessor;

class CountPointsThatSatisfyEquation extends CustomOperatorProcessor
{
    public function process() 
    {
        $returnValue = new QtismInteger(0);
        $operands = $this->getOperands();
        
        if (count($operands) >= 2) {
            $points = $operands[0];
            $equation = $operands[1];
            
            if (($points instanceof MultipleContainer || $points instanceof OrderedContainer) && $points->getBaseType() === BaseType::POINT && $equation instanceof QtismString) {
                // Check every Point X,Y against the equation...
                $math = new \oat\beeme\Parser();
                
                try {
                    foreach ($points as $point) {
                        $x = floatval($point->getX());
                        $y = floatval($point->getY());
                        
                        $result = $math->evaluate(
                            $equation->getValue(),
                            array(
                                'x' => $x,
                                'y' => $y
                            )
                        );
                        
                        if ($result === true) {
                            // The Point X,Y satisfies the equation...
                            $returnValue->setValue($returnValue->getValue() + 1);
                        }
                    }
                } catch (\Exception $e) {
                    // If an error occurs e.g. invalid expression, the NULL value is returned.
                    return null;
                }
            } else {
                // Not supported operands, return the NULL value.
                return null;
            }
        }
        
        return $returnValue;
    }
}
