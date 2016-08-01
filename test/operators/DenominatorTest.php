<?php

use qtism\common\enums\BaseType;
use qtism\common\datatypes\Boolean;
use qtism\common\datatypes\String;
use qtism\data\expressions\operators\CustomOperator;
use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\BaseValue;
use qtism\runtime\expressions\operators\OperandsCollection;
use qti\customOperators\math\fraction\Denominator;

/*  
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
 * Copyright (c) 2013-2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 */

 
/**
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class DenominatorTest extends PHPUnit_Framework_TestCase {
	
    public function testSimple() {
        $baseValue = new BaseValue(BaseType::STRING, '1/2');
        $customOperator = new CustomOperator(
            new ExpressionCollection(array($baseValue)),
            '<customOperator class="qti.customOperators.math.Denominator"><baseValue baseType="string">1/2</baseValue></customOperator>'
        );
        $operands = new OperandsCollection(array(new String('1/2')));
        $operator = new Denominator($customOperator, $operands);
        $result = $operator->process();
        
        $this->assertEquals($result->getValue(), 2);
    }
    
    public function testReturnsNullOne() {
        $baseValue = new BaseValue(BaseType::BOOLEAN, false);
        $customOperator = new CustomOperator(
            new ExpressionCollection(array($baseValue)),
            '<customOperator class="qti.customOperators.math.Denominator"><baseValue baseType="boolean">false</baseValue></customOperator>'
        );
        $operands = new OperandsCollection(array(new Boolean(false)));
        $operator = new Denominator($customOperator, $operands);
        $result = $operator->process();
        
        $this->assertSame(null, $result);
    }
    public function testReturnsNullTwo() {
        $baseValue = new BaseValue(BaseType::BOOLEAN, false);
        $customOperator = new CustomOperator(
            new ExpressionCollection(array($baseValue)),
            '<customOperator class="qti.customOperators.math.Denominator"><baseValue baseType="string">bla</baseValue></customOperator>'
        );
        $operands = new OperandsCollection(array(new Boolean(false)));
        $operator = new Denominator($customOperator, $operands);
        $result = $operator->process();
        
        $this->assertSame(null, $result);
    }
}
