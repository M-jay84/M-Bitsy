<?php

namespace App\Libs;

use ReflectionFunction;
use ReflectionMethod;

//start add 19.01.2020 DrTech76, Exception handler verbose output
/**
 * Prepares verbose output of the debug backtrace, for inclusion in trace and debug outputs and logging
 * Parameters:
 * @param backtrace - the output of a call to debug_backtrace() function
 * @param depth - How many levels back to read the traces for
 * Returns:
 * @return string
 **/
class Errors
{
    // Set Exception Handler
    public function __construct(){
        set_exception_handler(array($this, 'handleUncaughtException'));
    }

    // Error Dispaly Handler
    public function error_handler($e = false)
    {
        if ($e) {
            error_reporting(0);
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            ini_set('error_log', LOG_PATH . '/errors_log.txt');
            //set_error_handler(array($this, 'error_runtime_handler'));
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
        }
    }

    public function get_verbose_backtrace($backtrace = null, $depth = 0)
    {
        $skipFirst = false;
        $output = array();
        if (!(isset($depth) and is_numeric($depth))) { //no limit
            $depth = 0;
        } else {
            $depth = intval($depth);
        }

        if (!(isset($backtrace) and is_array($backtrace))) {
            if (function_exists("ReflectionFunction")) {
                $dbtRef = new ReflectionFunction("debug_backtrace");
                if ($dbtRef->getNumberOfParameters() == 2) {
                    $backtrace = debug_backtrace(true, $depth);
                } else {
                    $backtrace = debug_backtrace();
                }
            } else {
                $backtrace = debug_backtrace();
            }

            $skipFirst = true; //the first item will be this function so skip accordingly
        }

        foreach ($backtrace as $currDepth => $trace) {
            if ($skipFirst === true and $currDepth === 0) {
                continue;
            }

            if (!(isset($trace["file"]) or isset($trace["function"]))) {
                continue;
            }

            $isObject = (isset($trace["class"]));
            $function = $trace["function"];

            //$output[]="Function: ReflectionMethod exists ".var_export(function_exists("ReflectionMethod"),true);
            //$output[]="Function: ReflectionFunction exists ".var_export(function_exists("ReflectionFunction"),true);
            if ($isObject === true) {
                if (function_exists("ReflectionMethod")) {
                    $funcParamsRef = new ReflectionMethod($trace["class"], $trace["function"]);
                }
                $function = $trace['class'] . $trace['type'] . $function;
            } else {
                if (function_exists("ReflectionFunction")) {
                    $funcParamsRef = new ReflectionFunction($trace["function"]);
                }
            }

            $currTrace = "\n[" . $currDepth . "] File: " . ((isset($trace["file"])) ? $trace["file"] : "Undefined") . " Line: " . ((isset($trace["line"])) ? $trace["line"] : "Undefined");
            $currTrace .= "\nFunction: " . $function;
            if (isset($trace["args"])) {
                $currTrace .= "\nCall parameters";

                if (isset($funcParamsRef)) {
                    $funcParams = $funcParamsRef->getParameters();
                    foreach ($funcParams as $fpIndex => $funcParam) {
                        $currTrace .= "\nParam: " . $funcParam["name"] . " Passed value: " . ((isset($trace["args"][$fpIndex])) ? var_export($trace["args"][$fpIndex], true) : "[Not passed]: " . (($funcParam->isDefaultValueAvailable() === true) ? "[Using the Default value]" . var_export($funcParam->getDefaultValue(), true) : "[No Default value is available]"));
                    }
                } else {
                    foreach ($trace["args"] as $fpIndex => $funcParam) {
                        $currTrace .= "\nParam[" . $fpIndex . "]: Passed value: " . var_export($trace["args"][$fpIndex], true);
                    }
                }
            }

            $output[] = $currTrace;
        }

        if (count($output) > 0) {
            $output = array_merge(array("Call Stack"), $output);
        }

        return join("\n", $output);
    }

    // Debug Complex Exception Log & Redirect
    public function handleUncaughtException($e)
    {
        // Construct the error string
        $error = "\nUncaught Exception: " . ($message = date("Y-m-d H:i:s - "));
        $error .= $e->getMessage() . " in file " . $e->getFile() . " on line " . $e->getLine() . "\n";
        // Add verbose trace and debug output
        $error .= "\n" . $this->get_verbose_backtrace($e->getTrace());
        // Log details of error in a file
        error_log($error, 3, LOG_PATH . '/exception_log.txt');
        $_SESSION['FLASH'] = LANG["OOPS_ERR"];
        $data = [
            'page_title' => 'Message',
            'V_msg' => LANG["OOPS_ERR"],
            'V_url' => URLROOT,
            'refresh'  => 2
        ];
        extract($data);
        require_once VIEW_PATH . "/inc/error-page.php";
        exit();
    }

    // Log RunTime Errors
    public function error_runtime_handler($errno, $errstr, $errfile, $errline)
    {
        $date = date('d M Y H:i:s');
        switch ($errno) {
            case E_USER_ERROR:
                $err_msg = 'Custom ERROR [' . $errno . '] ' . $errstr . "\n" .
                    ' Fatal mistake! PHP ' . PHP_VERSION . " (" . PHP_OS . ")\n" .
                    ' Completion of work...';
                break;
            case E_USER_WARNING:
                $err_msg =  'Custom WARNING [' . $errno . '] ' . $errstr;
                break;
            case E_USER_NOTICE:
                $err_msg =  'Custom NOTICE [' . $errno . '] ' . $errstr;
                break;
            default:
                $err_msg = 'Message: Unknown error: [' . $errno . '] ' . $errstr;
                break;
        }
        $err_msg = "\n" .
            'Message: ' . $err_msg . "\n" .
            'Date: ' . $date . "\n" .
            'Line: ' . $errline . "\n" .
            'File: ' . $errfile . "\n";
        file_put_contents(LOG_PATH . '/runtime_errors.txt', $err_msg, FILE_APPEND);

        if ($errno === E_USER_ERROR) {
            exit(1);
        }

        return true;
    }

}