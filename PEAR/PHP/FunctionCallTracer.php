<?php

/**
 * Function Call Tracer
 *
 * Creates a function calls debug trace. Functions arguments, returned parameters
 * and watched variables are reported in the same section for each function call.
 * The trace is available as an array, or can be displayed or written in a file.
 * Traced variables can be processed by provided user functions for displaying
 * purposes.
 *
 * This package is not a replacement for full fledged PHP debuggers. It is
 * useful for (1) remote debugging, (2) to debug a complex sequence of function
 * calls, (3) to display non text variables in a user readable format.
 *
 * (1) Remote debugging is sometimes the only option to debug a package that
 * works fine on your system, e.g. a 32-bit OS, but breaks on a different system,
 * e.g. a 64-bit OS, which you have no access to. A remote user who has the
 * latter OS could run the package, then send you the trace for analysis.
 *
 * (2) It is sometimes difficult not to loose track of functions calls in some
 * live debugging sessions even with top notch PHP editor/debuggers. The trace
 * produced by this package may come handy and is easy to use in combination
 * with the source code to track calls and variables.
 *
 * (3) Some variables native format does not always display well, typically:
 * packed data and UTF-8 strings. They can be converted as they are being traced
 * to a readable format by provided user functions. For example: converting
 * binary strings to hexadecimal, or UTF-8 string to Unicode.
 *
 * Fully tested with phpUnit. Code coverage test close to 100%.
 *
 * Usage including trace examples is fully documented in docs/examples files.
 *
 * PHP version 5
 *
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * + Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * + Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation and/or
 * other materials provided with the distribution.
 * + The name of its contributors may not be used to endorse or promote products
 * derived from this software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  PHP
 * @package   PHP_FunctionCallTracer
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2007 Michel Corne
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   SVN: $Id: FunctionCallTracer.php 18 2007-08-04 09:05:18Z mcorne $
 * @link      http://pear.php.net/package/PHP_FunctionCallTracer
 */

/**
 * Function Call Tracer
 *
 * Traces functions arguments, returned parameters, and watched variables.
 * Sets user functions and processes any of the above.
 *
 * Main methods:
 * + self::traceArguments() traces the function arguments.
 * + self::traceReturn() traces the returned parameters by the function.
 * + self::traceVariables() traces variables within the function.
 * + self::setUserFunctions() sets user functions to process variables.
 * + self::processVariables() processes variables with user functions.
 * + self::getTrace() returns the function calls trace.
 * + self::putTrace() displays or writes the function calls trace in a file.
 *
 * Usage including trace examples is fully documented in docs/examples files.
 *
 * Some basic examples:
 * <pre>
 * Example 1: tracing argument: $a, variable: $b, returned parameter: $c
 *
 * require_once 'PHP/FunctionCallTracer.php';
 *
 * function foo($a)
 * {
 *          PHP_FunctionCallTracer::traceArguments();
 *
 *          $b = strtoupper($a);
 *          PHP_FunctionCallTracer::traceVariables($b);
 *
 *          $c = true;
 *          PHP_FunctionCallTracer::traceReturn($c);
 *          return $c;
 * }
 *
 * $c = foo('foo');
 * PHP_FunctionCallTracer::putTrace();
 *
 * Example 2: tracing and rounding variable: $a
 *
 * require_once 'PHP/FunctionCallTracer.php';
 *
 * PHP_FunctionCallTracer::setUserFunctions('round');
 *
 * function bar($a)
 * {
 *          $a *= 2;
 *          PHP_FunctionCallTracer::traceVariables($a);
 *          PHP_FunctionCallTracer::processVariables();
 *
 *          return $a;
 * }
 *
 * $a = bar(1.23);
 * PHP_FunctionCallTracer::putTrace();
 * </pre>
 *
 * @category  PHP
 * @package   PHP_FunctionCallTracer
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2007 Michel Corne
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   Release:@package_version@
 * @link      http://pear.php.net/package/PHP_FunctionCallTracer
 */
class PHP_FunctionCallTracer
{
    /**
     * The list of function calls
     *
     * @var    array
     * @access private
     * @static
     */
    private static $calls = array();

    /**
     * The function call ID stack
     *
     * @var    array
     * @access private
     * @static
     */
    private static $callIdStack = array();

    /**
     * The list of invalid user functions
     *
     * @var    array
     * @access private
     * @static
     */
    private static $invalidFct = array();

    /**
     * The multi-user function flag
     *
     * True if 2 or more user-functions, false otherwise
     *
     * @var    boolean
     * @access private
     * @static
     */
    private static $isMultiUserFct = false;

    /**
     * The list objects calling the methods being traced
     *
     * @var    array
     * @access private
     * @static
     */
    private static $objects = array();

    /**
     * The list of callable user functions
     *
     * @var    array
     * @access private
     * @static
     */
    private static $userFct = array();

    /**
     * Filters some key/values of a function calls trace
     *
     * @param  array  $trace the function call trace
     * @param  array  $keys  the keys to keep
     * @return array  the filtered function call trace
     * @access public
     * @static
     */
    public static function filterTrace($trace, $keys)
    {
        is_array($trace) or $trace = array($trace);
        is_array($keys) or $keys = array($keys);

        $filtered = array();
        foreach($keys as $key) {
            isset($trace[$key]) and $filtered[$key] = $trace[$key];
        }

        return $filtered;
    }

    /**
     * Creates a function call ID stack key
     *
     * Based on the backtrace. Ignores the last function call.
     * Retains only the data clearly identifying the call:
     * 'file', 'line', 'class', 'type', 'function'. Serializes the backtrace to
     * build the key.
     *
     * @param  array  $trace the function call backtrace
     * @return array  the function call ID stack key
     * @access public
     * @static
     */
    public static function createStackKey($trace)
    {
        array_shift($trace);
        foreach($trace as &$value) {
            $value = self::filterTrace($value,
                array('file', 'line', 'class', 'type', 'function'));
        }
        return serialize($trace);
    }

    /**
     * Gets the function calls trace
     *
     * This method is usually called at the end of a debugging session.
     *
     * @param  boolean $getPhpInfo the phpinfo is to be captured if true,
     *                             or not if false, the default is true
     * @return array   the function calls trace
     * @access public
     * @static
     */
    public static function getTrace($getPhpInfo = true)
    {
        // captures the PHP version details and the current date
        $trace['php_uname'] = php_uname();
        date_default_timezone_set('UTC');
        $trace['date'] = date(DATE_COOKIE);
        // captures the user functions and the unavailable ones
        self::$userFct and $trace['user_functions'] =
        array_map(array(__CLASS__, 'tidyMethodName'), self::$userFct);
        self::$invalidFct and $trace['invalid_user_functions'] =
        array_map(array(__CLASS__, 'tidyMethodName'), self::$invalidFct);
        // captures the function/methods calls and the objects details
        $trace['calls'] = self::$calls;
        self::$objects and $trace['objects'] = self::$objects;
        // captures the PHP info
        $getPhpInfo and ob_start() and phpinfo() and
        $trace['phpinfo'] = ob_get_contents() and ob_end_clean();

        return $trace;
    }

    /**
     * Determines if the provided user function is callable
     *
     * @param  array   $function the user function, e.g. 'dechex',
     *                           or object method, e.g. array($object, 'foo'),
     *                           or static method, e.g. array('foo', 'bar')
     * @return boolean true if callable, false if not
     * @access public
     * @static
     */
    public static function isCallable($function)
    {
        $isCallable = false;
        if (is_callable($function, true)) {
            // a syntax compliant function or object/class method
            if (is_string($function) or is_object($function[0])) {
                // checks if a callable function, e.g. 'dechex',
                // or a callable object method, e.g array($object, 'foo')
                $isCallable = is_callable($function);
            } else if (class_exists($function[0])) {
                // checks if the class method exists, e.g. array('Foo', 'bar')
                $isCallable = in_array($function[1], get_class_methods($function[0]));
            }
        }

        return $isCallable;
    }

    /**
     * Processes a set of variables with provided user functions
     *
     * It is usually called after self::traceArguments(), self::traceReturn(),
     * or self::traceVariables(). It will process the variables passed to
     * the above functions.
     * This method is used in 2 different ways.
     *
     * (1) If the package is used with one user function,
     * e.g. self::setUserFunctions('dechex'), this method is expecting
     * as arguments the keys of variables that were passed to the
     * above functions. Examples when called after
     * self::traceArguments($a, $b, $c):
     * + self::processVariables() => $a, $b, $c are processed.
     * + self::processVariables(true) => $a, $b, $c are processed.
     * + self::processVariables(0) => $a only is processed.
     * + self::processVariables(0,1) => $a and $b only are processed.
     *
     * (2) If the package is used with 2 or more user function :
     * e.g. self::setUserFunctions('dechex', 'bin2hex'), this method is expecting
     * for each argument a set of keys of variables that were passed to the
     * above functions. Examples when called after
     * self::traceArguments($a, $b, $c):
     * + self::processVariables() => $a, $b, $c are processed by dechex.
     * + self::processVariables(null, true) => $a, $b, $c are processed by bin2hex.
     * + self::processVariables(0, 1) => $a is processed by dechex and $b by bin2hex.
     * + self::processVariables(0, array(1, 2)) => $a is processed by dexhex, and $b and $c by bin2hex.
     *
     * Automatically calls self::traceArguments() if this method is the first one
     * called within a function.
     *
     * @return boolean false if this method is not called by a function,
     *                 true otherwise (actually the function call trace detail)
     * @access public
     * @static
     */
    public static function processVariables()
    {
        $trace = debug_backtrace();
        if (!isset($trace[1])) {
            // this method is expected to be called by a function
            return false;
        }
        // gets the function call ID
        // creates a call entry for the function if absent
        $key = self::createStackKey($trace);
        isset(self::$callIdStack[$key]) or self::traceArguments();
        $id = self::$callIdStack[$key];
        // extracts the function call last entered arguments, variables or
        // returned parameters
        $entry = end(self::$calls[$id]);
        if (!isset($entry['args'])) {
            // extracts the last traced variable
            end($entry);
            list($lastVarKey, $entry) = each($entry);
        }
        $variables = $entry['args'];
        // captures the list of variables keys to process
        $toProcess = func_get_args();
        // sets all variables to be processed if empty
        $toProcess === array() and $toProcess = array(true);
        self::$isMultiUserFct or $toProcess === array(true) or
        $toProcess = array($toProcess);
        // sets all variables to be processed if true for the corresponding
        // user function, e.g. array(1 => true),
        // ignores the other function variable keys
        $key = array_search(true, $toProcess, true);
        $key === false or $toProcess = array($key => array_keys($variables));

        $processed = array();
        foreach($toProcess as $fctID => $varKeys) {
            is_array($varKeys) or $varKeys = array($varKeys);
            foreach($varKeys as $key) {
                // processes the variable with the user function
                is_array($key) or isset($variables[$key]) and
                isset(self::$userFct[$fctID]) and $processed[$key] =
                call_user_func(self::$userFct[$fctID], $variables[$key]);
            }
        }
        // resorts and adds the processed variables to the function calls trace
        ksort($processed);
        $varType = key(self::$calls[$id]);
        if (isset($lastVarKey)) {
            self::$calls[$id][$varType][$lastVarKey]['args_p'] = $processed;
        } else {
            self::$calls[$id][$varType]['args_p'] = $processed;
        }

        return self::$calls[$id];
    }

    /**
     * Displays or writes the function calls trace in a file
     *
     * This method is usually called at the end of a debugging session.
     *
     * @param  string  $file       the name of the file, or the standard ouput
     *                             if empty or by default
     * @param  boolean $getPhpInfo the phpinfo is to be captured if true,
     *                             or not if false, the default is true for a file
     *                             and false for the standard output
     * @return array   the function calls trace
     * @access public
     * @static
     */
    public static function putTrace($file = '', $getPhpInfo = null)
    {
        if ($file) {
            // gets the trace including the phpinfo by default
            // stores the trace in a file
            is_null($getPhpInfo) and $getPhpInfo = true;
            $trace = self::getTrace($getPhpInfo);
            // stores the trace in a file
            $content = print_r($trace, true);
            @file_put_contents($file, $content) or
            exit("Error! Cannot write the trace in the file: $file");
        } else {
            // gets the trace without the phpinfo by default
            // displays the trace to the standard output
            is_null($getPhpInfo) and $getPhpInfo = false;
            $trace = self::getTrace($getPhpInfo);
            print_r($trace);
        }

        return $trace;
    }

    /**
     * Resets the class settings and working variables
     *
     * This method is needed when 2 or more debug sessions are run in a row.
     * There is no need to call this method before self::setUserFunctions()
     * which calls this method internally.
     *
     * @return void
     * @access public
     * @static
     */
    public static function reset()
    {
        self::$calls = array();
        self::$callIdStack = array();
        self::$invalidFct = array();
        self::$isMultiUserFct = false;
        self::$objects = array();
        self::$userFct = array();
    }

    /**
     * Sets provided user functions
     *
     * Each argument is expected to be a callable function or
     * object/class method. Examples:
     * + self::setUserFunctions('dechex') => the PHP function dexhex
     * + self::setUserFunctions(array($object, 'foo')) => the object method foo
     * + self::setUserFunctions(array('foo', 'bar')) => the class method foo::bar
     * + self::setUserFunctions('dechex', array('foo', 'bar')) => a combination of the PHP function dexhex and the class method foo::bar.
     *
     * The first argument is the user function/method #0, the second is #1 etc...
     * This order number is used when passing variables keys to
     * self::processVariables().
     *
     * Verifies that the user functions/methods are callable.
     * Reports which ones are callable or not in the function calls trace.
     *
     * @return array  the 2 sets of callable and invalid functions/methods
     * @access public
     * @static
     */
    public static function setUserFunctions()
    {
        self::reset();
        foreach(func_get_args() as $fctID => $function) {
            if (self::isCallable($function)) {
                // captures the valid function/method
                self::$userFct[$fctID] = $function;
            } else {
                // captures the invalid function/method
                self::$invalidFct[$fctID] = $function;
            }
        }
        self::$isMultiUserFct = (count(self::$userFct) +
            count(self::$invalidFct)) > 1;

        return array(self::$userFct, self::$invalidFct);
    }

    /**
     * Tidies the object/class method name
     *
     * @param  mixed  $function a function name, e.g. 'dechex', or an object
     *                          method, e.g. array('class' => $object,
     *                          'type' => '->', 'function' => 'foo'), or a class
     *                          method, e.g. array('class' => 'foo',
     *                          'type' => '::', 'function' => 'bar')
     * @return string the tidied function/method name, e.g. 'dexhex', or
     *                'blah=>foo', or 'foo::bar'
     * @access public
     * @static
     */
    public static function tidyMethodName($function)
    {
        if (is_array($function)) {
            // a class/object method, e.g. array('Foo', 'bar')
            // extracts the class and method names
            $class = current($function) or $class = '???';
            $method = next($function) and is_string($method) or $method = '???';
            if (is_object($class)) {
                // an object method call, gets the class name
                $class = get_class($class);
                $separator = '->';
            } else {
                // a class static method call
                is_string($class) or $class = '???';
                $separator = '::';
            }
            // implodes the class and method names
            $function = "$class$separator$method";
        } else {
            // else: a PHP function
            $function and is_string($function) or $function = '???';
        }

        return $function;
    }

    /**
     * Traces the argments passed to a function
     *
     * Usually called within a function before the rest of the function code.
     * Normally, not expecting any arguments since they are captured automaticly.
     * However, if the function expects arguments passed by reference, the
     * arguments should be passed explicitly, due to PHP Bug 42058:
     * "debug_backtrace messes up with references". For example, given the
     * function foo($a, &$b): self::traceArguments($a, $b) should be used
     * instead of self::traceArguments() to trace arguments properly.
     *
     * Called automaticly by self::traceReturn(), self::traceVariables() or
     * self::processVariables() if it is not called explicitly before any of
     * those methods within the function.
     *
     * It should always be called if the function itself calls other functions
     * that are being traced as well, in order to keep the function calls in
     * order within the trace.
     *
     * @return boolean false if this method is not called by a function,
     *                 true otherwise (actually the function call trace detail)
     * @access public
     * @static
     */
    public static function traceArguments()
    {
        $trace = debug_backtrace();
        if (!isset($trace[1])) {
            // this method is expected to be called by a function
            return false;
        }
        // skips one level if called internally by traceReturn()
        isset($trace[1]['class']) and $trace[1]['class'] == __CLASS__ and
        array_shift($trace);
        // skips 2 levels if called internally via traceVariables()
        $tracingReturn = isset($trace[0]['file']) or
        array_shift($trace) and array_shift($trace);
        // extracts the function call position
        $call = self::filterTrace($trace[1], array('file', 'line'));
        // reformats the function name
        $function = self::filterTrace($trace[1], array('class', 'type', 'function'));
        $call['function'] = implode('', $function);
        // extracts the function entry point position and arguments
        // cannot trust $trace[0]['args']: use func_get_args() if arguments passed
        $in = self::filterTrace($trace[0], array('file', 'line'));
        $in['args'] = ($args = func_get_args())? $args : $trace[1]['args'];
        // captures the function call details
        $id = count(self::$calls);
        self::$calls[] = array('call' => $call, 'in' => $in);
        if (isset($trace[1]['object'])) {
            // captures or makes a reference to the (method) object details
            $ref = array_search($trace[1]['object'], self::$objects, true);
            self::$objects[$id] = $ref === false? $trace[1]['object'] : "same as #$ref";
        }
        // adds the function call to the call stack
        $key = self::createStackKey($trace);
        self::$callIdStack[$key] = $id;

        return self::$calls[$id];
    }

    /**
     * Traces the return parameters
     *
     * Optionally called within a function before a return statement.
     * Normally expecting the parameters returned by the function, plus
     * optionally any other variables. For example, given the return($a, $b)
     * statement in a function: self::traceReturn($a, $b) should
     * preceed this return statement.
     *
     * Automatically calls self::traceArguments() if it is not called explicitly
     * before this method within the function.
     *
     * @return boolean false if this method is not called by a function,
     *                 true otherwise (actually the function call trace detail)
     * @access public
     * @static
     */
    public static function traceReturn()
    {
        $trace = debug_backtrace();
        // skips 2 levels if called internally by traceVariables()
        $tracingReturn = isset($trace[0]['file']) or array_shift($trace) and array_shift($trace);
        if (!isset($trace[1])) {
            // this method is expected to be called by a function
            return false;
        }
        // extracts the function return position and parameters
        $out = self::filterTrace($trace[0], array('file', 'line'));
        $out['args'] = func_get_args();
        // gets the function call ID, creates a call entry for the function if absent
        $key = self::createStackKey($trace);
        isset(self::$callIdStack[$key]) or self::traceArguments();
        $id = self::$callIdStack[$key];
        // adds the return parameters or watched variables to the function trace
        if ($tracingReturn) {
            self::$calls[$id]['out'] = $out;
        } else {
            self::$calls[$id]['watches'][] = $out;
        }

        return self::$calls[$id];
    }

    /**
     * Traces variables used by a function
     *
     * Expecting a list of variables. For example, self::traceVariables($a, $b).
     *
     * Automatically calls self::traceArguments() if it is not called explicitly
     * before this method within the function.
     *
     * @return boolean false if this method is not called by a function,
     *                 true otherwise (actually the function call trace detail)
     * @access public
     * @static
     */
    public static function traceVariables()
    {
        $args = func_get_args();
        return call_user_func_array(array(__CLASS__, 'traceReturn'), $args);
    }
}

?>