<?php
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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 */

use qtism\common\datatypes\QtiDatatype;
use qtism\common\datatypes\QtiIdentifier;
use qtism\common\datatypes\QtiPair;
use qtism\common\datatypes\QtiDirectedPair;
use qtism\common\datatypes\QtiBoolean;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;

class UtilsTest extends PHPUnit_Framework_TestCase {
	
    /**
     * @dataProvider toQtiDatatypeProvider
     */
    public function testToQtiDatatype($cardinality, $basetype, $value, $expected)
    {
        $result = taoQtiCommon_helpers_Utils::toQtiDatatype($cardinality, $basetype, $value);
        
        if ($expected === null) {
            $this->assertNull($result);
        } elseif ($expected instanceof QtiDatatype) {
            $this->assertTrue($expected->equals($result), $expected . ' != ' . $result);
        } else {
            $this->fail('testToQtiDatatype only deals with QtiDatatype objects or null.');
        }
    }
    
    public function toQtiDatatypeProvider()
    {
        return array(
            array('single', 'identifier', '', null),
            array('single', 'pair', '', null),
            array('single', 'directedPair', '', null),
            array('single', 'boolean', '', null),
            array('multiple', 'identifier', '', null),
            array('multiple', 'pair', '', null),
            array('multiple', 'boolean', '', null),
            array('multiple', 'directedPair', '', null),
            array('ordered', 'identifier', '', null),
            array('ordered', 'pair', '', null),
            array('ordered', 'directedPair', '', null),
            array('ordered', 'boolean', '', null),
            array('record', 'unknown', '', null),
            
            array('single', 'identifier', 'choice_1', new QtiIdentifier('choice_1')),
            array('single', 'pair', 'choice_1 choice_2', new QtiPair('choice_1', 'choice_2')),
            array('single', 'directedPair', 'choice_1 choice_2', new QtiDirectedPair('choice_1', 'choice_2')),
            array('single', 'boolean', 'true', new QtiBoolean(true)),
            array('single', 'boolean', 'false', new QtiBoolean(false)),
            
            array('multiple', 'identifier', '[]', new MultipleContainer(BaseType::IDENTIFIER)),
            array('multiple', 'identifier', '[choice_1]', new MultipleContainer(BaseType::IDENTIFIER, array(new QtiIdentifier('choice_1')))),
            array('multiple', 'identifier', '[choice_1; choice_2]', new MultipleContainer(BaseType::IDENTIFIER, array(new QtiIdentifier('choice_1'), new QtiIdentifier('choice_2')))),
            
            array('multiple', 'pair', '[]', new MultipleContainer(BaseType::PAIR)),
            array('multiple', 'pair', '[choice_1 choice_2]', new MultipleContainer(BaseType::PAIR, array(new QtiPair('choice_1', 'choice_2')))),
            array('multiple', 'pair', '[choice_1 choice_2; choice_3 choice_4]', new MultipleContainer(BaseType::PAIR, array(new QtiPair('choice_1', 'choice_2'), new QtiPair('choice_3', 'choice_4')))),
            
            array('multiple', 'directedPair', '[]', new MultipleContainer(BaseType::DIRECTED_PAIR)),
            array('multiple', 'directedPair', '[choice_1 choice_2]', new MultipleContainer(BaseType::DIRECTED_PAIR, array(new QtiDirectedPair('choice_1', 'choice_2')))),
            array('multiple', 'directedPair', '[choice_1 choice_2; choice_3 choice_4]', new MultipleContainer(BaseType::DIRECTED_PAIR, array(new QtiDirectedPair('choice_1', 'choice_2'), new QtiDirectedPair('choice_3', 'choice_4')))),
            
            array('multiple', 'boolean', '[]', new MultipleContainer(BaseType::BOOLEAN)),
            array('multiple', 'boolean', '[false]', new MultipleContainer(BaseType::BOOLEAN, array(new QtiBoolean(false)))),
            array('multiple', 'boolean', '[true; false]', new MultipleContainer(BaseType::BOOLEAN, array(new QtiBoolean(true), new QtiBoolean(false)))),
            
            array('ordered', 'identifier', '<>', new OrderedContainer(BaseType::IDENTIFIER)),
            array('ordered', 'identifier', '<choice_1>', new OrderedContainer(BaseType::IDENTIFIER, array(new QtiIdentifier('choice_1')))),
            array('ordered', 'identifier', '<choice_1; choice_2>', new OrderedContainer(BaseType::IDENTIFIER, array(new QtiIdentifier('choice_1'), new QtiIdentifier('choice_2')))),
            
            array('ordered', 'pair', '<>', new OrderedContainer(BaseType::PAIR)),
            array('ordered', 'pair', '<choice_1 choice_2>', new OrderedContainer(BaseType::PAIR, array(new QtiPair('choice_1', 'choice_2')))),
            array('ordered', 'pair', '<choice_1 choice_2; choice_3 choice_4>', new OrderedContainer(BaseType::PAIR, array(new QtiPair('choice_1', 'choice_2'), new QtiPair('choice_3', 'choice_4')))),
            
            array('ordered', 'directedPair', '<>', new OrderedContainer(BaseType::DIRECTED_PAIR)),
            array('ordered', 'directedPair', '<choice_1 choice_2>', new OrderedContainer(BaseType::DIRECTED_PAIR, array(new QtiDirectedPair('choice_1', 'choice_2')))),
            array('ordered', 'directedPair', '<choice_1 choice_2; choice_3 choice_4>', new OrderedContainer(BaseType::DIRECTED_PAIR, array(new QtiDirectedPair('choice_1', 'choice_2'), new QtiDirectedPair('choice_3', 'choice_4')))),
            
            array('ordered', 'boolean', '<>', new OrderedContainer(BaseType::BOOLEAN)),
            array('ordered', 'boolean', '<false>', new OrderedContainer(BaseType::BOOLEAN, array(new QtiBoolean(false)))),
            array('ordered', 'boolean', '<true; false>', new OrderedContainer(BaseType::BOOLEAN, array(new QtiBoolean(true), new QtiBoolean(false)))),
            
            array('single', 'pair', 'choice_1   choice_2', null),
            array('single', 'pair', 'choice_1  x choice_2', null),
            array('single', 'pair', '1_choice 1_choice', null),
            array('single', 'boolean', 'erut', null),
            array('multiple', 'boolean', '[true; erut]', null),
            array('multiple', 'pair', '[choice_1 choice_2; 3_choice 4_choice]', null),
        );
    }
}
