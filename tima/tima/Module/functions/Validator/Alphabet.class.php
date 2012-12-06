<?php

/**
 * The tiny modules for web application
 * - PHP versions 4 -
 * 
 * @category  web application framework
 * @package   tima
 * @author    IKEDA Youhey <youhey.ikeda@gmail.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright 2007 IKEDA Youhey
 *     Licensed under the Apache License, Version 2.0 (the "License"); 
 *     you may not use this file except in compliance with the License. 
 *     You may obtain a copy of the License at 
 *         http://www.apache.org/licenses/LICENSE-2.0 
 *     Unless required by applicable law or agreed to in writing, software 
 *     distributed under the License is distributed on an "AS IS" BASIS, 
 *     WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. 
 *     See the License for the specific language governing permissions and 
 *     limitations under the License.
 * @version  1.0.0
 */

/**
 * 文字列がアルファベットとして評価できるかを検証
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: Alphabet.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Validator_Alphabet extends Validator_AbstractValidator
{

    /**
     * 文字列が数値として評価できるかを検証
     * 
     * 
     * 
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        $regex = sprintf('(?:%1$s)+(?:%1$s| )*', VALIDATE_ALPHABET);

        return 
            (bool)preg_match("/^${regex}$/", $attribute);
    }
}
