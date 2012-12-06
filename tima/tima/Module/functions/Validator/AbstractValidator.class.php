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

/* @use Module_Function */
require_once 
    dirname(dirname(dirname(__FILE__))) . DS . 'Function.class.php';

/* 英小文字文字列の正規表現 */
define(
    'VALIDATE_ALPHABET_LOWER', 
    '(?:[a-z])');

/* 英大文字文字列の正規表現 */
define(
    'VALIDATE_ALPHABET_UPPER', 
    '(?:[A-Z])');

/* 英小・大文字列の正規表現 */
define(
    'VALIDATE_ALPHABET', 
    '(?:' . VALIDATE_ALPHABET_LOWER . '|' . VALIDATE_ALPHABET_UPPER . ')');

/* 空白文字列の正規表現 */
define(
    'VALIDATE_SPACER', 
    '(?:\s)');

/* 区切り文字列の正規表現 */
define(
    'VALIDATE_PUNCTUATION', 
    '(?:' . VALIDATE_SPACER . '\.,;\:&"\'\?\!\(\))');

/**
 * トップレベル・ドメインの正規表現
 * 2006年08月のデータで最終確認しています
 */
define(
    'VALIDATE_TOP_LV_DOMAIN', 
    '(?:' . 
        'ac|ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|' . 
        'ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|' . 
        'ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cu|cv|cx|cy|cz|' . 
        'de|dj|dk|dm|do|dz|' . 
        'ec|edu|ee|eg|er|es|et|eu|' . 
        'fi|fj|fk|fm|fo|fr|' . 
        'ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|' . 
        'hk|hm|hn|hr|ht|hu|' . 
        'id|ie|il|im|in|info|int|io|iq|ir|is|it|' . 
        'je|jm|jo|jp|' . 
        'ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|' . 
        'la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|' . 
        'ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|' . 
        'mz|' . 
        'na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nu|nz|' . 
        'om|org|' . 
        'pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|' . 
        'qa|' . 
        're|ro|ru|rw|' . 
        'sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|' . 
        'tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|' . 
        'ua|ug|uk|um|us|uy|uz|' . 
        'va|vc|ve|vg|vi|vn|vu|' . 
        'wf|ws|' . 
        'ye|yt|yu|' . 
        'za|zm|zw' . 
        ')');

/* URLエンコードされた文字列の正規表現 */
define(
    'VALIDATE_URL_ENCODE', 
    '(?:%[0-9a-fA-F]{2})');

/**
 * 検証モジュールの機能（抽象）クラス
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: AbstractValidator.class.php 4 2007-06-20 07:16:44Z do_ikare $
 * @abstract
 */
class Validator_AbstractValidator extends Module_Function {}
