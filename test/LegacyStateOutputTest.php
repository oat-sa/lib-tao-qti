<?php

use PHPUnit\Framework\TestCase;
use qtism\common\datatypes\QtiDuration;
use qtism\common\datatypes\QtiPair;
use qtism\common\datatypes\QtiDirectedPair;
use qtism\common\datatypes\QtiPoint;
use qtism\common\datatypes\QtiIdentifier;
use qtism\common\datatypes\QtiBoolean;
use qtism\common\datatypes\QtiInteger;
use qtism\common\datatypes\QtiFloat;
use qtism\common\datatypes\QtiString;
use qtism\common\datatypes\QtiUri;
use qtism\common\datatypes\QtiIntOrIdentifier;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\common\MultipleContainer;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\ResponseVariable;

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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

 
/**
 *
 * @author  Jérôme Bogaerts <jerome@taotesting.com>
 * @package taoQtiCommon
 */
class LegacyStateOutputTest extends TestCase
{
    public function testStateOutputIdentifier()
    {
        $sO = new taoQtiCommon_helpers_LegacyStateOutput();
        $sO->addVariable(new ResponseVariable('RESP1', Cardinality::SINGLE, BaseType::IDENTIFIER, new QtiIdentifier('ChoiceA')));
        $sO->addVariable(new ResponseVariable('RESP2', Cardinality::MULTIPLE, BaseType::IDENTIFIER, new MultipleContainer(BaseType::IDENTIFIER, array(new QtiIdentifier('ChoiceA'), new QtiIdentifier('ChoiceB')))));
        $sO->addVariable(new OutcomeVariable('OUT1', Cardinality::ORDERED, BaseType::IDENTIFIER, new OrderedContainer(BaseType::IDENTIFIER)));
        $sO->addVariable(new OutcomeVariable('OUT2', Cardinality::MULTIPLE, BaseType::IDENTIFIER, new MultipleContainer(BaseType::IDENTIFIER, array(null, new QtiIdentifier('ChoiceC')))));

        $expectedArray = array();
        $expectedArray['RESP1'] = array('ChoiceA');
        $expectedArray['RESP2'] = array('ChoiceA', 'ChoiceB');
        $expectedArray['OUT1'] = array();
        $expectedArray['OUT2'] = array('', 'ChoiceC');
        
        $this->assertEquals($expectedArray, $sO->getOutput());
    }
    
    public function testStateOutputBoolean()
    {
        $sO = new taoQtiCommon_helpers_LegacyStateOutput();
        $sO->addVariable(new ResponseVariable('RESP1', Cardinality::SINGLE, BaseType::BOOLEAN, new QtiBoolean(true)));
        $sO->addVariable(new ResponseVariable('RESP2', Cardinality::MULTIPLE, BaseType::BOOLEAN, new MultipleContainer(BaseType::BOOLEAN, array(new QtiBoolean(false), new QtiBoolean(true)))));
        $sO->addVariable(new OutcomeVariable('OUT1', Cardinality::ORDERED, BaseType::BOOLEAN, new OrderedContainer(BaseType::BOOLEAN)));
        $sO->addVariable(new OutcomeVariable('OUT2', Cardinality::MULTIPLE, BaseType::BOOLEAN, new MultipleContainer(BaseType::BOOLEAN, array(new QtiBoolean(true), null, new QtiBoolean(false)))));
        
        $expectedArray = array();
        $expectedArray['RESP1'] = array('true');
        $expectedArray['RESP2'] = array('false', 'true');
        $expectedArray['OUT1'] = array();
        $expectedArray['OUT2'] = array('true', '', 'false');
        
        $this->assertEquals($expectedArray, $sO->getOutput());
    }
    
    public function testStateOutputInteger()
    {
        $sO = new taoQtiCommon_helpers_LegacyStateOutput();
        $sO->addVariable(new ResponseVariable('RESP1', Cardinality::SINGLE, BaseType::INTEGER, new QtiInteger(0)));
        $sO->addVariable(new ResponseVariable('RESP2', Cardinality::MULTIPLE, BaseType::INTEGER, new MultipleContainer(BaseType::INTEGER, array(new QtiInteger(-13), new QtiInteger(1337)))));
        $sO->addVariable(new OutcomeVariable('OUT1', Cardinality::ORDERED, BaseType::INTEGER, new OrderedContainer(BaseType::INTEGER)));
        $sO->addVariable(new OutcomeVariable('OUT2', Cardinality::MULTIPLE, BaseType::INTEGER, new MultipleContainer(BaseType::INTEGER, array(null, new QtiInteger(-466)))));
    
        $expectedArray = array();
        $expectedArray['RESP1'] = array('0');
        $expectedArray['RESP2'] = array('-13', '1337');
        $expectedArray['OUT1'] = array();
        $expectedArray['OUT2'] = array('', '-466');
    
        $this->assertEquals($expectedArray, $sO->getOutput());
    }
    
    public function testStateOutputFloat()
    {
        $sO = new taoQtiCommon_helpers_LegacyStateOutput();
        $sO->addVariable(new ResponseVariable('RESP1', Cardinality::SINGLE, BaseType::FLOAT, new QtiFloat(0.0)));
        $sO->addVariable(new ResponseVariable('RESP2', Cardinality::MULTIPLE, BaseType::FLOAT, new MultipleContainer(BaseType::FLOAT, array(new QtiFloat(-13.65), new QtiFloat(1337.1)))));
        $sO->addVariable(new OutcomeVariable('OUT1', Cardinality::ORDERED, BaseType::FLOAT, new OrderedContainer(BaseType::FLOAT)));
        $sO->addVariable(new OutcomeVariable('OUT2', Cardinality::MULTIPLE, BaseType::FLOAT, new MultipleContainer(BaseType::FLOAT, array(null, new QtiFloat(-466.3)))));
        $sO->addVariable(new OutcomeVariable('OUT3', Cardinality::ORDERED, BaseType::FLOAT, null));
    
        $expectedArray = array();
        $expectedArray['RESP1'] = array('0');
        $expectedArray['RESP2'] = array('-13.65', '1337.1');
        $expectedArray['OUT1'] = array();
        $expectedArray['OUT2'] = array('', '-466.3');
        $expectedArray['OUT3'] = array();
    
        $this->assertEquals($expectedArray, $sO->getOutput());
    }
    
    public function testStateOutputPoint()
    {
        $sO = new taoQtiCommon_helpers_LegacyStateOutput();
        $sO->addVariable(new ResponseVariable('RESP1', Cardinality::SINGLE, BaseType::POINT, new QtiPoint(0, 0)));
        $sO->addVariable(new ResponseVariable('RESP2', Cardinality::MULTIPLE, BaseType::POINT, new MultipleContainer(BaseType::POINT, array(new QtiPoint(-3, 5), new QtiPoint(13, 37)))));
        $sO->addVariable(new OutcomeVariable('OUT1', Cardinality::ORDERED, BaseType::POINT, new OrderedContainer(BaseType::POINT)));
        $sO->addVariable(new OutcomeVariable('OUT2', Cardinality::MULTIPLE, BaseType::POINT, new MultipleContainer(BaseType::POINT, array(new QtiPoint(0, 0), null, new QtiPoint(2, 3)))));
    
        $expectedArray = array();
        $expectedArray['RESP1'] = array(array('0', '0'));
        $expectedArray['RESP2'] = array(array('-3', '5'), array('13', '37'));
        $expectedArray['OUT1'] = array();
        $expectedArray['OUT2'] = array(array('0', '0'), '', array('2', '3'));
    
        $this->assertEquals($expectedArray, $sO->getOutput());
    }
    
    public function testStateOutputString()
    {
        $sO = new taoQtiCommon_helpers_LegacyStateOutput();
        $sO->addVariable(new ResponseVariable('RESP1', Cardinality::SINGLE, BaseType::STRING, new QtiString('String!')));
        $sO->addVariable(new ResponseVariable('RESP2', Cardinality::SINGLE, BaseType::STRING, null));
        $sO->addVariable(new ResponseVariable('RESP3', Cardinality::MULTIPLE, BaseType::STRING, new MultipleContainer(BaseType::STRING, array(new QtiString(''), new QtiString('Hello')))));
        $sO->addVariable(new OutcomeVariable('OUT1', Cardinality::ORDERED, BaseType::STRING, new OrderedContainer(BaseType::STRING)));
        $sO->addVariable(new OutcomeVariable('OUT2', Cardinality::MULTIPLE, BaseType::STRING, new MultipleContainer(BaseType::STRING, array(null, new QtiString('World')))));
    
        $expectedArray = array();
        $expectedArray['RESP1'] = array('String!');
        $expectedArray['RESP2'] = array('');
        $expectedArray['RESP3'] = array('', 'Hello');
        $expectedArray['OUT1'] = array();
        $expectedArray['OUT2'] = array('', 'World');
    
        $this->assertEquals($expectedArray, $sO->getOutput());
    }
    
    public function testStateOutputPair()
    {
        $sO = new taoQtiCommon_helpers_LegacyStateOutput();
        $sO->addVariable(new ResponseVariable('RESP1', Cardinality::SINGLE, BaseType::PAIR, new QtiPair('A', 'B')));
        $sO->addVariable(new ResponseVariable('RESP2', Cardinality::MULTIPLE, BaseType::PAIR, new MultipleContainer(BaseType::PAIR, array(new QtiPair('A', 'B'), new QtiPair('C', 'D')))));
        $sO->addVariable(new OutcomeVariable('OUT1', Cardinality::ORDERED, BaseType::PAIR, new OrderedContainer(BaseType::PAIR)));
        $sO->addVariable(new OutcomeVariable('OUT2', Cardinality::MULTIPLE, BaseType::PAIR, new MultipleContainer(BaseType::PAIR, array(new QtiPair('A', 'B'), null, new QtiPair('E', 'F')))));
    
        $expectedArray = array();
        $expectedArray['RESP1'] = array(array('A', 'B'));
        $expectedArray['RESP2'] = array(array('A', 'B'), array('C', 'D'));
        $expectedArray['OUT1'] = array();
        $expectedArray['OUT2'] = array(array('A', 'B'), '', array('E', 'F'));

        $this->assertEquals($expectedArray, $sO->getOutput());
    }
    
    public function testStateOutputDirectedPair()
    {
        $sO = new taoQtiCommon_helpers_LegacyStateOutput();
        $sO->addVariable(new ResponseVariable('RESP1', Cardinality::SINGLE, BaseType::DIRECTED_PAIR, new QtiDirectedPair('A', 'B')));
        $sO->addVariable(new ResponseVariable('RESP2', Cardinality::MULTIPLE, BaseType::DIRECTED_PAIR, new MultipleContainer(BaseType::DIRECTED_PAIR, array(new QtiDirectedPair('A', 'B'), new QtiDirectedPair('C', 'D')))));
        $sO->addVariable(new OutcomeVariable('OUT1', Cardinality::ORDERED, BaseType::DIRECTED_PAIR, new OrderedContainer(BaseType::DIRECTED_PAIR)));
        $sO->addVariable(new OutcomeVariable('OUT2', Cardinality::MULTIPLE, BaseType::DIRECTED_PAIR, new MultipleContainer(BaseType::DIRECTED_PAIR, array(new QtiDirectedPair('A', 'B'), null, new QtiDirectedPair('E', 'F')))));
    
        $expectedArray = array();
        $expectedArray['RESP1'] = array(array('A', 'B'));
        $expectedArray['RESP2'] = array(array('A', 'B'), array('C', 'D'));
        $expectedArray['OUT1'] = array();
        $expectedArray['OUT2'] = array(array('A', 'B'), '', array('E', 'F'));
    
        $this->assertEquals($expectedArray, $sO->getOutput());
    }
    
    public function testStateOutputDuration()
    {
        $sO = new taoQtiCommon_helpers_LegacyStateOutput();
        $sO->addVariable(new ResponseVariable('RESP1', Cardinality::SINGLE, BaseType::DURATION, new QtiDuration('P3DT24M')));
        $sO->addVariable(new ResponseVariable('RESP2', Cardinality::SINGLE, BaseType::DURATION, null));
        $sO->addVariable(new ResponseVariable('RESP3', Cardinality::MULTIPLE, BaseType::DURATION, new MultipleContainer(BaseType::DURATION, array(new QtiDuration('PT0S'), new QtiDuration('PT1M')))));
        $sO->addVariable(new OutcomeVariable('OUT1', Cardinality::ORDERED, BaseType::DURATION, new OrderedContainer(BaseType::DURATION)));
        $sO->addVariable(new OutcomeVariable('OUT2', Cardinality::MULTIPLE, BaseType::DURATION, new MultipleContainer(BaseType::DURATION, array(null, new QtiDuration('P3DT23S'), null))));
    
        $expectedArray = array();
        $expectedArray['RESP1'] = array('P3DT24M');
        $expectedArray['RESP2'] = array('');
        $expectedArray['RESP3'] = array('PT0S', 'PT1M');
        $expectedArray['OUT1'] = array();
        $expectedArray['OUT2'] = array('', 'P3DT23S', '');
    
        $this->assertEquals($expectedArray, $sO->getOutput());
    }
    
    public function testStateOutputUri()
    {
        $sO = new taoQtiCommon_helpers_LegacyStateOutput();
        $sO->addVariable(new ResponseVariable('RESP1', Cardinality::SINGLE, BaseType::URI, new QtiUri('http://bit.ly')));
        $sO->addVariable(new ResponseVariable('RESP2', Cardinality::SINGLE, BaseType::URI, null));
        $sO->addVariable(new ResponseVariable('RESP3', Cardinality::MULTIPLE, BaseType::URI, new MultipleContainer(BaseType::URI, array(new QtiUri('http://bit.lu'), new QtiUri('https://bit.ly')))));
        $sO->addVariable(new OutcomeVariable('OUT1', Cardinality::ORDERED, BaseType::URI, new OrderedContainer(BaseType::URI)));
        $sO->addVariable(new OutcomeVariable('OUT2', Cardinality::MULTIPLE, BaseType::URI, new MultipleContainer(BaseType::URI, array(new QtiUri('http://bit.ly'), null))));
    
        $expectedArray = array();
        $expectedArray['RESP1'] = array('http://bit.ly');
        $expectedArray['RESP2'] = array('');
        $expectedArray['RESP3'] = array('http://bit.lu', 'https://bit.ly');
        $expectedArray['OUT1'] = array();
        $expectedArray['OUT2'] = array('http://bit.ly', '');
    
        $this->assertEquals($expectedArray, $sO->getOutput());
    }
    
    public function testStateOutputIntOrIdentifier()
    {
        $sO = new taoQtiCommon_helpers_LegacyStateOutput();
        $sO->addVariable(new ResponseVariable('RESP1', Cardinality::SINGLE, BaseType::INT_OR_IDENTIFIER, new QtiIntOrIdentifier(0)));
        $sO->addVariable(new ResponseVariable('RESP2', Cardinality::SINGLE, BaseType::INT_OR_IDENTIFIER, null));
        $sO->addVariable(new ResponseVariable('RESP3', Cardinality::MULTIPLE, BaseType::INT_OR_IDENTIFIER, new MultipleContainer(BaseType::INT_OR_IDENTIFIER, array(new QtiIntOrIdentifier('ChoiceA'), new QtiIntOrIdentifier(1337)))));
        $sO->addVariable(new OutcomeVariable('OUT1', Cardinality::ORDERED, BaseType::INT_OR_IDENTIFIER, new OrderedContainer(BaseType::INT_OR_IDENTIFIER)));
        $sO->addVariable(new OutcomeVariable('OUT2', Cardinality::MULTIPLE, BaseType::INT_OR_IDENTIFIER, new MultipleContainer(BaseType::INT_OR_IDENTIFIER, array(new QtiIntOrIdentifier('ChoiceB'), new QtiIntOrIdentifier(-466), null))));
    
        $expectedArray = array();
        $expectedArray['RESP1'] = array('0');
        $expectedArray['RESP2'] = array('');
        $expectedArray['RESP3'] = array('ChoiceA', '1337');
        $expectedArray['OUT1'] = array();
        $expectedArray['OUT2'] = array('ChoiceB', '-466', '');
    
        $this->assertEquals($expectedArray, $sO->getOutput());
    }
}
