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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

/**
 * A class aiming at providing utility methods for the taoQtiCommon
 * extension.
 * 
 * @author Jérîome Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiCommon_helpers_Utils {
    
    /**
     * Amount of bytes to read for each 
     * read instructions while reading a
     * JSON payload. The current value is
     * 262,144 bytes -> 256 kbytes
     * 
     * @var unknown_type
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
    
}