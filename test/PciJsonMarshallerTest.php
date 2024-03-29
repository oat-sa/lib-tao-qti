<?php

use PHPUnit\Framework\TestCase;
use qtism\common\datatypes\QtiFile;
use qtism\common\datatypes\files\FileSystemFileManager;

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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */


/**
 *
 * @author     Jérôme Bogaerts <jerome@taotesting.com>
 * @package    taoQtiCommon
 * @subpackage test
 */
class PciJsonMarshallerTest extends TestCase
{
    private $file;
    
    public function setUp(): void
    {
        parent::setUp();
        
        $fileManager = new FileSystemFileManager();
        $file = $fileManager->createFromData('Some text', 'text/plain');
        $this->setFile($file);
    }
    
    public function tearDown(): void
    {
        parent::tearDown();
        
        $fileManager = new FileSystemFileManager();
        $file = $this->getFile();
        $fileManager->delete($file);
    }
    
    protected function getFile()
    {
        return $this->file;
    }
    
    protected function setFile(QtiFile $file)
    {
        $this->file = $file;
    }
    
    public function testMarshallFile()
    {
        $file = $this->getFile();
        $marshaller = new taoQtiCommon_helpers_PciJsonMarshaller();
        $json = $marshaller->marshall($file);
        
        $this->assertEquals(array('base' => array('file' => array('mime' => taoQtiCommon_helpers_PciJsonMarshaller::FILE_PLACEHOLDER_MIMETYPE, 'data' => base64_encode(taoQtiCommon_helpers_PciJsonMarshaller::FILE_PLACEHOLDER_DATA)))), json_decode($json, true));
    }
}
