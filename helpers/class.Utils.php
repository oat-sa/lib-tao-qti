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
 * Copyright (c) 2014-2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */


use qtism\common\datatypes\QtiFile;
use qtism\common\datatypes\QtiIdentifier;
use qtism\common\datatypes\QtiPair;
use qtism\common\datatypes\QtiDirectedPair;
use qtism\common\datatypes\QtiBoolean;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\Variable;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;

/**
 * A class aiming at providing utility methods for the taoQtiCommon
 * extension.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiCommon_helpers_Utils {
    
    /**
     * Amount of bytes to read for each 
     * read instructions while reading a
     * JSON payload. The current value is
     * 262,144 bytes -> 256 kbytes
     * 
     * @var integer
     */
    const JSON_PAYLOAD_CHUNK_SIZE = 262144;
    
    /**
     * Reads a JSON Payload from 'php://input' and decodes it
     * as an associative array
     * 
     * @return boolean|array An associative array representing the JSON payload or false if an error occurs.
     */
    static public function readJsonPayload() {
        $fp = fopen('php://input', 'rb');
        
        if ($fp === false) {
            return false;
        }
        
        $payload = '';
        
        while (feof($fp) !== true) {
            $payload .= fread($fp, self::JSON_PAYLOAD_CHUNK_SIZE);
        }
        
        @fclose($fp);
        
        return @json_decode($payload, true);
    }
    
    /**
     * Marshall a QTI File datatype into a binary string. The resulting binary string constitution
     * is the following:
     * 
     * * The length of the file name, unsigned short.
     * * The file name, string.
     * * The length of the mime type, unsigned short.
     * * The mime type, string.
     * * The binary content of the file, string.
     * 
     * @param File $file
     * @return string
     */
    static public function qtiFileToString(QtiFile $file) {
        $string = '';
        $filename = $file->getFilename();
        $mimetype = $file->getMimeType();
        $filenameLen = strlen($filename);
        $mimetypeLen = strlen($mimetype);
        
        $string .= pack('S', $filenameLen);
        $string .= $filename;
        
        $string .= pack('S', $mimetypeLen);
        $string .= $mimetype;
        
        // Avoid invalid data for serialize
        // (This could make me cry...)
        $string .= $file->getData();
        return $string;
    }
    
    /**
     * Whether or not a given QTI Variable contains the QTI Placeholder File.
     * 
     * @param Variable $variable
     * @return boolean
     */
    static public function isQtiFilePlaceHolder(Variable $variable) {
        
        $correctBaseType = $variable->getBaseType() === BaseType::FILE;
        $correctCardinality = $variable->getCardinality() === Cardinality::SINGLE;
        
        if ($correctBaseType === true && $correctCardinality === true) {
            
            $value = $variable->getValue();
            $notNull = $value !== null;
            $mime = taoQtiCommon_helpers_PciJsonMarshaller::FILE_PLACEHOLDER_MIMETYPE;
            
            if ($notNull === true && $value->getMimeType() === $mime) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Whether or not a given QTI Variable contains a QTI File value.
     * 
     * @param Variable $variable
     * @param boolean $considerNull Whether or not to consider File variables containing NULL variables.
     * @return boolean
     */
    static public function isQtiFile(Variable $variable, $considerNull = true) {
        
        $correctBaseType = $variable->getBaseType() === BaseType::FILE;
        $correctCardinality = $variable->getCardinality() === Cardinality::SINGLE;
        $nullConsideration = ($considerNull === true) ? true : $variable->getValue() !== null;
        
        return $correctBaseType === true && $correctCardinality === true && $nullConsideration === true;
    }
    
    /**
     * Transform a TAO serialized result value to its QtiDatatype equivalent.
     * 
     * This method transforms a TAO serialized result $value to a the equivalent QtiDatatype depending
     * on result's $cardinality and $baseType.
     * 
     * Note: at the present time, this implementation only supports 'single', 'multiple', and 'ordered' cardinality in conjunction
     * with the 'identifier', 'boolean', 'pair' or 'directedPair' baseType.
     * 
     * @param string $cardinality i.e. 'ordered', 'multiple' or 'single'.
     * @param string $basetype i.e. 'identifier'
     * @param string $value A TAO serialized result value e.g. '<choice1 choice2 choice3>'
     * @return mixed A QtiDatatype object or null in case of no possibility to transform $value in the appropriate target $cardinality and $basetype.
     */
    static public function toQtiDatatype($cardinality, $basetype, $value)
    {
        // @todo support all baseTypes
        $datatype = null;
        
        if (is_string($value) && empty($value) === false && $cardinality !== 'record' && ($basetype === 'identifier' || $basetype === 'pair' || $basetype === 'directedPair' || $basetype === 'boolean')) {
            if ($cardinality !== 'simple') {
                $value = trim($value, "<>[]");
                $value = explode(';', $value);
            } else {
                $value = array($value);
            }
            
            if (count($value) === 1 && empty($value[0]) === true) {
                $value = array();
            }
            
            $value = array_map(
                function($val) {
                    return trim($val);
                },
                $value
            );
            
            $qtiBasetype = BaseType::getConstantByName($basetype);
            $datatype = ($cardinality === 'ordered') ? new OrderedContainer($qtiBasetype) : new MultipleContainer($qtiBasetype);
            foreach ($value as $val) {
                try {
                    switch ($basetype) {
                        case 'identifier':
                            $datatype[] = new QtiIdentifier($val);
                            break;
                            
                        case 'pair':
                            $pair = explode("\x20", $val);
                            if (count($pair) === 2) {
                                $datatype[] = new QtiPair($pair[0], $pair[1]);
                            }
                            break;
                            
                        case 'directedPair':
                            $pair = explode("\x20", $val);
                            if (count($pair) === 2) {
                                $datatype[] = new QtiDirectedPair($pair[0], $pair[1]);
                            }
                            break;
                            
                        case 'boolean':
                            if ($val === 'true') {
                                $datatype[] = new QtiBoolean(true);
                            } elseif ($val === 'false') {
                                $datatype[] = new QtiBoolean(false);
                            } else {
                                $datatype[] = new QtiBoolean($val);
                            }
                            break;
                    }
                } catch (InvalidArgumentException $e) {
                    $datatype = null;
                    break;
                }
            }
            
            $datatype = ($cardinality === 'single') ? (isset($datatype[0]) ? $datatype[0] : null) : $datatype;
        }
        
        return $datatype;
    }
}
