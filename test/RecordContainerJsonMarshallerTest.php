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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use qtism\common\datatypes\QtiBoolean;
use qtism\common\datatypes\QtiFloat;
use qtism\common\datatypes\QtiIdentifier;
use qtism\common\datatypes\QtiInteger;
use qtism\common\datatypes\QtiIntOrIdentifier;
use qtism\common\datatypes\QtiString;
use qtism\common\datatypes\QtiUri;
use qtism\runtime\common\RecordContainer;

class RecordContainerJsonMarshallerTest extends TestCase
{
    public function testMarshalRecordFileToJson()
    {
        $record = new RecordContainer();
        $record->offsetSet('recordQtiString', new QtiString('some_value'));
        $record->offsetSet('recordQtiBool', new QtiBoolean(false));
        $record->offsetSet('recordIntOrIdentifier', new QtiIntOrIdentifier('Id123'));
        $record->offsetSet('recordQtiInteger', new QtiInteger(123));
        $record->offsetSet('recordQtiFloat', new QtiFloat(123.12));
        $record->offsetSet('recordQtiUri', new QtiUri('someUri'));
        $record->offsetSet('recordQtiIdentifier', new QtiIdentifier('qtiId'));
        $result = taoQtiCommon_helpers_RecordContainerJsonMarshaller::marshalRecordFileToJson($record);
        self::assertIsString($result);
        self::assertIsArray(json_decode($result, true));
    }
}
