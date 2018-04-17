<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace ActiveGenerator\gii;

use ReflectionClass;
use ActiveGenerator\base\Model;

/**
 * This is the base class for all generator classes.
 *
 * A generator instance is responsible for taking user inputs, validating them,
 * and using them to generate the corresponding code based on a set of code template files.
 *
 * A generator class typically needs to implement the following methods:
 *
 * - [[getName()]]: returns the name of the generator
 * - [[getDescription()]]: returns the detailed description of the generator
 * - [[generate()]]: generates the code based on the current user input and the specified code template files.
 *   This is the place where main code generation code resides.
 *
 * @property string $description The detailed description of the generator. This property is read-only.
 * @property string $stickyDataFile The file path that stores the sticky attribute values. This property is
 * read-only.
 * @property string $templatePath The root path of the template files that are currently being used. This
 * property is read-only.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
abstract class Generator extends Model
{
    /**
     * @var array a list of available code templates. The array keys are the template names,
     * and the array values are the corresponding template paths or path aliases.
     */
    public $templates = [];
    /**
     * @var string the name of the code template that the user has selected.
     * The value of this property is internally managed by this class.
     */
    public $template = 'default';
    /**
     * @var bool whether the strings will be generated using `ActiveGenerator::t()` or normal strings.
     */
    public $enableI18N = false;
    /**
     * @var string the message category used by `ActiveGenerator::t()` when `$enableI18N` is `true`.
     * Defaults to `app`.
     */
    public $messageCategory = 'app';


    /**
     * @return string name of the code generator
     */
    abstract public function getName();

    /**
     * Generates the code based on the current user input and the specified code template files.
     * This is the main method that child classes should implement.
     * Please refer to [[\ActiveGenerator\gii\generators\controller\Generator::generate()]] as an example
     * on how to implement this method.
     * @param $path
     * @return CodeFile[] a list of code files to be created.
     */
    abstract public function generate();

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!isset($this->templates['default'])) {
            $this->templates['default'] = $this->defaultTemplate();
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'enableI18N' => 'Enable I18N',
            'messageCategory' => 'Message Category',
        ];
    }

    /**
     * Returns a list of code template files that are required.
     * Derived classes usually should override this method if they require the existence of
     * certain template files.
     * @return array list of code template files that are required. They should be file paths
     * relative to [[templatePath]].
     */
    public function requiredTemplates()
    {
        return [];
    }

    /**
     * Returns the list of sticky attributes.
     * A sticky attribute will remember its value and will initialize the attribute with this value
     * when the generator is restarted.
     * @return array list of sticky attributes
     */
    public function stickyAttributes()
    {
        return ['template', 'enableI18N', 'messageCategory'];
    }

    /**
     * Returns the list of hint messages.
     * The array keys are the attribute names, and the array values are the corresponding hint messages.
     * Hint messages will be displayed to end users when they are filling the form for the generator.
     * @return array the list of hint messages
     */
    public function hints()
    {
        return [
            'enableI18N' => 'This indicates whether the generator should generate strings using <code>ActiveRecord::t()</code> method.
                Set this to <code>true</code> if you are planning to make your application translatable.',
            'messageCategory' => 'This is the category used by <code>ActiveRecord::t()</code> in case you enable I18N.',
        ];
    }

    /**
     * Returns the list of auto complete values.
     * The array keys are the attribute names, and the array values are the corresponding auto complete values.
     * Auto complete values can also be callable typed in order one want to make postponed data generation.
     * @return array the list of auto complete values
     */
    public function autoCompleteData()
    {
        return [];
    }

    /**
     * Returns the message to be displayed when the newly generated code is saved successfully.
     * Child classes may override this method to customize the message.
     * @return string the message to be displayed when the newly generated code is saved successfully.
     */
    public function successMessage()
    {
        return 'The code has been generated successfully.';
    }

    /**
     * Returns the view file for the input form of the generator.
     * The default implementation will return the "form.php" file under the directory
     * that contains the generator class file.
     * @return string the view file for the input form of the generator.
     */
    public function formView()
    {
        $class = new ReflectionClass($this);

        return dirname($class->getFileName()) . '/form.php';
    }

    /**
     * Returns the root path to the default code template files.
     * The default implementation will return the "templates" subdirectory of the
     * directory containing the generator class file.
     * @return string the root path to the default code template files.
     */
    public function defaultTemplate()
    {
        $class = new ReflectionClass($this);

        return dirname($class->getFileName()) . '/default';
    }

    /**
     * @return string the detailed description of the generator.
     */
    public function getDescription()
    {
        return '';
    }



    /**
     * @return string the root path of the template files that are currently being used.
     */
    public function getTemplatePath()
    {
        if (isset($this->templates[$this->template])) {
            return $this->templates[$this->template];
        }
    }

    /**
     * Generates code using the specified code template and parameters.
     * Note that the code template will be used as a PHP file.
     * @param string $template the code template file. This must be specified as a file path
     * relative to [[templatePath]].
     * @param array $params list of parameters to be passed to the template file.
     * @return string the generated code
     */
    public function render($template, $params = [])
    {
        $params['generator'] = $this;
        ob_start();
        extract($params);
        include($this->getTemplatePath().'/'.$template);
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }



    /**
     * @param string $value the attribute to be validated
     * @return bool whether the value is a reserved PHP keyword.
     */
    public function isReservedKeyword($value)
    {
        static $keywords = [
            '__class__',
            '__dir__',
            '__file__',
            '__function__',
            '__line__',
            '__method__',
            '__namespace__',
            '__trait__',
            'abstract',
            'and',
            'array',
            'as',
            'break',
            'case',
            'catch',
            'callable',
            'cfunction',
            'class',
            'clone',
            'const',
            'continue',
            'declare',
            'default',
            'die',
            'do',
            'echo',
            'else',
            'elseif',
            'empty',
            'enddeclare',
            'endfor',
            'endforeach',
            'endif',
            'endswitch',
            'endwhile',
            'eval',
            'exception',
            'exit',
            'extends',
            'final',
            'finally',
            'for',
            'foreach',
            'function',
            'global',
            'goto',
            'if',
            'implements',
            'include',
            'include_once',
            'instanceof',
            'insteadof',
            'interface',
            'isset',
            'list',
            'namespace',
            'new',
            'old_function',
            'or',
            'parent',
            'php_user_filter',
            'print',
            'private',
            'protected',
            'public',
            'require',
            'require_once',
            'return',
            'static',
            'switch',
            'this',
            'throw',
            'trait',
            'try',
            'unset',
            'use',
            'var',
            'while',
            'xor',
        ];

        return in_array(strtolower($value), $keywords, true);
    }

    /**
     * Generates a string depending on enableI18N property
     *
     * @param string $string the text be generated
     * @param array $placeholders the placeholders to use by `ActiveRecord::t()`
     * @return string
     */
    public function generateString($string = '', $placeholders = [])
    {
        $string = addslashes($string);
        if ($this->enableI18N) {
            // If there are placeholders, use them
            if (!empty($placeholders)) {
                $ph = ', ' . \var_export($placeholders, 1);
            } else {
                $ph = '';
            }
            $str = "ActiveRecord::t('" . $this->messageCategory . "', '" . $string . "'" . $ph . ")";
        } else {
            // No I18N, replace placeholders by real words, if any
            if (!empty($placeholders)) {
                $phKeys = array_map(function($word) {
                    return '{' . $word . '}';
                }, array_keys($placeholders));
                $phValues = array_values($placeholders);
                $str = "'" . str_replace($phKeys, $phValues, $string) . "'";
            } else {
                // No placeholders, just the given string
                $str = "'" . $string . "'";
            }
        }
        return $str;
    }
}
