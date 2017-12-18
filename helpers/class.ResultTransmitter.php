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

use oat\taoResultServer\models\classes\ResultStorageWrapper;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\common\datatypes\QtiFile;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\common\ResponseVariable;

/**
 * The ResultTransmitter class provides a way to transmit easily
 * a QtiSm Runtime Variable objects to a given Result Server for storage.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiCommon_helpers_ResultTransmitter {

    /**
     * be transmitted to.
     *
     * @var ResultStorageWrapper
     */
    private $resultStorage;

    /**
     * Create a new ResultTransmitter object.
     *
     * @param $storage
     */
    public function __construct(ResultStorageWrapper $storage) {
        $this->setResultStorage($storage);
    }

    /**
     * Set the result server object where the variables will be transmitted to.
     *
     * @param $storage
     */
    protected function setResultStorage(ResultStorageWrapper $storage) {
        $this->resultStorage = $storage;
    }

    /**
     * Get the StateFull result server where the variables will be transmitted to.
     *
     * @return ResultStorageWrapper
     */
    protected function getResultStorage() {
        return $this->resultStorage;
    }

    /**
     * Transmit a QtiSm Runtime Variable to the target Result Server as an Item Result.
     *
     * @param mixed $variables QtiSm Runtime Variable(s).
     * @param string $transmissionId A unique identifier that identifies uniquely the visited item.
     * @param string $itemUri An optional URI that identifies uniquely the item the $variable comes from.
     * @param string $testUri An optional URL that identifies uniquely the test the $variable comes from.
     * @throws taoQtiCommon_helpers_ResultTransmissionException If an error occurs while transmitting the Variable to the target Result Server.
     */
    public function transmitItemVariable($variables, $transmissionId, $itemUri = '', $testUri = '') {

        $itemVariableSet = [];

        if (is_array($variables) === false) {
            $variables = [$variables];
        }

        foreach ($variables as $variable) {
            $identifier = $variable->getIdentifier();

            if ($variable instanceof OutcomeVariable) {
                $value = $variable->getValue();

                $resultVariable = new taoResultServer_models_classes_OutcomeVariable();
                $resultVariable->setIdentifier($identifier);
                $resultVariable->setBaseType(BaseType::getNameByConstant($variable->getBaseType()));
                $resultVariable->setCardinality(Cardinality::getNameByConstant($variable->getCardinality()));
                $resultVariable->setValue(self::transformValue($value));

                $itemVariableSet[] = $resultVariable;
            }
            else if ($variable instanceof ResponseVariable) {
                // ResponseVariable.
                $value = $variable->getValue();

                $resultVariable = new taoResultServer_models_classes_ResponseVariable();
                $resultVariable->setIdentifier($identifier);
                $resultVariable->setBaseType(BaseType::getNameByConstant($variable->getBaseType()));
                $resultVariable->setCardinality(Cardinality::getNameByConstant($variable->getCardinality()));
                $resultVariable->setCandidateResponse(self::transformValue($value));

                // The fact that the response is correct must not be sent for built-in
                // response variables 'duration' and 'numAttempts'.
                if (!in_array($identifier, array('duration', 'numAttempts', 'comment'))) {
                    $resultVariable->setCorrectResponse($variable->isCorrect());
                }

                $itemVariableSet[] = $resultVariable;
            }
        }

        try {
            common_Logger::d("Sending Item Variables to result server.");
            $this->getResultStorage()->storeItemVariables($testUri, $itemUri, $itemVariableSet, $transmissionId);
        }
        catch (Exception $e) {
            $msg = "An error occured while transmitting one or more Outcome/Response Variable(s) to the target result server.";
            $code = taoQtiCommon_helpers_ResultTransmissionException::OUTCOME;
            throw new taoQtiCommon_helpers_ResultTransmissionException($msg, $code);
        }
    }

    /**
     * Transmit a test-level QtiSm Runtime Variable to the target Result Server as a test result.
     *
     * @param mixed $variables An OutcomeVariable object (or an OutcomeVariable array) to be transmitted to the target Result Server.
     * @param string $transmissionId A unique identifier that identifies uniquely the visited test.
     * @param string $testUri An optional URL that identifies uniquely the test the $variable comes from.
     */
    public function transmitTestVariable($variables, $transmissionId, $testUri = '') {

        $testVariableSet = [];

        if (!is_array($variables)) {
            $variables = [$variables];
        }

        foreach ($variables as $variable) {
            $resultVariable = new taoResultServer_models_classes_OutcomeVariable();
            $resultVariable->setIdentifier($variable->getIdentifier());
            $resultVariable->setBaseType(BaseType::getNameByConstant($variable->getBaseType()));
            $resultVariable->setCardinality(Cardinality::getNameByConstant($variable->getCardinality()));

            $value = $variable->getValue();
            $resultVariable->setValue(self::transformValue($value));

            $testVariableSet[] = $resultVariable;
        }

        try {
            common_Logger::d("Sending Test Variables to result server.");
            $this->getResultStorage()->storeTestVariables($testUri, $testVariableSet, $transmissionId);
        }
        catch (Exception $e) {
            $msg = "An error occured while transmitting one or more Outcome Variable(s) to the target result server.";
            $code = taoQtiCommon_helpers_ResultTransmissionException::OUTCOME;
            throw new taoQtiCommon_helpers_ResultTransmissionException($msg, $code);
        }
    }

    /**
     * Transform a QTI Datatype value to a value compliant
     * with result server.
     *
     * @param mixed $value
     * @return string
     */
    private static function transformValue($value) {
        if (gettype($value) === 'object') {

            if ($value instanceof QtiFile) {
                return taoQtiCommon_helpers_Utils::qtiFileToString($value);
            }
            else {
                return $value->__toString();
            }
        }
        else {
            return $value;
        }
    }

}