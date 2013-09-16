<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

use qtism\common\datatypes\Pair;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\Variable;
use qtism\common\datatypes\Point;

/**
 * This helper class gives a way to build a State (a collection of variables)
 * to be transmitted to the client-side.
 * 
 * The result output can be accessed with the tao_helpers_StateOutput::getOutput() method
 * which returns an associative array, that can be transformed as needed for transmission
 * to the client-side.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiCommon_helpers_StateOutput {
    
    /**
     * The output associative array output.
     * 
     * @var array
     */
    private $output;
    
    /**
     * Create a new tao_helpers_StateOutput object.
     * 
     */
    public function __construct() {
        $this->setOutput(array());
    }
    
    /**
     * Set the output array.
     * 
     * @param array $output An array.
     */
    protected function setOutput(array $output) {
        $this->output = $output;
    }
    
    /**
     * Get the output array.
     * 
     * @return array
     */
    public function &getOutput() {
        return $this->output;
    }
    
    /**
     * Add a variable to the State that has to be built for the client-side. The final output
     * array is available through the tao_helpers_StateOutput::getOutput() method.
     * 
     * @param Variable $variable A QTI Runtime Variable
     */
    public function addVariable(Variable $variable) {
        $output = &$this->getOutput();
        $baseType = $variable->getBaseType();
        $cardinality = $variable->getCardinality();
        $varName = $variable->getIdentifier();
        
        if ($cardinality === Cardinality::SINGLE) {
            $singleValue = $variable->getValue();
            if (is_null($singleValue) === true) {
                $output[$varName] = array('');
                return;
            }
            
            $values = array($variable->getValue());
        }
        else {
            $containerValue = $variable->getValue();
            if ($containerValue->isNull() === true) {
                $output[$varName] = array();
                return;
            }
            
            $values = $containerValue->getArrayCopy(true);
        }
        
        $output[$varName] = array();
        
        foreach ($values as $val) {
            if (is_null($val) === true) {
                $output[$varName][] = '';   
            }
            else if (gettype($val) === 'boolean') {
                $output[$varName][] = ($val === true) ? 'true' : 'false';
            }
            else if ($val instanceof Point) {
                $output[$varName][] = array('' . $val->getX(), '' . $val->getY());
            }
            else if ($val instanceof Pair) {
                $output[$varName][] = array($val->getFirst(), $val->getSecond());
            }
            else if (gettype($val) === 'object') {
                $output[$varName][] = $val->__toString();
            }
            else {
                $output[$varName][] = '' . $val;
            }
        }
    }
}